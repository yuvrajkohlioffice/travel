<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Invoice;
use App\Models\Followup;
use App\Models\User;
use App\Models\Package;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------------------------
        // 1. HANDLE AJAX DATATABLES
        // -------------------------------------------
        if ($request->ajax()) {
            
            // A. Followups Table (Scoped by Permissions)
            if ($request->datatable == 'followups') {
                $followups = $this->getFollowupQuery() // <--- Uses Private Function
                    ->with('lead', 'user')
                    ->when($request->month, fn($q) => $q->whereMonth('next_followup_date', $request->month))
                    ->when($request->year, fn($q) => $q->whereYear('next_followup_date', $request->year))
                    ->when($request->from, fn($q) => $q->whereDate('next_followup_date', '>=', $request->from))
                    ->when($request->to, fn($q) => $q->whereDate('next_followup_date', '<=', $request->to))
                    ->orderBy('next_followup_date', 'asc');

                return DataTables::of($followups)
                    ->addColumn('lead_name', fn($f) => $f->lead->name ?? '-')
                    ->addColumn('assigned', fn($f) => $f->user->name ?? '-')
                    ->addColumn('next_followup', fn($f) => Carbon::parse($f->next_followup_date)->format('d M Y'))
                    ->addColumn('remark', fn($f) => $f->remark ?? '-')
                    ->addColumn('actions', function ($f) {
                        return '
                            <button type="button" class="actionBtn px-2 py-1 bg-gray-200 rounded" data-lead="' . ($f->lead->id ?? 0) . '">Call</button>
                            <button type="button" class="px-2 py-1 bg-indigo-600 text-white rounded" onclick="openQuickModal(' . ($f->lead->id ?? 0) . ')">Note</button>
                        ';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }

            // B. User Performance Table (Scoped by Company)
            if ($request->datatable == 'users') {
                // Get users query based on company/admin scope
                $user = Auth::user();
                $usersQuery = User::query();

                // If not Admin, only show users from same company
                if ($user->role_id != 1 && $user->company_id) {
                    $usersQuery->where('company_id', $user->company_id);
                }

                // We need to count leads created by these users
                // But we must ensure the *current user* has permission to see those leads counts? 
                // Usually, performance tables show the User's name and their total leads.
                
                $usersWithStats = $usersQuery->withCount('leads'); 

                return DataTables::of($usersWithStats)
                    ->addColumn('user', fn($u) => $u->name)
                    ->addColumn('leads_created', fn($u) => $u->leads_count)
                    ->make(true);
            }
        }

        // -------------------------------------------
        // 2. PREPARE BASE QUERIES (Scoped)
        // -------------------------------------------
        $leadsQuery    = $this->getLeadQuery();
        $invoicesQuery = $this->getInvoiceQuery();
        
        // Revenue comes from Payments, but we only want payments for Invoices the user can see.
        $paymentsQuery = Payment::whereHas('invoice', function ($q) {
            // Apply the same scoping logic as getInvoiceQuery inside the relationship
            $user = Auth::user();
            if ($user->role_id == 1) return; // Admin sees all
            
            // Scope invoices by company or user ownership
            $q->whereHas('lead', function ($leadQ) use ($user) {
                if ($user->company_id) {
                    // Owner/Employee Company Check
                    $leadQ->whereHas('createdBy', fn($u) => $u->where('company_id', $user->company_id));
                    
                    // Specific Employee Check (if not owner)
                    $isOwner = $user->id === optional($user->company)->owner_id;
                    if (!$isOwner) {
                         $leadQ->where(function ($sub) use ($user) {
                            $sub->where('user_id', $user->id)
                                ->orWhereHas('assignedUsers', fn($au) => $au->where('user_id', $user->id));
                        });
                    }
                }
            });
        });

        // -------------------------------------------
        // 3. APPLY DASHBOARD FILTERS
        // -------------------------------------------
        $month = $request->month;
        $year  = $request->year;
        $from  = $request->from;
        $to    = $request->to;
        $status = $request->status;

        $applyDateFilters = function ($q, $column = 'created_at') use ($month, $year, $from, $to) {
            if ($month) $q->whereMonth($column, $month);
            if ($year) $q->whereYear($column, $year);
            if ($from) $q->whereDate($column, '>=', $from);
            if ($to) $q->whereDate($column, '<=', $to);
        };

        // Apply filters to main queries
        $applyDateFilters($leadsQuery);
        $applyDateFilters($invoicesQuery);
        $applyDateFilters($paymentsQuery);

        if ($status) {
            $leadsQuery->where('lead_status', $status);
        }

        // -------------------------------------------
        // 4. CALCULATE STATS
        // -------------------------------------------
        $leadCount    = $leadsQuery->count();
        $invoiceCount = $invoicesQuery->count();
        $packageCount = Package::count(); // Packages are usually global, or add scope if needed
        $userCount    = User::when(Auth::user()->role_id != 1 && Auth::user()->company_id, function($q) {
                            return $q->where('company_id', Auth::user()->company_id);
                        })->count();

        // Status Counts (Must clone query to remove date filters if you want "All Time" status, 
        // OR keep filters if you want "Status counts for this period". 
        // Usually dashboard status circles show TOTAL, but filtered is better for UX.
        // We will use the filtered query but we need to group by status.)
        
        // Note: We clone because ->count() executes the query, but we need raw select for grouping
        $leadsForStatus = clone $leadsQuery;
        // Reset the specific select to avoid conflicts if any
        $leadStatusCounts = $leadsForStatus->selectRaw('lead_status, COUNT(*) as total')
            ->groupBy('lead_status')
            ->pluck('total', 'lead_status');

        // Today/Week Stats (These need fresh scoped queries, ignoring the $from/$to filters)
        $todayLeads = $this->getLeadQuery()->whereDate('created_at', today())->count();
        $weekLeads  = $this->getLeadQuery()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        
        $todayInvoices = $this->getInvoiceQuery()->whereDate('created_at', today())->count();
        $weekInvoices  = $this->getInvoiceQuery()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // Revenue (Sum of paid_amount on filtered payments)
        $totalRevenue = $paymentsQuery->whereIn('status', ['partial', 'paid', 'completed'])->sum('paid_amount');

        // -------------------------------------------
        // 5. GRAPH DATA
        // -------------------------------------------
        $startDate = $from ?: now()->subDays(29)->format('Y-m-d');
        $endDate   = $to   ?: now()->format('Y-m-d');

        if ($month && $year) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
            $endDate   = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');
        }

        $period = Carbon::parse($startDate)->daysUntil(Carbon::parse($endDate));
        
        // We prepare separate queries for graph to avoid N+1 inside loop
        // However, for 30 days, simple queries are acceptable, or use SQL grouping.
        // For simplicity and permission accuracy, we iterate the scoped check.
        
        $last30Days = collect($period)->map(function ($date) {
            $d = $date->format('Y-m-d');
            return [
                'date'     => $d,
                'leads'    => $this->getLeadQuery()->whereDate('created_at', $d)->count(),
                // For invoices graph (revenue), we sum payments made on that day
                'invoices' => Payment::whereHas('invoice', fn($q) => $q->whereIn('id', $this->getInvoiceQuery()->select('id')))
                                     ->whereDate('created_at', $d)
                                     ->whereIn('status', ['partial', 'paid', 'completed'])
                                     ->sum('paid_amount'),
            ];
        })->values();

        // -------------------------------------------
        // 6. DEPARTURES & RETURNS (Scoped)
        // -------------------------------------------
        
        // A. Today's Departures
        $todayDepartures = $this->getInvoiceQuery()
            ->with(['lead', 'package'])
            ->whereDate('travel_start_date', today())
            ->get();

        // B. Today's Returns
        $todayReturns = $this->getInvoiceQuery()
            ->select('invoices.*')
            ->join('packages', 'invoices.package_id', '=', 'packages.id')
            ->with(['lead', 'package'])
            ->whereRaw("DATE_ADD(invoices.travel_start_date, INTERVAL packages.package_days DAY) = ?", [today()->format('Y-m-d')])
            ->get();

        return view('dashboard', compact(
            'leadCount', 'invoiceCount', 'packageCount', 'userCount',
            'leadStatusCounts', 'todayLeads', 'weekLeads', 'todayInvoices',
            'weekInvoices', 'totalRevenue', 'last30Days',
            'todayDepartures', 'todayReturns'
        ));
    }

    /**
     * Get Base Query for Leads based on User Role/Permissions
     */
    private function getLeadQuery()
    {
        $user = Auth::user();
        $query = Lead::query();

        // 1. Admin: See Everything
        if ($user->role_id == 1) {
            return $query; // Admin sees deleted? add ->withTrashed() if needed
        }

        // 2. Company Context
        if ($user->company_id) {
            // Check if Owner
            $isOwner = $user->id === optional($user->company)->owner_id;

            $query->where(function ($q) use ($user, $isOwner) {
                if ($isOwner) {
                    // Owner: See all leads created by users in their company
                    $q->whereHas('createdBy', fn($u) => $u->where('company_id', $user->company_id));
                } else {
                    // Employee: See Own Leads OR Assigned Leads
                    $q->where('user_id', $user->id)
                      ->orWhereHas('assignedUsers', fn($au) => $au->where('user_id', $user->id));
                }
            });
        } else {
            // Fallback for users with no company (shouldn't happen in standard flow)
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Get Base Query for Invoices based on Permission
     * Usually permissions cascade: If you can see the Lead, you can see the Invoice.
     */
    private function getInvoiceQuery()
    {
        $user = Auth::user();
        $query = Invoice::query();

        if ($user->role_id == 1) {
            return $query;
        }

        // Filter invoices by ensuring the related LEAD is visible to the user
        return $query->whereHas('lead', function ($q) {
             // Re-use logic from getLeadQuery, but applied inside the relation
             $user = Auth::user();
             if ($user->company_id) {
                $isOwner = $user->id === optional($user->company)->owner_id;
                
                if ($isOwner) {
                    $q->whereHas('createdBy', fn($u) => $u->where('company_id', $user->company_id));
                } else {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('assignedUsers', fn($au) => $au->where('user_id', $user->id));
                }
             }
        });
    }

    /**
     * Get Base Query for Followups based on Permission
     */
    private function getFollowupQuery()
    {
        $user = Auth::user();
        $query = Followup::query();

        if ($user->role_id == 1) {
            return $query;
        }

        // Filter followups by ensuring the related LEAD is visible
        return $query->whereHas('lead', function ($q) {
             $user = Auth::user();
             if ($user->company_id) {
                $isOwner = $user->id === optional($user->company)->owner_id;
                
                if ($isOwner) {
                    $q->whereHas('createdBy', fn($u) => $u->where('company_id', $user->company_id));
                } else {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('assignedUsers', fn($au) => $au->where('user_id', $user->id));
                }
             }
        });
    }
}