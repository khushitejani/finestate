<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class CryptocurrencyController extends Controller
{
    public function index()
    {
        $cryptos = Cryptocurrency::latest()->get();
        return view('cryptos.index', compact('cryptos'));
    }

    public function create()
    {
        return view('cryptos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cryptocurrencies_cap' => 'nullable|numeric|min:0',
            'available_for_purchase' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'day_prices_input' => 'nullable|string',
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('cryptos', 'public');
        }
        $basePrices = [];
        if ($request->filled('day_prices_input')) {
            $basePrices = array_map('floatval', explode(',', $request->day_prices_input));
        }
        Cryptocurrency::create([
            'name' => $request->name,
            'price' => $request->price,
            'cryptocurrencies_cap' => $request->cryptocurrencies_cap,
            'available_for_purchase' => $request->available_for_purchase ?? 0,
            'image' => $image,
            'day_prices' => ['base' => $basePrices],
        ]);
        return response()->json(['success' => true, 'message' => 'Cryptocurrency created successfully!']);
    }

    public function edit(Cryptocurrency $crypto)
    {
        return view('cryptos.edit', ['cryptocurrency' => $crypto]);
    }


    public function update(Request $request, Cryptocurrency $crypto)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cryptocurrencies_cap' => 'nullable|numeric|min:0',
            'available_for_purchase' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'day_prices_input' => 'nullable|string',
        ]);

        $imagePath = $crypto->image;
        if ($request->hasFile('image')) {
            if ($crypto->image) {
                Storage::disk('public')->delete($crypto->image);
            }
            $imagePath = $request->file('image')->store('cryptos', 'public');
        }

        $dayPrices = $crypto->day_prices ?? ['base' => []];
        if ($request->filled('day_prices_input')) {
            $dayPrices['base'] = array_map('floatval', array_map('trim', explode(',', $request->day_prices_input)));
        }

        $crypto->update([
            'name' => $request->name,
            'price' => $request->price,
            'cryptocurrencies_cap' => $request->cryptocurrencies_cap,
            'available_for_purchase' => $request->available_for_purchase ?? $crypto->available_for_purchase,
            'image' => $imagePath,
            'day_prices' => $dayPrices,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cryptocurrency updated successfully!',
        ]);
    }

    public function destroy(Cryptocurrency $crypto)
    {
        if ($crypto->image) {
            Storage::disk('public')->delete($crypto->image);
        }

        $crypto->delete();

        return response()->json(['success' => true, 'message' => 'Cryptocurrency deleted successfully!']);
    }
    public function intradayChart()
    {
        $id = 18;
        $crypto = Cryptocurrency::find($id);

        if (!$crypto) {
            return response()->json([
                'success' => false,
                'message' => 'Cryptocurrency not found'
            ], 404);
        }

        $chartData = getChartDataForToday($crypto);

        return response()->json([
            'crypto' => $crypto->name,
            'chart' => $chartData
        ]);
    }
}
