<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NFT;
use Illuminate\Support\Facades\Storage;

class NFTController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Display all NFTs
    public function index()
    {
        $nfts = NFT::all();
        return view('nfts.index', compact('nfts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('nfts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('nfts', 'public');
        }

        $nft = NFT::create($data);

        return response()->json([
            'success' => true,
            'message' => 'NFT created successfully!',
            'data' => $nft,
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NFT $nft)
    {
        return view('nfts.edit', compact('nft'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NFT $nft)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($nft->image && Storage::disk('public')->exists($nft->image)) {
                Storage::disk('public')->delete($nft->image);
            }
            $nft->image = $request->file('image')->store('nfts', 'public');
        }

        $nft->name = $request->name;
        $nft->price = $request->price;
        $nft->description = $request->description;
        $nft->save();

        return response()->json([
            'success' => true,
            'message' => 'NFT updated successfully!',
            'data' => $nft,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NFT $nft)
    {
        if ($nft->image && \Storage::disk('public')->exists($nft->image)) {
            \Storage::disk('public')->delete($nft->image);
        }

        $nft->delete();

        return response()->json([
            'success' => true,
            'message' => 'NFT deleted successfully!',
        ]);
    }
}
