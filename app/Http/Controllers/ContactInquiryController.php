<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    public function store(Request $request)
    {
        // Log the data to storage/logs/laravel.log to see exactly what is arriving
        \Log::info('Contact Inquiry Received:', $request->all());

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $inquiry = ContactInquiry::create($validated);

        return response()->json([
            'success' => true,
            'id' => $inquiry->id
        ]);
    }
}