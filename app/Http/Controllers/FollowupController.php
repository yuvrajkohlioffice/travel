<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadUser;
use Carbon\Carbon;

class FollowupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'reason' => 'nullable|string',
            'remark' => 'nullable|string',
            'next_followup_date' => 'nullable|date',
            'next_followup_time' => 'nullable',
        ]);

        Followup::create([
            'lead_id' => $request->lead_id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'remark' => $request->remark,
            'next_followup_date' => $request->next_followup_date,
            'next_followup_time' => $request->next_followup_time,
            'last_followup_date' => now(),
        ]);

        return back()->with('success', 'Followup Added Successfully');
    }
   public function getLeadDetails(Lead $lead)
{
    $user = auth()->user();

    // Followups with filtering + formatting
    $followups = $lead
        ->followups()
        ->when($user->role_id != 1, function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with('user:id,name')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($f) {
            return [
                'created_at' => Carbon::parse($f->created_at)->format('d-M-Y h:i A'),
                'reason' => $f->reason,
                'remark' => $f->remark,
                'next_followup_date' => $f->next_followup_date
                    ? Carbon::parse($f->next_followup_date)->format('d-M-Y h:i A')
                    : null,
                'user_name' => $f->user->name ?? null,
            ];
        });

    // Return merged data
    return response()->json([
        'phone' => [
            'phone_number' => $lead->phone_number,
            'phone_code' => $lead->phone_code,
            'full_number' => '+' . $lead->phone_code . ' ' . $lead->phone_number,
        ],
        'followups' => $followups,
    ]);
}

}
