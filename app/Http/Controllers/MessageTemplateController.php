<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use App\Models\Package;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MessageTemplateController extends Controller
{
    /**
     * INDEX – Datatable list
     */
   public function index(Request $request)
{
    if ($request->ajax()) {
        $data = MessageTemplate::with('package')->select('message_templates.*');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('package_name', fn($row) => $row->package->package_name ?? '-')
            
            // WhatsApp status column
            ->addColumn('whatsapp_status', function ($row) {
                return $row->whatsapp_text || $row->whatsapp_media
                    ? '<i class="fas fa-check-circle text-green-500"></i>'
                    : '<i class="fas fa-times-circle text-red-500"></i>';
            })
            
            // Email status column
            ->addColumn('email_status', function ($row) {
                return $row->email_subject || $row->email_body || $row->email_media
                    ? '<i class="fas fa-check-circle text-green-500"></i>'
                    : '<i class="fas fa-times-circle text-red-500"></i>';
            })
            
            ->addColumn('action', function ($row) {
                return '
                    <a href="'.route('templates.edit', $row->id).'" class="btn btn-sm btn-yellow-500 px-2 py-1 rounded bg-yellow-600 text-white mr-1">
                        <i class="fas fa-edit"></i>
                        Edit
                    </a>
                    <button class="btn btn-sm btn-red-600 px-2 py-1 rounded bg-red-700 text-white deleteBtn" data-id="'.$row->id.'">
                        <i class="fas fa-trash-alt"></i>
                        Delete
                    </button>
                ';
            })
            ->rawColumns(['whatsapp_status', 'email_status', 'action'])
            ->make(true);
    }

    return view('templates.index');
}


    /**
     * CREATE
     */
    public function create()
    {
        $packages = Package::all();
        return view('templates.create', compact('packages'));
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'whatsapp_text' => 'nullable|string',
            'whatsapp_media' => 'nullable|file|mimes:jpg,png,jpeg,pdf,docx|max:2048',
            'email_subject' => 'nullable|string',
            'email_body' => 'nullable|string',
            'email_media' => 'nullable|file|mimes:jpg,png,jpeg,pdf,docx|max:2048',
        ]);

        $data = $request->except(['whatsapp_media', 'email_media']);

        // Upload WhatsApp File
        if ($request->hasFile('whatsapp_media')) {
            $data['whatsapp_media'] = $request->file('whatsapp_media')
                ->store('whatsapp_media', 'public');
        }

        // Upload Email File
        if ($request->hasFile('email_media')) {
            $data['email_media'] = $request->file('email_media')
                ->store('email_media', 'public');
        }

        MessageTemplate::create($data);

        return redirect()->route('templates.index')
            ->with('success', 'Template created successfully!');
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $template = MessageTemplate::findOrFail($id);
        $packages = Package::all();
        return view('templates.edit', compact('template', 'packages'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $template = MessageTemplate::findOrFail($id);

        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'whatsapp_text' => 'nullable|string',
            'whatsapp_media' => 'nullable|file|mimes:jpg,png,jpeg,pdf,docx|max:2048',
            'email_subject' => 'nullable|string',
            'email_body' => 'nullable|string',
            'email_media' => 'nullable|file|mimes:jpg,png,jpeg,pdf,docx|max:2048',
        ]);

        $data = $request->except(['whatsapp_media', 'email_media']);

        // Replace WhatsApp File
        if ($request->hasFile('whatsapp_media')) {
            if ($template->whatsapp_media && file_exists(storage_path('app/public/'.$template->whatsapp_media))) {
                unlink(storage_path('app/public/'.$template->whatsapp_media));
            }
            $data['whatsapp_media'] = $request->file('whatsapp_media')
                ->store('whatsapp_media', 'public');
        }

        // Replace Email File
        if ($request->hasFile('email_media')) {
            if ($template->email_media && file_exists(storage_path('app/public/'.$template->email_media))) {
                unlink(storage_path('app/public/'.$template->email_media));
            }
            $data['email_media'] = $request->file('email_media')
                ->store('email_media', 'public');
        }

        $template->update($data);

        return redirect()->route('templates.index')
            ->with('success', 'Template updated successfully!');
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        $template = MessageTemplate::findOrFail($id);

        if ($template->whatsapp_media && file_exists(storage_path('app/public/'.$template->whatsapp_media))) {
            unlink(storage_path('app/public/'.$template->whatsapp_media));
        }

        if ($template->email_media && file_exists(storage_path('app/public/'.$template->email_media))) {
            unlink(storage_path('app/public/'.$template->email_media));
        }

        $template->delete();

        return response()->json(['status' => true, 'message' => 'Deleted successfully']);
    }
}
