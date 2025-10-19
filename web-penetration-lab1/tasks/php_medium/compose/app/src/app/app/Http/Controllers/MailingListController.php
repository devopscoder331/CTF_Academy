<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MailingList;

class MailingListController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'email' => 'required|email|unique:mailing_list,email',
        ]);

        // Create a new record in the mailing_list table
        MailingList::create([
            'email' => $validated['email'],
            'status' => '1'
        ]);

        // Return a response
        return response()->json([
            'message' => 'Email added to the mailing list successfully!',
        ], 201);
    }
}
