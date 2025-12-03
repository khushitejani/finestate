<?php

namespace App\Http\Controllers;

use App\Models\BusinessShipping;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BusinessShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shippings = BusinessShipping::latest()->get();
        return view('business_shipping.index', compact('shippings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('business_shipping.create'); // form Blade
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no' => 'nullable|numeric',
            'name' => 'required|string|max:255',
            'category' => 'required|in:City,State,Long-distance',
            'image' => 'nullable|image|max:2048',
            'resource' => 'nullable|string',
            'income_per_hour' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ]);

        $shipping = new BusinessShipping();
        $shipping->name = $request->name;
        $shipping->no = $request->no;
        $shipping->category = $request->category;
        $shipping->resource = $request->resource;
        $shipping->income_per_hour = $request->income_per_hour;
        $shipping->price = $request->price;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('business_shippings', 'public');
            $shipping->image = $path;
        }

        $shipping->save();

        return response()->json(['success' => true, 'message' => 'Shipping created successfully']);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shipping = BusinessShipping::findOrFail($id);
        return view('business_shipping.show', compact('shipping'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $shipping = BusinessShipping::findOrFail($id);
        return view('business_shipping.edit', compact('shipping'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shipping = BusinessShipping::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'no' => 'nullable|numeric',
            'category' => 'required|in:City,State,Long-distance',
            'image' => 'nullable|image|max:2048',
            'resource' => 'nullable|string',
            'income_per_hour' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ]);

        $shipping->name = $request->name;
        $shipping->no = $request->no;
        $shipping->category = $request->category;
        $shipping->resource = $request->resource;
        $shipping->income_per_hour = $request->income_per_hour;
        $shipping->price = $request->price;

        if ($request->hasFile('image')) {
            // if ($shipping->image) {
            //     Storage::disk('public')->delete($shipping->image);
            // }
            $shipping->image = $request->file('image')->store('business_shippings', 'public');
        }

        $shipping->save();

        return response()->json(['success' => true, 'message' => 'Shipping updated successfully']);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shipping = BusinessShipping::findOrFail($id);

        if ($shipping->image) {
            Storage::disk('public')->delete($shipping->image);
        }

        $shipping->delete();

        return response()->json(['success' => true, 'message' => 'Shipping deleted successfully']);
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
                return response()->json(['error' => 'File is empty or has no data'], 400);
            }

            $rawHeaders = array_shift($rows);
            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace([' ', '.', '/', '$'], '_', $h)));
            }, $rawHeaders);
            $importedCount = 0;

            foreach ($rows as $row) {
                if (count(array_filter($row)) === 0) continue;
                $rowData = array_combine($headers, $row);
                $shipping = new BusinessShipping();
                $shipping->name = $rowData['game_name'] ?? 'Unknown';
                $shipping->category = $rowData['class'] ?? 'City';
                $shipping->resource = $rowData['total_km'] ?? '';
                $shipping->no = isset($rowData['no_']) ? intval($rowData['no_']) : null;
                $shipping->income_per_hour = isset($rowData['total_km___income_per_hour']) ? floatval($rowData['total_km___income_per_hour']) : 0;
                $shipping->price = isset($rowData['price'])
                    ? floatval(str_replace(['$', ','], '', $rowData['price']))
                    : 0;
                if (!empty($rowData['link'])) {
                    $imageName = trim($rowData['link']);
                    $shipping->image = 'business_shippings/' . $imageName;
                } else {
                    $shipping->image = null;
                }
                $shipping->save();
                $importedCount++;
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadDemo()
    {
        $filePath = public_path('assets/demo-files/Business_Shipping.zip');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'BusinessShipping.zip');
        }

        abort(404, 'Demo ZIP file not found.');
    }
}
