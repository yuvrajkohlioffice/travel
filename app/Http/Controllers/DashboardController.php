<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Invoice;
use App\Models\Followup;
use App\Models\User;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------------------------
        // FILTERS
        // -------------------------------------------
        $month = $request->month;
        $year = $request->year;
        $from = $request->from;
        $to = $request->to;
        $status = $request->status;

        // -------------------------------------------
        // BASIC QUERIES
        // -------------------------------------------
        $leadsQuery = Lead::query();
        $invoicesQuery = Invoice::query();
        $paymentsQuery = Payment::query();

        // Apply filters
        if ($month) {
            $leadsQuery->whereMonth('created_at', $month);
            $invoicesQuery->whereMonth('created_at', $month);
            $paymentsQuery->whereMonth('created_at', $month);
        }

        if ($year) {
            $leadsQuery->whereYear('created_at', $year);
            $invoicesQuery->whereYear('created_at', $year);
            $paymentsQuery->whereYear('created_at', $year);
        }

        if ($from) {
            $leadsQuery->whereDate('created_at', '>=', $from);
            $invoicesQuery->whereDate('created_at', '>=', $from);
            $paymentsQuery->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $leadsQuery->whereDate('created_at', '<=', $to);
            $invoicesQuery->whereDate('created_at', '<=', $to);
            $paymentsQuery->whereDate('created_at', '<=', $to);
        }

        if ($status) {
            $leadsQuery->where('lead_status', $status);
        }

        // -------------------------------------------
        // BASIC COUNTS
        // -------------------------------------------
        $leadCount = $leadsQuery->count();
        $invoiceCount = $invoicesQuery->count();
        $packageCount = Package::count();
        $userCount = User::count();

        // -------------------------------------------
        // LEAD STATUS COUNTS
        // -------------------------------------------
        $leadStatusCounts = Lead::selectRaw('lead_status, COUNT(*) as total')->groupBy('lead_status')->pluck('total', 'lead_status');

        // -------------------------------------------
        // TODAY / WEEK
        // -------------------------------------------
        $todayLeads = (clone $leadsQuery)->whereDate('created_at', today())->count();
        $weekLeads = (clone $leadsQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $todayInvoices = (clone $invoicesQuery)->whereDate('created_at', today())->count();
        $weekInvoices = (clone $invoicesQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // -------------------------------------------
        // TOTAL REVENUE
        // -------------------------------------------
        $totalRevenue = $paymentsQuery->whereIn('status', ['partial', 'completed'])->sum('paid_amount');

        // -------------------------------------------
        // UPCOMING FOLLOWUPS (DATATABLE READY)
        // -------------------------------------------
        $upcomingFollowups = Followup::with('lead', 'user')->whereDate('next_followup_date', '>=', today())->orderBy('next_followup_date');

        if ($request->ajax() && $request->datatable == 'followups') {
            return DataTables::of($upcomingFollowups)
                ->addColumn('lead_name', fn($f) => $f->lead->name)
                ->addColumn('assigned', fn($f) => $f->user->name ?? '-')
                ->addColumn('next_followup', fn($f) => $f->next_followup_date)
                ->addColumn('remark', fn($f) => $f->remark ?? '-')
                ->addColumn('actions', function ($f) {
                    $callBtn = '<button type="button" class="actionBtn px-2 py-1 bg-gray-200 rounded" data-lead="' . $f->lead->id . '">Call</button>';
                    $noteBtn = '<button type="button" class="px-2 py-1 bg-indigo-600 text-white rounded" onclick="openQuickModal(' . $f->lead->id . ', \'' . addslashes($f->lead->name) . '\')">Note</button>';
                    return $callBtn . ' ' . $noteBtn;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        // -------------------------------------------
        // USER PERFORMANCE (DATATABLE READY)
        // -------------------------------------------
        $createdLeadsByUser = Lead::selectRaw('user_id, COUNT(*) as total')->groupBy('user_id')->get();

        if ($request->ajax() && $request->datatable == 'users') {
            return DataTables::of($createdLeadsByUser)
                ->addColumn('user', fn($u) => optional(User::find($u->user_id))->name ?? 'Unknown')
                ->addColumn('leads_created', fn($u) => $u->total) // now total exists
                ->make(true);
        }
        if ($request->ajax() && $request->datatable == 'users') {
            $createdLeadsByUser = Lead::with('createdBy')->select('user_id')->selectRaw('COUNT(*) as total')->groupBy('user_id');

            return DataTables::of($createdLeadsByUser)->addColumn('user', fn($u) => $u->createdBy->name ?? 'Unknown')->addColumn('leads_created', fn($u) => $u->total)->make(true);
        }
        if ($request->ajax() && $request->datatable == 'followups') {
            $followups = Followup::with('lead', 'user')->when($request->month, fn($q) => $q->whereMonth('next_followup_date', $request->month))->when($request->year, fn($q) => $q->whereYear('next_followup_date', $request->year))->when($request->from, fn($q) => $q->whereDate('next_followup_date', '>=', $request->from))->when($request->to, fn($q) => $q->whereDate('next_followup_date', '<=', $request->to));

            return DataTables::of($followups)
                ->addColumn('lead_name', fn($f) => $f->lead->name ?? '-')
                ->addColumn('assigned', fn($f) => $f->user->name ?? '-')
                ->addColumn('next_followup', fn($f) => $f->next_followup_date)
                ->addColumn('remark', fn($f) => $f->remark ?? '-')
                ->addColumn('actions', function ($f) {
                    return '<button class="actionBtn px-3 py-1 bg-indigo-600 text-white rounded text-xs" data-lead="' . $f->lead_id . '">Call</button>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        // -------------------------------------------
        // GRAPH DATA
        // -------------------------------------------
        $startDate = $from ?: now()->subDays(29)->format('Y-m-d');
        $endDate = $to ?: now()->format('Y-m-d');

        if ($month && $year) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');
        }

        $period = Carbon::parse($startDate)->daysUntil(Carbon::parse($endDate));

        $last30Days = collect($period)
            ->map(function ($date) {
                $d = $date->format('Y-m-d');
                return [
                    'date' => $d,
                    'leads' => Lead::whereDate('created_at', $d)->count(),
                    'invoices' => Payment::whereDate('created_at', $d)
                        ->whereIn('status', ['partial', 'completed'])
                        ->sum('paid_amount'),
                ];
            })
            ->values();

        return view('dashboard', compact('leadCount', 'invoiceCount', 'packageCount', 'userCount', 'leadStatusCounts', 'todayLeads', 'weekLeads', 'todayInvoices', 'weekInvoices', 'totalRevenue', 'upcomingFollowups', 'createdLeadsByUser', 'last30Days'));
    }
}
