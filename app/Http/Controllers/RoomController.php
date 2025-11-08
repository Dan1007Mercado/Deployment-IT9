<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        return view('rooms', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number' => 'required|unique:rooms,number',
            'type' => 'required|string',
            'status' => 'nullable|string',
            'price' => 'required|numeric',
        ]);
        Room::create($data);
        return redirect()->back()->with('success', 'Room created!');
    }
}