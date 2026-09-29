<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactLead;
use Illuminate\Http\Request;

class ContactLeadController extends Controller
{
    /**
     * Display a listing of all contact leads.
     */
    public function index()
    {
        $leads = ContactLead::latest()->paginate(15);

        $statusCounts = [
            'all'         => ContactLead::count(),
            'new'         => ContactLead::where('status', 'new')->count(),
            'contacted'   => ContactLead::where('status', 'contacted')->count(),
            'in_progress' => ContactLead::where('status', 'in_progress')->count(),
            'converted'   => ContactLead::where('status', 'converted')->count(),
            'closed'      => ContactLead::where('status', 'closed')->count(),
        ];

        return view('admin.contact_leads.index', compact('leads', 'statusCounts'));
    }

    /**
     * Display a single contact lead's details.
     */
    public function show(ContactLead $contact_lead)
    {
        return view('admin.contact_leads.show', ['contactLead' => $contact_lead]);
    }

    /**
     * Update the status and/or notes of a contact lead.
     */
    public function update(Request $request, ContactLead $contact_lead)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,in_progress,converted,closed',
            'notes'  => 'nullable|string|max:2000',
        ]);

        $contact_lead->update([
            'status' => $request->status,
            'notes'  => $request->notes,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Soft delete a contact lead.
     */
    public function destroy(ContactLead $contact_lead)
    {
        $contact_lead->delete();
        return response()->json(['success' => true]);
    }
}
