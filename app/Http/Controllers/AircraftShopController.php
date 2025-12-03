<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AircraftShop;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AircraftShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aircrafts = AircraftShop::all();
        return view('aircraftshops.index', compact('aircrafts'));
    }
    public function create()
    {
        return view('aircraftshops.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no'            => 'nullable|numeric',
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric',
            'description'   => 'required|string',
            'images'      => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:10240'
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $uniqueName = uniqid('aircraftshop_') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('aircraftshops', $uniqueName, 'public');
                $imagePaths[] = $path;
            }
        }
        $aircraftShop = new AircraftShop();
        $aircraftShop->no = $request->no;
        $aircraftShop->name = $request->name;
        $aircraftShop->price = $request->price;
        $aircraftShop->description = $request->description;
        $aircraftShop->images = json_encode($imagePaths);
        $aircraftShop->save();

        return response()->json([
            'success' => true,
            'message' => 'Aircraft Shop created successfully.'
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $aircraftShop = AircraftShop::findOrFail($id);
        return view('aircraftshops.edit', compact('aircraftShop'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $aircraftShop = AircraftShop::findOrFail($id);

        $request->validate([
            'no'              => 'nullable|numeric',
            'name'            => 'required|string|max:255',
            'price'           => 'required|numeric',
            'description'     => 'nullable|string',
            'images.*'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'existing_images' => 'nullable|array',
        ]);

        $existingImages = $request->existing_images ?? [];
        $imagePaths = $existingImages;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $uniqueName = uniqid('aircraftshop_') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('aircraftshops', $uniqueName, 'public');
                $imagePaths[] = $path;
            }
        }

        $oldImages = is_string($aircraftShop->images)
            ? json_decode($aircraftShop->images, true)
            : (array)$aircraftShop->images;

        $removedImages = array_diff($oldImages, $imagePaths);
        foreach ($removedImages as $img) {
            if (Storage::disk('public')->exists($img)) {
                Storage::disk('public')->delete($img);
            }
        }
        $aircraftShop->no = $request->no;
        $aircraftShop->name = $request->name;
        $aircraftShop->price = $request->price;
        $aircraftShop->description = $request->description;
        $aircraftShop->images = json_encode($imagePaths);
        $aircraftShop->save();

        return response()->json([
            'success' => true,
            'message' => 'Aircraft Shop updated successfully.'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $aircraftShop = AircraftShop::find($id);
        if (!$aircraftShop) {
            return response()->json([
                'success' => false,
                'message' => 'Aircraft Shop not found.'
            ], 404);
        }
        // if ($aircraftShop->image && Storage::disk('public')->exists($aircraftShop->image)) {
        //     Storage::disk('public')->delete($aircraftShop->image);
        // }
        $aircraftShop->delete();
        return response()->json([
            'success' => true,
            'message' => 'Aircraft Shop deleted successfully.'
        ]);
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
                return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $h)));
            }, $rawHeaders);
            $importedCount = 0;

            foreach ($rows as $rowIndex => $row) {
                $rowData = array_combine($headers, $row);
                $priceStr = $rowData['game_price__in___'] ?? '0';
                $price = floatval(str_replace([',', '$'], '', $priceStr));
                $no = isset($rowData['no_']) ? intval($rowData['no_']) : null;
                AircraftShop::create([
                    'name' => $rowData['game_name_'] ?? 'Unknown',
                    'no' => $no,
                    'address' => $rowData['real_name_'] ?? 'Unknown',
                    'price' => $price,
                    'description' => $rowData['company_name'] ?? 'Unknown',
                    'images' => json_encode(
                        array_map(
                            fn($img) => 'aircraftshops/' . ltrim(trim(str_replace('\\', '/', $img)), '/'),
                            explode(',', $rowData['link'] ?? '')
                        ),
                        JSON_UNESCAPED_SLASHES
                    ),

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
        $filePath = public_path('assets/demo-files/Aircraftshop.zip');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'Aircraftshop.zip');
        }

        abort(404, 'ZIP file not found.');
    }
}
