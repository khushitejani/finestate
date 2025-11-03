<?php

namespace App\Http\Controllers;

use App\Models\Carshowroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CarshowroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carshowrooms = Carshowroom::all();
        return view('carshowrooms.index', compact('carshowrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('carshowrooms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric',
            'images'        => 'required|array',
            'images.*'      => 'image|mimes:jpg,jpeg,png,gif,webp|max:20480',
        ]);

        $uploadedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $uniqueName = uniqid('carshowroom_') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('carshowrooms', $uniqueName, 'public');
                $uploadedImages[] = $path;
            }
        }

        $carshowroom = new Carshowroom();
        $carshowroom->name  = $request->name;
        $carshowroom->price = $request->price;
        $carshowroom->images = $uploadedImages;
        $carshowroom->save();

        return response()->json([
            'success' => true,
            'message' => 'Car Showroom created successfully with multiple images.'
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
    public function edit(string $id)
    {
        $carshowroom = Carshowroom::findOrFail($id);
        return view('carshowrooms.edit', compact('carshowroom'));
    }

    public function update(Request $request, string $id)
    {
        $carshowroom = Carshowroom::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:20480',
            'existing_images' => 'nullable|string',
        ]);

        $carshowroom->name = $request->name;
        $carshowroom->price = $request->price;

        // Decode kept existing images
        $existingImages = $request->existing_images ? json_decode($request->existing_images, true) : [];

        // Handle new uploads
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $uniqueName = uniqid('carshowroom_') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('carshowrooms', $uniqueName, 'public');
                $uploadedImages[] = $path;
            }
        }

        // Merge kept images + new uploads
        $carshowroom->images = json_encode(array_merge($existingImages, $uploadedImages));

        $carshowroom->save();

        return response()->json([
            'success' => true,
            'message' => 'Car Showroom updated successfully with multiple images.'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $carshowroom = Carshowroom::find($id);

        if (!$carshowroom) {
            return response()->json([
                'success' => false,
                'message' => 'Car Showroom not found.'
            ], 404);
        }

        if ($carshowroom->image && Storage::disk('public')->exists($carshowroom->image)) {
            Storage::disk('public')->delete($carshowroom->image);
        }
        $carshowroom->delete();

        return response()->json([
            'success' => true,
            'message' => 'Car Showroom deleted successfully.'
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
            $headers = array_map(fn($h) => strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $h))), $rawHeaders);
            $importedCount = 0;
            foreach ($rows as $index => $row) {
                $rowData = @array_combine($headers, $row);
                if (!$rowData) continue;

                $name      = $rowData['game_name_'] ?? $rowData['game_name'] ?? null;
                $priceStr  = $rowData['game_price__in___'] ?? $rowData['game_price_in_'] ?? $rowData['price'] ?? null;
                $imagesStr = $rowData['image_path'] ?? '';

                if (empty($name) || empty($priceStr)) continue;

                $price = floatval(str_replace(['$', ',', ' '], '', $priceStr));

                $images = array_map(fn($img) => 'carshowrooms/' . ltrim($img, '/'), array_filter(array_map('trim', explode(',', $imagesStr))));

                Carshowroom::create([
                    'name'   => $name,
                    'price'  => $price,
                    'images' => json_encode($images, JSON_UNESCAPED_SLASHES),
                ]);

                $importedCount++;
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
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
        $filePath = public_path('assets/demo-files/carshowrooms.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'carshowrooms_demo.xlsx');
        }

        abort(404, 'Demo Excel file not found.');
    }
}
