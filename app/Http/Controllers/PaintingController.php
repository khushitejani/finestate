<?php

namespace App\Http\Controllers;

use App\Models\Painting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PaintingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paintings = Painting::all();
        return view('paintings.index', compact('paintings'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('paintings.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'years' => 'required|string|max:20',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        $fileName = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'paintings/' . uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('paintings', $file, basename($fileName));
        }

        Painting::create([
            'name' => $request->name,
            'years'  => $request->years,
            'price' => $request->price,
            'image' => $fileName,
        ]);

        return response()->json(['success' => true, 'message' => 'Painting created successfully.']);
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
        $painting = Painting::findOrFail($id);
        return view('paintings.edit', compact('painting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $painting = Painting::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'years' => 'required|string|max:20',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        // If new image file uploaded, delete old image and save new one
        if ($request->hasFile('image')) {
            if ($painting->image && Storage::disk('public')->exists($painting->image)) {
                Storage::disk('public')->delete($painting->image);
            }

            $file = $request->file('image');
            $fileName = 'paintings/' . uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('paintings', $file, basename($fileName));
            $painting->image = $fileName;
        }

        $painting->name = $request->name;
        $painting->years = $request->years;
        $painting->price = $request->price;

        $painting->save();

        return response()->json(['success' => true, 'message' => 'Painting updated successfully.']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $painting = Painting::findOrFail($id);

        // Delete image if exists
        if ($painting->image && Storage::disk('public')->exists($painting->image)) {
            Storage::disk('public')->delete($painting->image);
        }

        $painting->delete();

        return response()->json(['success' => true, 'message' => 'Painting deleted successfully.']);
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xls,xlsx,csv',
        ]);
        $file = $request->file('import_file');
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, ['xls', 'xlsx', 'csv'])) {
            return response()->json(['error' => 'Invalid file type'], 400);
        }
        if ($extension === 'csv') {
            $data = array_map('str_getcsv', file($file->getRealPath()));
        } else {
            $spreadsheet = IOFactory::load($file->getPathname());
            $data = $spreadsheet->getActiveSheet()->toArray();
        }

        if (count($data) < 2) {
            return response()->json(['error' => 'File is empty or has no data'], 400);
        }

        $rawHeaders = array_shift($data);
        $headers = array_map(function ($h) {
            return strtolower(trim(str_replace(' ', '_', $h)));
        }, $rawHeaders);

        $importedCount = 0;
        $errors = [];
        foreach ($data as $index => $row) {
            $rowData = array_combine($headers, $row);

            $coinData = [
                'name'  => $rowData['name'] ?? null,
                'years' => $rowData['years'] ?? null,
                'price' => $rowData['price'] ?? null,
            ];
            $fileName = null;
            $excelImage = $rowData['image_path'] ?? $rowData['image'] ?? null;
            if ($excelImage) {
                $fileName = 'paintings/' . ltrim($excelImage, '/');
            } else {
                $fileName = 'default.jpeg';
            }
            Painting::create([
                'name'  => $coinData['name'],
                'years' => $coinData['years'],
                'price' => $coinData['price'],
                'image' => $fileName,
            ]);
            $importedCount++;
        }
        return response()->json([
            'success' => true,
            'message' => "$importedCount paintings imported successfully.",
            'errors' => $errors,
        ]);
    }

    public function downloadDemo()
    {
        $filePath = public_path('assets/demo-files/painting.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'painting_demo.xlsx');
        }

        abort(404, 'Demo Excel file not found.');
    }
}
