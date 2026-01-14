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
use Illuminate\Support\Facades\DB; // Added for raw queries

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------------------------
        // 1. HANDLE AJAX DATATABLES (Cleaned & Consolidated)
        // -------------------------------------------
        if ($request->ajax()) {
            // A. Followups Table
            if ($request->datatable == 'followups') {
                $followups = Followup::with('lead', 'user')
                    ->when($request->month, fn($q) => $q->whereMonth('next_followup_date', $request->month))
                    ->when($request->year, fn($q) => $q->whereYear('next_followup_date', $request->year))
                    ->when($request->from, fn($q) => $q->whereDate('next_followup_date', '>=', $request->from))
                    ->when($request->to, fn($q) => $q->whereDate('next_followup_date', '<=', $request->to))
                    // Default to today or future if no specific filter provided, or adjust as needed
                    ->orderBy('next_followup_date');

                return DataTables::of($followups)
                    ->addColumn('lead_name', fn($f) => $f->lead->name ?? '-')
                    ->addColumn('assigned', fn($f) => $f->user->name ?? '-')
                    ->addColumn('next_followup', fn($f) => Carbon::parse($f->next_followup_date)->format('d M Y'))
                    ->addColumn('remark', fn($f) => $f->remark ?? '-')
                    ->addColumn('actions', function ($f) {
                        $callBtn = '<button type="button" class="actionBtn px-2 py-1 bg-gray-200 rounded" data-lead="' . ($f->lead->id ?? 0) . '">Call</button>';
                        $noteBtn = '<button type="button" class="px-2 py-1 bg-indigo-600 text-white rounded" onclick="openQuickModal(' . ($f->lead->id ?? 0) . ')">Note</button>';
                        return $callBtn . ' ' . $noteBtn;
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }

            // B. User Performance Table
            if ($request->datatable == 'users') {
                $createdLeadsByUser = Lead::with('createdBy')
                    ->select('user_id', DB::raw('COUNT(*) as total'))
                    ->groupBy('user_id');

                return DataTables::of($createdLeadsByUser)
                    ->addColumn('user', fn($u) => $u->createdBy->name ?? 'Unknown')
                    ->addColumn('leads_created', fn($u) => $u->total)
                    ->make(true);
            }
        }

        // -------------------------------------------
        // 2. FILTERS
        // -------------------------------------------
        $month = $request->month;
        $year = $request->year;
        $from = $request->from;
        $to = $request->to;
        $status = $request->status;

        // -------------------------------------------
        // 3. MAIN QUERIES (Filtered)
        // -------------------------------------------
        $leadsQuery = Lead::query();
        $invoicesQuery = Invoice::query();
        $paymentsQuery = Payment::query();

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
        // 4. COUNTS & STATS
        // -------------------------------------------
        $leadCount = $leadsQuery->count();
        $invoiceCount = $invoicesQuery->count();
        $packageCount = Package::count();
        $userCount = User::count();

        $leadStatusCounts = Lead::selectRaw('lead_status, COUNT(*) as total')
            ->groupBy('lead_status')
            ->pluck('total', 'lead_status');

        $todayLeads = Lead::whereDate('created_at', today())->count();
        $weekLeads = Lead::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        
        $todayInvoices = Invoice::whereDate('created_at', today())->count();
        $weekInvoices = Invoice::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $totalRevenue = $paymentsQuery->whereIn('status', ['partial', 'paid'])->sum('paid_amount');

        // -------------------------------------------
        // 5. GRAPH DATA
        // -------------------------------------------
        $startDate = $from ?: now()->subDays(29)->format('Y-m-d');
        $endDate = $to ?: now()->format('Y-m-d');

        if ($month && $year) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');
        }

        $period = Carbon::parse($startDate)->daysUntil(Carbon::parse($endDate));
        $last30Days = collect($period)->map(function ($date) {
            $d = $date->format('Y-m-d');
            return [
                'date' => $d,
                'leads' => Lead::whereDate('created_at', $d)->count(),
                'invoices' => Payment::whereDate('created_at', $d)->whereIn('status', ['partial', 'completed'])->sum('paid_amount'),
            ];
        })->values();

        // ===========================================
        // NEW FEATURE: TRAVEL DEPARTURES & RETURNS
        // ===========================================

        // A. Today's Departures (Travel Start Date is Today)
        $todayDepartures = Invoice::with(['lead', 'package'])
            ->whereDate('travel_start_date', today())
            ->get();

        // B. Today's Returns 
        // Logic: travel_start_date + package_days = Today
        $todayReturns = Invoice::select('invoices.*')
            ->join('packages', 'invoices.package_id', '=', 'packages.id')
            ->with(['lead', 'package'])
            ->whereRaw("DATE_ADD(invoices.travel_start_date, INTERVAL packages.package_days DAY) = ?", [today()->format('Y-m-d')])
            ->get();

        return view('dashboard', compact(
            'leadCount', 
            'invoiceCount', 
            'packageCount', 
            'userCount', 
            'leadStatusCounts', 
            'todayLeads', 
            'weekLeads', 
            'todayInvoices', 
            'weekInvoices', 
            'totalRevenue', 
            'last30Days',
            'todayDepartures', // New Variable
            'todayReturns'     // New Variable
        ));
    }
}