<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Invoice;
use App\Models\Followup;
use App\Models\User;
use App\Models\Package;
use App\Models\PackageItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
public function index()
{
    // -------------------------------------------
    // FILTERS
    // -------------------------------------------
    $month = request()->month;
    $year  = request()->year;
    $from  = request()->from;
    $to    = request()->to;

    // Default → Last 30 days
    $startDate = $from ?: now()->subDays(29)->format('Y-m-d');
    $endDate   = $to   ?: now()->format('Y-m-d');

    if ($month && $year) {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate   = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');
    }

    // -------------------------------------------
    // BASIC COUNTS
    // -------------------------------------------
    $leadCount         = Lead::count();
    $invoiceCount      = Invoice::count();
    $packageCount      = Package::count();
    $packageItemCount  = PackageItem::count();
    $userCount         = User::count();

    // -------------------------------------------
    // LEAD STATUS
    // -------------------------------------------
    $leadStatusCounts = Lead::selectRaw('lead_status, COUNT(*) as total')
        ->groupBy('lead_status')
        ->pluck('total', 'lead_status');

    // -------------------------------------------
    // TODAY / WEEK / REVENUE
    // -------------------------------------------
    $todayLeads     = Lead::whereDate('created_at', today())->count();
    $weekLeads      = Lead::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

    $todayInvoices  = Invoice::whereDate('created_at', today())->count();
    $weekInvoices   = Invoice::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

    $totalRevenue   = Invoice::sum('final_price');
    $thisMonthRevenue = Invoice::whereMonth('created_at', now()->month)->sum('final_price');

    // -------------------------------------------
    // FOLLOWUPS
    // -------------------------------------------
    $upcomingFollowups = Followup::with('lead', 'user')
        ->whereDate('next_followup_date', '>=', today())
        ->orderBy('next_followup_date')
        ->limit(10)
        ->get();

    // -------------------------------------------
    // USER CREATED LEADS
    // -------------------------------------------
    $createdLeadsByUser = Lead::selectRaw('user_id, COUNT(*) as total')
        ->groupBy('user_id')
        ->with('createdBy')
        ->get();

    // -------------------------------------------
    // GRAPH DATA (FILTERED)
    // -------------------------------------------
    $period = Carbon::parse($startDate)->daysUntil(Carbon::parse($endDate));

    $last30Days = collect($period)->map(function ($date) {
        $d = $date->format('Y-m-d');

        return [
            'date'     => $d,
            'leads'    => Lead::whereDate('created_at', $d)->count(),
            'invoices' => Invoice::whereDate('created_at', $d)->count(),
        ];
    })->values();

    // -------------------------------------------
    return view('dashboard', compact(
        'leadCount',
        'invoiceCount',
        'packageCount',
        'userCount',
        'packageItemCount',
        'leadStatusCounts',
        'todayLeads',
        'weekLeads',
        'todayInvoices',
        'weekInvoices',
        'totalRevenue',
        'thisMonthRevenue',
        'upcomingFollowups',
        'createdLeadsByUser',
        'last30Days'
    ));
}

}
