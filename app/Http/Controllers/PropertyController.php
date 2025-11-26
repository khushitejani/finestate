<?php

namespace App\Http\Controllers;

use App\Models\Improvement;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Support\Facades\Log;


class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $properties = Property::latest()->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'properties' => $properties->map(function ($property) {
                    return [
                        'id' => $property->id,
                        'address' => $property->address,
                        'price' => $property->price,
                        'income_per_hour' => $property->income_per_hour,
                        'image_url' => $property->image ? asset('storage/' . $property->image) : null,
                    ];
                }),
            ]);
        }
        return view('properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        return view('properties.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    public function store(Request $request)
    {
        $request->validate([
            'no' => 'nullable|numeric',
            'income_per_hour' => 'required|numeric',
            'price' => 'required|numeric',
            'address' => 'required|string',
            'property_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ]);

        $imagePaths = [];

        if ($request->hasFile('property_images')) {
            foreach ($request->file('property_images') as $file) {
                $path = $file->store('properties', 'public'); 
                $imagePaths[] = $path;
            }
        }

        $property = Property::create([
            'no' => $request->no,
            'income_per_hour' => $request->income_per_hour,
            'price' => $request->price,
            'address' => $request->address,
            'image' => json_encode($imagePaths),
        ]);

        return redirect()->route('properties.index')->with('success', 'Property created successfully');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        return view('properties.edit', compact('property'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        $request->validate([
            'no' => 'nullable|numeric',
            'income_per_hour' => 'required|numeric|min:0',
            'address' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'property_images_json' => 'nullable|string',
            'property_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $property->no = $request->no;
        $property->income_per_hour = $request->income_per_hour;
        $property->address = $request->address;
        $property->price = $request->price;

        $images = $request->input('property_images_json') ? json_decode($request->input('property_images_json'), true) : [];

        if ($request->hasFile('property_images')) {
            foreach ($request->file('property_images') as $file) {
                $path = $file->store('properties', 'public');
                $images[] = $path;
            }
        }
        $property->image = json_encode($images);
        $property->save();
        return redirect()->route('properties.index')->with('success', 'Property updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        try {
            // if ($property->image && Storage::disk('public')->exists($property->image)) {
            //     Storage::disk('public')->delete($property->image);
            // }
            $property->delete();
            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete property.',
            ], 500);
        }
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
                return strtolower(trim(str_replace(' ', '_', $h)));
            }, $rawHeaders);

            $importedCount = 0;

            foreach ($rows as $row) {
                $rowData = array_combine($headers, $row);
                $no = $rowData['no'] ?? ' ';
                $address = $rowData['location'] ?? 'Unknown';
                $price = isset($rowData['purchase_price']) ? floatval(str_replace(['$', ','], '', $rowData['purchase_price'])) : 0;
                $incomePerHour = isset($rowData['income_per_hour']) ? floatval(str_replace(['$', ','], '', $rowData['income_per_hour'])) : 0;
                $imagesArray = array_map('trim', explode(',', $rowData['link'] ?? ''));
                $imagesArray = array_map(fn($img) => 'properties/' . ltrim(str_replace('\\', '/', $img), '/'), $imagesArray);
                Property::create([
                    'no' => $no,
                    'address' => $address,
                    'price' => $price,
                    'income_per_hour' => $incomePerHour,
                    'image' => json_encode($imagesArray, JSON_UNESCAPED_SLASHES),
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
    public function downloadDemo()
    {
        $filePath = public_path('assets/demo-files/properties.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'properties_demo.xlsx');
        }

        abort(404, 'Demo Excel file not found.');
    }
}
