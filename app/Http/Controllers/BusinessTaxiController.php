<?php

namespace App\Http\Controllers;

use App\Models\BusinessTaxi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class BusinessTaxiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxis = BusinessTaxi::latest()->get();
        return view('business_taxis.index', compact('taxis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('business_taxis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'class' => 'nullable|string|max:255',
                'resource' => 'nullable|string',
                'income_per_hour' => 'nullable|numeric',
                'price' => 'nullable|numeric',
            ]);

            $fileName = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $uniqueName = uniqid('taxi_') . '.' . $file->getClientOriginalExtension();
                $fileName = $file->storeAs('business_taxis', $uniqueName, 'public');
            }

            $taxi = BusinessTaxi::create([
                'name' => $request->name,
                'resource' => $request->resource,
                'class' => $request->class,
                'income_per_hour' => $request->income_per_hour,
                'price' => $request->price,
                'image' => $fileName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Business Taxi created successfully.',
                'data' => $taxi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error occurred.',
                'error' => $e->getMessage(),
            ]);
        }
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
        $taxi = BusinessTaxi::findOrFail($id);
        return view('business_taxis.edit', compact('taxi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $taxi = BusinessTaxi::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'class' => 'nullable|string|max:255',
                'resource' => 'nullable|string',
                'income_per_hour' => 'nullable|numeric',
                'price' => 'nullable|numeric',
            ]);

            $data = $request->only(['name', 'class', 'resource', 'income_per_hour', 'price']);

            if ($request->hasFile('image')) {
                if ($taxi->image && Storage::disk('public')->exists($taxi->image)) {
                    Storage::disk('public')->delete($taxi->image);
                }
                $file = $request->file('image');
                $uniqueName = uniqid('taxi_') . '.' . $file->getClientOriginalExtension();
                $fileName = $file->storeAs('business_taxis', $uniqueName, 'public');
                $data['image'] = $fileName;
            }

            $taxi->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Business Taxi updated successfully.',
                'data' => $taxi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating taxi.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $taxi = BusinessTaxi::find($id);

        if (!$taxi) {
            return response()->json([
                'success' => false,
                'message' => 'Business Taxi not found.',
            ], 404);
        }

        if ($taxi->image && Storage::disk('public')->exists($taxi->image)) {
            Storage::disk('public')->delete($taxi->image);
        }

        $taxi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Business Taxi deleted successfully.',
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
                $h = strtolower(trim($h));
                $h = str_replace([' ', '.', '/', '$'], '_', $h);
                return $h;
            }, $rawHeaders);

            $importedCount = 0;
            $currentClass = 'Unknown';
            $grouped = [];

            foreach ($rows as $row) {
                if (count(array_filter($row)) === 0) continue;
                $rowData = array_combine($headers, $row);

                if (!empty($rowData['class'])) {
                    $currentClass = $rowData['class'];
                }

                $grouped[$currentClass][] = $rowData;
            }
            foreach ($grouped as $className => $classRows) {
                foreach ($classRows as $rowData) {
                    $name = $rowData['game_name'] ?? $rowData['name'] ?? 'Unknown';
                    $resource = $rowData['total_km'] ?? 'Unknown';
                    $incomePerHour = isset($rowData['income_per_hour']) ? floatval($rowData['income_per_hour']) : 0;
                    $price = 0;
                    if (isset($rowData['price_in__']) && !empty($rowData['price_in__'])) {
                        $price = floatval(str_replace([',', '$'], '', $rowData['price_in__']));
                    }


                    $imagePath = $rowData['image_path'] ?? null;
                    $storedImagePath = $imagePath ? 'business_taxis/' . $imagePath : 'default.jpeg';

                    BusinessTaxi::create([
                        'class' => $className,
                        'name' => $name,
                        'resource' => $resource,
                        'income_per_hour' => $incomePerHour,
                        'price' => $price,
                        'image' => $storedImagePath,
                    ]);
                    $importedCount++;
                }
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
        $filePath = public_path('assets/demo-files/business_texi.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'business_taxis_demo.xlsx');
        }
        abort(404, 'Demo Excel file not found.');
    }
 
}
