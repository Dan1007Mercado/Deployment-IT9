<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $rooms = Room::with('roomType')
            ->when($search, function($query, $search) {
                return $query->where('room_number', 'like', "%{$search}%")
                            ->orWhereHas('roomType', function($q) use ($search) {
                                $q->where('type_name', 'like', "%{$search}%");
                            });
            })
            ->orderBy('room_number')
            ->get();
            
        $roomTypes = RoomType::all();
        
        return view('rooms', compact('rooms', 'roomTypes', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,room_type_id',
            'floor' => 'required|string|max:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $roomData = [
            'room_number' => $request->room_number,
            'room_type_id' => $request->room_type_id,
            'floor' => $request->floor,
            'room_status' => 'available', // Default status
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('room-images', 'public');
            $roomData['image_path'] = $imagePath;
        }

        Room::create($roomData);

        return redirect()->route('rooms', ['search' => $request->get('search')])->with('success', 'Room added successfully!');
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number,' . $room->room_id . ',room_id',
            'room_type_id' => 'required|exists:room_types,room_type_id',
            'floor' => 'required|string|max:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $roomData = [
            'room_number' => $request->room_number,
            'room_type_id' => $request->room_type_id,
            'floor' => $request->floor,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($room->image_path) {
                Storage::disk('public')->delete($room->image_path);
            }
            $imagePath = $request->file('image')->store('room-images', 'public');
            $roomData['image_path'] = $imagePath;
        }

        $room->update($roomData);

        return redirect()->route('rooms', ['search' => $request->get('search')])->with('success', 'Room updated successfully!');
    }

    public function destroy(Room $room, Request $request)
    {
        // Delete image if exists
        if ($room->image_path) {
            Storage::disk('public')->delete($room->image_path);
        }

        $room->delete();

        return redirect()->route('rooms', ['search' => $request->get('search')])->with('success', 'Room deleted successfully!');
    }
}