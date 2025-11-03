<?php

namespace App\Http\Controllers;

use App\Models\YatchShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class YatchShopController extends Controller
{
    public function index()
    {
        $yatch_shops = YatchShop::latest()->get();
        return view('yatch_shops.index', compact('yatch_shops'));
    }

    public function create()
    {
        return view('yatch_shops.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('yatch_shops', 'public');
        }

        YatchShop::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'image'       => json_encode([$image]),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Yacht created successfully!',
        ]);
    }

    public function show(YatchShop $yachtshop)
    {
        return view('yatch_shops.show', compact('yachtshop'));
    }

    public function edit(YatchShop $yatchshop)
    {
        return view('yatch_shops.edit', compact('yatchshop'));
    }
    public function update(Request $request, YatchShop $yatchshop)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $images = json_decode($yatchshop->image, true) ?? [];

        if ($request->hasFile('image')) {
            $images = [$request->file('image')->store('yatch_shops', 'public')];
        }

        $yatchshop->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'image'       => json_encode($images),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Yacht updated successfully!',
        ]);
    }

    public function destroy(YatchShop $yatchshop)
    {
        if (!$yatchshop->exists) {
            return response()->json([
                'success' => false,
                'message' => 'Yacht not found or already deleted.',
            ]);
        }

        $deleted = $yatchshop->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Yacht deleted successfully!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete yacht.',
            ]);
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
            $rows = $sheet->toArray(null, true, true, true);

            if (count($rows) < 2) {
                return response()->json(['error' => 'File is empty or has no data'], 400);
            }

            $rawHeaders = array_shift($rows);
            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace([' ', '-', '(', ')', '$'], ['_', '_', '', '', ''], $h)));
            }, array_values($rawHeaders));

            $importedCount = 0;

            foreach ($rows as $row) {
                $row = array_values($row);

                if (count(array_filter($row)) === 0) continue;
                if (count($row) < count($headers)) continue;

                $rowData = @array_combine($headers, $row);
                if (!$rowData) continue;

                $name = $rowData['game_name_'] ?? 'Unknown';
                $price = isset($rowData['game_price_in_'])
                    ? floatval(str_replace(['$', ',', '₹'], '', $rowData['game_price_in_']))
                    : 0;
                $excelImage = $rowData['image_path'] ?? null;
                if ($excelImage) {
                    $storedImagePath = 'yatch_shops/' . ltrim($excelImage, '/');
                } else {
                    $storedImagePath = null;
                }

                YatchShop::create([
                    'name'        => $name,
                    'price'       => $price,
                    'image'       => json_encode([$storedImagePath]),
                ]);

                $importedCount++;
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => 'Yacht data imported successfully!',
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
        $filePath = public_path('assets/demo-files/Yachts.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'yatch_shops_demo.xlsx');
        }

        abort(404, 'Demo Excel file not found.');
    }
}
