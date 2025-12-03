<?php

namespace App\Http\Controllers;

use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;



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
            'no' => 'nullable|numeric',
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
            'no' => $request->no,
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
            'no' => 'nullable|numeric',
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
            'no' => $request->no,
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
        // if ($crypto->image) {
        //     Storage::disk('public')->delete($crypto->image);
        // }

        $crypto->delete();

        return response()->json(['success' => true, 'message' => 'Cryptocurrency deleted successfully!']);
    }
    public function bulkImport(Request $request)
    {
        try {
            if (!$request->hasFile('import_file')) {
                return response()->json(['error' => 'No file uploaded'], 400);
            }

            $file = $request->file('import_file');
            $extension = $file->getClientOriginalExtension();

            if (!in_array($extension, ['xls', 'xlsx', 'csv'])) {
                return response()->json(['error' => 'Invalid file type'], 400);
            }

            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) < 2) {
                return response()->json(['error' => 'File contains no data'], 400);
            }

            // Normalize headers
            $rawHeaders = array_shift($rows);
            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace([' ', '.', '-'], '_', $h)));
            }, $rawHeaders);

            $importedCount = 0;

            foreach ($rows as $row) {
                $rowData = array_combine($headers, $row);

                $no = $rowData['no'] ?? null;

                $name = $rowData['name']
                    ?? $rowData['crypto_name']
                    ?? $rowData['crypto']
                    ?? 'Unknown';

                $price = isset($rowData['price'])
                    ? floatval(str_replace([',', '$'], '', $rowData['price']))
                    : 0;

                $cap = isset($rowData['cryptocurrencies_cap'])
                    ? floatval(str_replace([',', '$'], '', $rowData['cryptocurrencies_cap']))
                    : 0;

                $availableForPurchase = isset($rowData['available_for_purchase'])
                    ? intval($rowData['available_for_purchase'])
                    : 0;

                // Image file path from excel
                $imagePath = $rowData['image'] ?? $rowData['logo'] ?? null;
                $storedImagePath = $imagePath ? 'cryptos/' . ltrim($imagePath, '/') : 'default.jpeg';

                Cryptocurrency::create([
                    'no'                     => $no,
                    'name'                   => $name,
                    'price'                  => $price,
                    'cryptocurrencies_cap'   => $cap,
                    'available_for_purchase' => $availableForPurchase,
                    'image'                  => $storedImagePath,
                    'day_prices'             => ['base' => []],
                ]);

                $importedCount++;
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
