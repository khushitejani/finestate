<?php

namespace App\Http\Controllers;

use App\Models\Jewelled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class JewelledController extends Controller
{
    public function index()
    {
        $jewelleds = Jewelled::all();
        return view('jewelleds.index', compact('jewelleds'));
    }

    public function create()
    {
        return view('jewelleds.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no'    => 'nullable|numeric',
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only('no','name', 'price');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('jewelleds', 'public');
        }

        Jewelled::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Jewelled created successfully.'
        ]);
    }

    public function edit(Jewelled $jewelled)
    {
        return view('jewelleds.edit', compact('jewelled'));
    }

    public function update(Request $request, Jewelled $jewelled)
    {
        $request->validate([
            'no'    => 'nullable|numeric',
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only('no', 'name', 'price');

        if ($request->hasFile('image')) {
            if ($jewelled->image) {
                Storage::disk('public')->delete($jewelled->image);
            }
            $data['image'] = $request->file('image')->store('jewelleds', 'public');
        }

        $jewelled->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Jewelled updated successfully.'
        ]);
    }

    public function destroy(Jewelled $jewelled)
    {
        // if ($jewelled->image) {
        //     Storage::disk('public')->delete($jewelled->image);
        // }
        $jewelled->delete();

        return response()->json(['success' => true, 'message' => 'Jewelled deleted successfully.']);
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
                'no'    => $rowData['no_'] ?? ' ',
                'price' => $rowData['price'] ?? null,
            ];
            $fileName = null;
            $excelImage = $rowData['link'] ?? $rowData['link'] ?? null;
            if ($excelImage) {
                $fileName = 'jewelleds/' . ltrim($excelImage, '/');
            } else {
                $fileName = 'default.jpeg';
            }
            Jewelled::create([
                'name'  => $coinData['name'],
                'no'    => $coinData['no'],
                'price' => $coinData['price'],
                'image' => $fileName,
            ]);
            $importedCount++;
        }
        return response()->json([
            'success' => true,
            'message' => "$importedCount coins imported successfully.",
            'errors' => $errors,
        ]);
    }
    public function downloadDemo()
    {
        $filePath = public_path('assets/demo-files/jewels.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'jewels_demo.xlsx');
        }

        abort(404, 'Demo Excel file not found.');
    }
}
