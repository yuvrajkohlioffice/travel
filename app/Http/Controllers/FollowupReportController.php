<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class FollowupReportController extends Controller
{
    // Load Blade view
    public function index()
    {
        return view('followup_report.index');
    }

    // Data for DataTables

    public function getReport(Request $request)
    {
        $type = $request->input('type', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $date = Carbon::now();

        // Determine date range
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            switch ($type) {
                case 'yesterday':
                    $start = $date->copy()->subDay()->startOfDay();
                    $end = $date->copy()->subDay()->endOfDay();
                    break;
                case 'yearly':
                    $start = $date->copy()->startOfYear();
                    $end = $date->copy()->endOfYear();
                    break;
                case 'today':
                default:
                    $start = $date->copy()->startOfDay();
                    $end = $date->copy()->endOfDay();
                    break;
            }
        }

        $authUser = auth()->user();

        // Base query with eager loading
        $query = Followup::with('user') // eager load users
            ->select('user_id', 'reason', DB::raw('COUNT(DISTINCT lead_id) as calls_count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('user_id', 'reason');

        // Role-based filtering
        if ($authUser->role_id != 1) {
            $companyUserIds = User::where('company_id', $authUser->company_id)->pluck('id')->toArray();
            $query->whereIn('user_id', $companyUserIds);
        }

        $followups = $query->get();

        // Prepare report per user
        $report = [];
        foreach ($followups as $f) {
            $userId = $f->user_id;
            $userName = $f->user->name ?? 'Unknown'; // uses eager-loaded relation

            if (!isset($report[$userId])) {
                $report[$userId] = [
                    'user' => $userName,
                    'total_calls' => 0,
                    'reasons' => [],
                ];
            }

            $report[$userId]['total_calls'] += $f->calls_count;
            $report[$userId]['reasons'][$f->reason] = $f->calls_count;
        }

        // Format for DataTables
        $data = collect($report)
            ->values()
            ->map(function ($item, $key) {
                return [
                    'DT_RowIndex' => $key + 1,
                    'user' => $item['user'],
                    'total_calls' => $item['total_calls'],
                    'reason_counts' => collect($item['reasons'])->map(fn($count, $reason) => "$reason: $count")->implode(', '),
                ];
            });

        return DataTables::of($data)->make(true);
    }
    public function getLeads(Request $request)
{
    $userId = $request->input('user_id');
    $reason = $request->input('reason'); // optional, to filter by reason
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $query = Followup::with('lead') // eager load leads
        ->where('user_id', $userId);

    if ($reason) {
        $query->where('reason', $reason);
    }

    if ($startDate && $endDate) {
        $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
    }

    $followups = $query->get();

    $data = $followups->map(function ($f, $key) {
        return [
            'DT_RowIndex' => $key + 1,
            'lead_name' => $f->lead->name ?? 'Unknown',
            'lead_contact' => $f->lead->contact ?? '',
            'reason' => $f->reason,
            'remark' => $f->remark,
            'next_followup' => $f->next_followup_date?->format('Y-m-d'),
            'created_at' => $f->created_at->format('Y-m-d H:i'),
        ];
    });

    return DataTables::of($data)->make(true);
}

}
