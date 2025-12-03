<?php

namespace App\Http\Controllers;

use App\Models\Island;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IslandController extends Controller
{
    public function index()
    {
        $islands = Island::all();
        return view('islands.index', compact('islands'));
    }

    public function create()
    {
        return view('islands.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'no' => 'nullable|numeric',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:20480',
        ]);
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $uniqueName = uniqid('island_') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('islands', $uniqueName, 'public');
                $uploadedImages[] = $path;
            }
        }
        Island::create([
            'no' => $request->no,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'images' => $uploadedImages,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Island created successfully!',
        ]);
    }

    public function edit(Island $island)
    {
        return view('islands.edit', compact('island'));
    }

    public function update(Request $request, Island $island)
    {
        $data = $request->validate([
            'no' => 'nullable|numeric',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $finalImages = $request->existing_images ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('islands', 'public');
                $finalImages[] = $path;
            }
        }
        $island->update([
            'no' => $request->no,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'images' => $finalImages,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Island updated successfully!',
            'data' => $island
        ]);
    }

    public function destroy(Island $island)
    {
        // if ($island->image) {
        //     Storage::disk('public')->delete($island->image);
        // }
        $island->delete();

        return response()->json([
            'success' => true,
            'message' => 'Island deleted successfully!'
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
                return strtolower(trim(str_replace([' ', '.', '-', '(', ')', '$'], ['_', '_', '_', '', '', ''], $h)));
            }, $rawHeaders);

            $importedCount = 0;

            foreach ($rows as $row) {
                $rowData = array_combine($headers, $row);

                $name = $rowData['game_name'] ?? $rowData['name'] ?? 'Unknown';
                $no          = $rowData['no_'] ?? ' ';
                $description = $rowData['description'] ?? null;
                $price = isset($rowData['price']) ? floatval(str_replace(['$', ',', '₹'], '', $rowData['price'])) : 0;

                $images = [];
                if (!empty($rowData['link'])) {
                    $rawImages = explode(',', $rowData['link']);
                    foreach ($rawImages as $img) {
                        $img = trim($img);
                        if ($img) {
                            $images[] = 'islands/' . ltrim($img, '/');
                        }
                    }
                }

                if (empty($images)) {
                    $images[] = 'default.jpeg';
                }
                Island::create([
                    'name' => $name,
                    'price' => $price,
                    'no'          => $no,
                    'description' => $description,
                    'images' => $images,
                ]);

                $importedCount++;
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => "{$importedCount} islands imported successfully!",
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
       $filePath = public_path('assets/demo-files/Island.zip');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'Island.zip');
        }

        abort(404, 'ZIP file not found.');
    }
}
