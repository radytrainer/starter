<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Item::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        // Create post
        $post = Item::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Item created successfully!',
            'data' => $post,
            'image_url' => $imagePath ? asset('storage/' . $imagePath) : null,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
        return response()->json($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        //
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        // Handle image upload
        $imagePath = $item->image;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
                $imagePath = $request->file('image')->store('items', 'public');
                $item->image = $imagePath;
            }
            // Update post
            $item->update([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $imagePath,
            ]);
        } else {
            // Update post without image
            $item->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
        }
        $item->save();
        return response()->json([
            'message' => 'Item updated successfully!',
            'data' => $item,
            'image_url' => $imagePath ? asset('storage/' . $imagePath) : null,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        //
        $item->delete();
        return response()->json([
            'message' => 'Item deleted successfully!',
        ], 200);
    }
}
