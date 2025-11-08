<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::all();
        return view('guests', compact('guests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:guests,email',
            'phone' => 'nullable|string',
        ]);
        Guest::create($data);
        return redirect()->back()->with('success', 'Guest created!');
    }
}