<?php

namespace App\Http\Controllers;

use App\Models\Construction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class ConstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $constructions = Construction::latest()->get();
        return view('constructions.index', compact('constructions'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('constructions.create'); // modal form
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'metal' => 'nullable|string|max:255',
            'builder_men' => 'nullable|string|max:255',
            'wood' => 'nullable|string|max:255',
            'concrete' => 'nullable|string|max:255',
            'total_cost_of_construction' => 'nullable|numeric',
            'total_profit' => 'nullable|numeric',
            'total_percentage' => 'nullable|numeric',
            'time' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'total_return_after_completion' => 'nullable|numeric',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('constructions', 'public');
        }

        $construction = Construction::create(array_merge(
            $request->only([
                'no',
                'name',
                'metal',
                'builder_men',
                'wood',
                'concrete',
                'total_cost_of_construction',
                'total_profit',
                'total_percentage',
                'time',
                'total_return_after_completion'
            ]),
            ['image' => $imagePath]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Construction created successfully!',
            'data' => $construction
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
    public function edit($id)
    {
        $construction = Construction::findOrFail($id);
        return view('constructions.edit', compact('construction')); // modal form
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $construction = Construction::findOrFail($id);

        $request->validate([
            'no' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'metal' => 'nullable|string|max:255',
            'builder_men' => 'nullable|string|max:255',
            'wood' => 'nullable|string|max:255',
            'concrete' => 'nullable|string|max:255',
            'total_cost_of_construction' => 'nullable|numeric',
            'total_profit' => 'nullable|numeric',
            'total_percentage' => 'nullable|numeric',
            'time' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'total_return_after_completion' => 'nullable|numeric',
        ]);

        if ($request->hasFile('image')) {
            if ($construction->image && Storage::disk('public')->exists($construction->image)) {
                Storage::disk('public')->delete($construction->image);
            }
            $construction->image = $request->file('image')->store('constructions', 'public');
        }

        $construction->update($request->only([
            'no',
            'name',
            'metal',
            'builder_men',
            'wood',
            'concrete',
            'total_cost_of_construction',
            'total_profit',
            'total_percentage',
            'time',
            'total_return_after_completion'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Construction updated successfully!',
            'data' => $construction
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $construction = Construction::findOrFail($id);

        $construction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Construction deleted successfully!'
        ]);
    }

    public function bulkImport(Request $request)
    {
        try {
            if (!$request->hasFile('import_file')) {
                return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
            }

            $file = $request->file('import_file');
            $extension = $file->getClientOriginalExtension();

            if (!in_array($extension, ['xls', 'xlsx', 'csv'])) {
                return response()->json(['success' => false, 'message' => 'Invalid file type.'], 400);
            }

            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) < 2) {
                return response()->json(['success' => false, 'message' => 'File is empty or has no data.'], 400);
            }

            $rawHeaders = array_shift($rows);
            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace([' ', '.', '/', '-', '(', ')', '%', '$'], '_', $h)));
            }, $rawHeaders);
            $importedCount = 0;
            foreach ($rows as $row) {
                $rowData = array_combine($headers, $row);

                $totalCost = isset($rowData['total_cost_of_construction'])
                    ? floatval(str_replace(['$', ',', ' '], '', $rowData['total_cost_of_construction']))
                    : 0;

                $totalReturn = isset($rowData['total_return_after_complete_construction'])
                    ? floatval(str_replace(['$', ',', ' '], '', $rowData['total_return_after_complete_construction']))
                    : 0;

                $totalProfit = isset($rowData['total_profit_in__'])
                    ? floatval(str_replace(['$', ',', ' '], '', $rowData['total_profit_in__']))
                    : 0;

                $profitPercentage = isset($rowData['profit_percentage'])
                    ? floatval(str_replace(['%', ' '], '', $rowData['profit_percentage']))
                    : 0;

                $logoPath = $rowData['link'] ?? null;
                $storedImagePath = $logoPath ? 'constructions/' . ltrim($logoPath, '/') : 'default.jpeg';
                $construction = Construction::create([
                    'no' => $rowData['no_'] ?? null,
                    'name' => $rowData['project_name'] ?? null,
                    'metal' => $rowData['metal'] ?? null,
                    'builder_men' => $rowData['builders___men'] ?? null,
                    'wood' => $rowData['wood'] ?? null,
                    'concrete' => $rowData['concrete'] ?? null,
                    'total_cost_of_construction' => $totalCost,
                    'total_return_after_completion' => $totalReturn,
                    'total_profit' => $totalProfit,
                    'total_percentage' => $profitPercentage,
                    'time' => $rowData['time'] ?? null,
                    'image' => $storedImagePath,
                ]);
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => "{$importedCount} constructions imported successfully!"
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function downloadDemo()
    {
        $filePath = public_path('assets/demo-files/ConstructionDemo.xlsx');
        if (file_exists($filePath)) {
            return response()->download($filePath, 'ConstructionDemo.xlsx');
        }
        abort(404, 'Demo file not found.');
    }
}
