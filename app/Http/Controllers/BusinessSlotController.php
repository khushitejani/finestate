<?php

namespace App\Http\Controllers;

use App\Models\BusinessSlot;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BusinessSlotController extends Controller
{
    public function index()
    {
        $business_slots = BusinessSlot::latest()->get();
        return view('business_slots.index', compact('business_slots'));
    }

    public function create()
    {
        return view('business_slots.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no' => 'nullable|string|max:255',
            'expansion_time' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
        ]);

        BusinessSlot::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Business Slot created successfully!'
        ]);
    }

    public function edit($id)
    {
        $businessSlot = BusinessSlot::findOrFail($id);
        return view('business_slots.edit', compact('businessSlot'));
    }

    public function update(Request $request, $id)
    {
        $slot = BusinessSlot::findOrFail($id);

        $request->validate([
            'no' => 'nullable|string|max:255',
            'expansion_time' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
        ]);

        $slot->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Business Slot updated successfully!'
        ]);
    }

    public function destroy($id)
    {
        $slot = BusinessSlot::findOrFail($id);
        $slot->delete(); 

        return response()->json([
            'success' => true,
            'message' => 'Business Slot deleted successfully!'
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
                return strtolower(trim(str_replace([' ', '.', '-', '(', ')'], '_', $h)));
            }, $rawHeaders);

            $importedCount = 0;

            foreach ($rows as $row) {
                $rowData = array_combine($headers, $row);

                $price = isset($rowData['price'])
                    ? floatval(str_replace(['$', ','], '', $rowData['price']))
                    : 0;

                BusinessSlot::create([
                    'no' => $rowData['slot_no_'] ?? null,
                    'expansion_time' => $rowData['expantion_time'] ?? null, 
                    'price' => $price,
                ]);

                $importedCount++;
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => "{$importedCount} Business Slots imported successfully!",
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
        $filePath = public_path('assets/demo-files/BusinessSlotDemo.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'BusinessSlotDemo.xlsx');
        }

        abort(404, 'Demo file not found.');
    }
}
