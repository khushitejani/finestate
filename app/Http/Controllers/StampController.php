<?php

namespace App\Http\Controllers;

use App\Models\Stamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;


class StampController extends Controller
{
    public function index()
    {
        $stamps = Stamp::all();
        return view('stamps.index', compact('stamps'));
    }

    public function create()
    {
        return view('stamps.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no'    => 'nullable|numeric',
            'name'  => 'required|string|max:255',
            'years' => 'required|string|max:20',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only('no', 'name', 'price', 'years');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('stamps', 'public');
        }

        Stamp::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Stamp created successfully.'
        ]);
    }

    public function edit(Stamp $stamp)
    {
        return view('stamps.edit', compact('stamp'));
    }

    public function update(Request $request, Stamp $stamp)
    {
        $request->validate([
            'no'    => 'nullable|numeric',
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'years' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only('no', 'name', 'price', 'years');

        if ($request->hasFile('image')) {
            if ($stamp->image) {
                Storage::disk('public')->delete($stamp->image);
            }
            $data['image'] = $request->file('image')->store('stamps', 'public');
        }

        $stamp->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Stamp updated successfully.'
        ]);
    }

    public function destroy(Stamp $stamp)
    {
        // if ($stamp->image) {
        //     Storage::disk('public')->delete($stamp->image);
        // }
        $stamp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stamp deleted successfully.'
        ]);
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
                'no'    => $rowData['no_'] ?? null,
                'name'  => $rowData['name'] ?? null,
                'price' => $rowData['price'] ?? null,
                'years' => $rowData['years'] ?? null
            ];
            $fileName = null;
            $excelImage = $rowData['link'] ?? $rowData['image'] ?? null;
            if ($excelImage) {
                $fileName = 'stamps/' . ltrim($excelImage, '/');
            } else {
                $fileName = 'default.jpeg';
            }
            Stamp::create([
                'no'    => $coinData['no'],
                'name'  => $coinData['name'],
                'price' => $coinData['price'],
                'years'  => $coinData['years'],
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
        $filePath = public_path('assets/demo-files/Stamp.zip');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'Stamp.zip');
        }

        abort(404, 'ZIP file not found.');
    }
}
