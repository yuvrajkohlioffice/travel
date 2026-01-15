<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use App\Models\FollowupReason; // Import this
use App\Models\Lead;
use App\Models\LeadView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import for Transactions
use Carbon\Carbon;

class FollowupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'lead_id'            => 'required|exists:leads,id',
            'reason'             => 'required', // Can be ID or String
            'remark'             => 'nullable|string',
            'next_followup_date' => 'nullable|date',
            'next_followup_time' => 'nullable',
        ]);

        return DB::transaction(function () use ($request) {
            
            // 1. Identify the FollowupReason Model
            // We check if the input is a numeric ID (Best Practice) or a String Name (Legacy Support)
            $reasonModel = null;
            
            if (is_numeric($request->reason)) {
                $reasonModel = FollowupReason::with('leadStatus')->find($request->reason);
            } else {
                $reasonModel = FollowupReason::with('leadStatus')->where('name', $request->reason)->first();
            }

            // 2. Determine values for storage
            // If model found, use its strict name. If not, use user input.
            $reasonName = $reasonModel ? $reasonModel->name : $request->reason;

            // 3. Create the Followup
            Followup::create([
                'lead_id'            => $request->lead_id,
                'user_id'            => auth()->id(),
                'reason'             => $reasonName, 
                'remark'             => $request->remark,
                'next_followup_date' => $request->next_followup_date,
                'next_followup_time' => $request->next_followup_time,
                'last_followup_date' => now(),
            ]);

            // 4. Automatically Update Lead Status
            // If the selected Reason is linked to a Lead Status, update the Lead
            if ($reasonModel && $reasonModel->leadStatus) {
                
                // Fetch the status name/id from the relationship defined in FollowupReason
                $newStatusName = $reasonModel->leadStatus->name; 
                $newStatusId   = $reasonModel->lead_status_id;

                
                Lead::where('id', $request->lead_id)->update([
                    'status'      => $newStatusName,
                   
                ]);
            }

            return back()->with('success', 'Followup Added and Lead Status Updated Successfully');
        });
    }

    public function getLeadDetails(Lead $lead)
    {
        $user = auth()->user();
        
        // Log the view
        LeadView::create([
            'lead_id'   => $lead->id,
            'user_id'   => $user->id,
            'viewed_at' => Carbon::now('Asia/Kolkata'),
        ]);

        // Optimized Followups Query
        $followups = $lead->followups()
            ->when($user->role_id != 1, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('user:id,name') // Eager load user to avoid N+1 query problem
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($f) {
                return [
                    'created_at'         => Carbon::parse($f->created_at)->format('d-M-Y h:i A'),
                    'reason'             => $f->reason,
                    'remark'             => $f->remark,
                    'next_followup_date' => $f->next_followup_date ? Carbon::parse($f->next_followup_date)->format('d-M-Y h:i A') : null,
                    'user_name'          => $f->user->name ?? 'System',
                ];
            });

        return response()->json([
            'phone' => [
                'phone_number' => $lead->phone_number,
                'phone_code'   => $lead->phone_code,
                'full_number'  => '+' . $lead->phone_code . ' ' . $lead->phone_number,
            ],
            'followups' => $followups,
        ]);
    }
}