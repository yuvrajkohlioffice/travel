<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use Illuminate\Http\Request;

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
}
