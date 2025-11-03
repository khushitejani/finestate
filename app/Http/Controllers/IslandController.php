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
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('islands', 'public');
        }

        $island = Island::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Island created successfully!',
            'data' => $island
        ]);
    }

    public function edit(Island $island)
    {
        return view('islands.edit', compact('island'));
    }

    public function update(Request $request, Island $island)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($island->image) {
                Storage::disk('public')->delete($island->image);
            }
            $data['image'] = $request->file('image')->store('islands', 'public');
        }

        $island->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Island updated successfully!',
            'data' => $island
        ]);
    }

    public function destroy(Island $island)
    {
        if ($island->image) {
            Storage::disk('public')->delete($island->image);
        }
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
                return strtolower(trim(str_replace([' ', '-', '(', ')', '$'], ['_', '_', '', '', ''], $h)));
            }, $rawHeaders);

            $importedCount = 0;

            foreach ($rows as $row) {
                $rowData = array_combine($headers, $row);

                $name = $rowData['game_name'] ?? $rowData['name'] ?? 'Unknown';
                $description = $rowData['location'] ?? null;
                $price = isset($rowData['price']) ? floatval(str_replace(['$', ',', '₹'], '', $rowData['price'])) : 0;

                $excelImage = $rowData['image_path'] ?? $rowData['image'] ?? null;
                if ($excelImage) {
                    $storedImagePath = 'islands/' . ltrim($excelImage, '/');
                } else {
                    $storedImagePath = 'default.jpeg';
                }

                Island::create([
                    'name' => $name,
                    'price' => $price,
                    'description' => $description,
                    'image' => $storedImagePath,
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
        $filePath = public_path('assets/demo-files/islands.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'islands_demo.xlsx');
        }

        abort(404, 'Demo Excel file not found.');
    }
}
