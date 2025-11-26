<?php

namespace App\Http\Controllers;

use App\Models\Improvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImprovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $improvements = Improvement::latest()->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'improvements' => $improvements->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'no' => $item->no,
                        'name' => $item->name,
                        'price' => $item->price,
                        'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                    ];
                }),
            ]);
        }

        return view('improvements.index', compact('improvements'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('improvements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no' => 'nullable|numeric',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:5120',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $fileName = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uniqueName = uniqid('improvement_') . '.' . $file->getClientOriginalExtension();
            $fileName = $file->storeAs('improvements', $uniqueName, 'public');
        }
        $improvement = Improvement::create([
            'no' => $request->no,
            'image' => $fileName,
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Improvement added successfully.',
            'data' => $improvement,
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
        $improvement = Improvement::findOrFail($id); // Fetch the improvement from the database
        return view('improvements.edit', compact('improvement')); // Pass it to the view
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $improvement = Improvement::findOrFail($id);

        $request->validate([
            'no' => 'nullable|numeric',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
        ]);
        $data = $request->only(['no', 'name', 'price']);

        if ($request->hasFile('image')) {
            if ($improvement->image && Storage::disk('public')->exists($improvement->image)) {
                Storage::disk('public')->delete($improvement->image);
            }
            $file = $request->file('image');
            $uniqueName = uniqid('improvement_') . '.' . $file->getClientOriginalExtension();
            $fileName = $file->storeAs('improvements', $uniqueName, 'public');
            $data['image'] = $fileName;
        }

        $improvement->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Improvement updated successfully.',
            'data' => $improvement,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $improvement = Improvement::findOrFail($id);

        // if ($improvement->image && Storage::disk('public')->exists($improvement->image)) {
        //     Storage::disk('public')->delete($improvement->image);
        // }
        $improvement->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Improvement deleted successfully.'
        ]);
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xls,xlsx,csv',
        ]);

        $file = $request->file('import_file');
        $extension = $file->getClientOriginalExtension();

        // Read Excel/CSV data
        if ($extension === 'csv') {
            $data = array_map('str_getcsv', file($file->getRealPath()));
        } else {
            $spreadsheet = IOFactory::load($file->getPathname());
            $data = $spreadsheet->getActiveSheet()->toArray();
        }

        if (count($data) < 2) {
            return response()->json(['error' => 'File is empty or missing data'], 400);
        }

        // Prepare headers
        $rawHeaders = array_shift($data);
        $headers = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h))), $rawHeaders);

        $importedCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            if (count($row) < count($headers)) continue;

            $rowData = array_combine($headers, $row);

            $excelImage = $rowData['link'] ?? $rowData['link'] ?? null;

            if ($excelImage) {
                $fileName = 'improvements/' . ltrim($excelImage, '/');
            } else {
                $fileName = 'default.jpeg';
            }

            try {
                Improvement::create([
                    'name'        => $rowData['name'] ?? 'Untitled',
                    'no'          => $rowData['no_'] ?? ' ',
                    'price'       => $rowData['price'] ?? 0,
                    'image'       => $fileName,
                ]);
                $importedCount++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }
        return response()->json([
            'success' => true,
            'message' => "$importedCount cards imported successfully.",
            'errors'  => $errors,
        ]);
    }

    public function downloadDemo()
    {
        $filePath = public_path('assets/demo-files/improvements.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'improvements_demo.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        abort(404, 'Demo Excel file not found.');
    }
}
