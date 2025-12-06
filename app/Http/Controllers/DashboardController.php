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
        //-------------------------------------------
        // BASIC COUNTS
        //-------------------------------------------
        $leadCount         = Lead::count();
        $invoiceCount      = Invoice::count();
        $packageCount      = Package::count();
        $packageItemCount  = PackageItem::count();
        $userCount         = User::count();

        //-------------------------------------------
        // LEAD STATUS COUNT (OPEN, IN PROGRESS, CLOSED etc)
        //-------------------------------------------
        $leadStatusCounts = Lead::selectRaw('lead_status, COUNT(*) as total')
            ->groupBy('lead_status')
            ->pluck('total', 'lead_status');

        //-------------------------------------------
        // TODAY / WEEK STATS
        //-------------------------------------------
        $todayLeads = Lead::whereDate('created_at', Carbon::today())->count();
        $weekLeads  = Lead::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

        $todayInvoices = Invoice::whereDate('created_at', Carbon::today())->count();
        $weekInvoices  = Invoice::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

        //-------------------------------------------
        // REVENUE SUMMARY
        //-------------------------------------------
        $totalRevenue = Invoice::sum('final_price');
        $thisMonthRevenue = Invoice::whereMonth('created_at', Carbon::now()->month)->sum('final_price');

        //-------------------------------------------
        // UPCOMING FOLLOWUPS
        //-------------------------------------------
        $upcomingFollowups = Followup::with('lead', 'user')
            ->whereDate('next_followup_date', '>=', Carbon::today())
            ->orderBy('next_followup_date', 'asc')
            ->limit(10)
            ->get();

        //-------------------------------------------
        // TRACKING: views, assigned, created
        //-------------------------------------------
        $leadViewsCount = \DB::table('lead_views')->count(); // works with your LeadView model
        $assignedLeadsCount = \DB::table('users')->count();

        $createdLeadsByUser = Lead::selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->with('createdBy')
            ->get();

        //-------------------------------------------
        // LAST 30 DAYS GRAPH DATA
        //-------------------------------------------
        $last30Days = collect(range(0, 29))->map(function ($i) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');

            return [
                'date' => $date,
                'leads' => Lead::whereDate('created_at', $date)->count(),
                'invoices' => Invoice::whereDate('created_at', $date)->count(),
            ];
        })->reverse()->values();

        //-------------------------------------------
        // RETURN VIEW
        //-------------------------------------------
        return view('dashboard', compact(
            'leadCount',
            'invoiceCount',
            'packageCount',
            'packageItemCount',
            'userCount',
            'leadStatusCounts',
            'todayLeads',
            'weekLeads',
            'todayInvoices',
            'weekInvoices',
            'totalRevenue',
            'thisMonthRevenue',
            'upcomingFollowups',
            'leadViewsCount',
            'assignedLeadsCount',
            'createdLeadsByUser',
            'last30Days'
        ));
    }
}
