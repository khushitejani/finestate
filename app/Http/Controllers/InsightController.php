<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;


class InsightController extends Controller
{
    public function index()
    {
        $insights = Insight::latest()->get();
        return view('insights.index', compact('insights'));
    }

    public function create()
    {
        return view('insights.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no'         => 'nullable|numeric',
            'name'       => 'required|string|max:255',
            'years'      => 'required|string|max:20',
            'conditions' => 'nullable|string',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('insights', 'public');
        }

        Insight::create([
            'no'          => $request->no,
            'name'        => $request->name,
            'years'       => $request->years,
            'conditions'  => $request->conditions,
            'image'       => $image,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Insight created successfully!'
        ]);
    }

    public function edit(Insight $insight)
    {
        return view('insights.edit', compact('insight'));
    }
    public function show(Insight $insight) {}

    public function update(Request $request, Insight $insight)
    {
        $request->validate([
            'no'         => 'nullable|numeric',
            'name'       => 'required|string|max:255',
            'years'      => 'required|string|max:20',
            'conditions' => 'nullable|string',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($insight->image) {
                Storage::disk('public')->delete($insight->image);
            }
            $insight->image = $request->file('image')->store('insights', 'public');
        }

        $insight->update([
            'no'         => $request->no,
            'name'       => $request->name,
            'years'      => $request->years,
            'conditions' => $request->conditions,
            'image'      => $insight->image,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Insight updated successfully!'
        ]);
    }

    public function destroy(Insight $insight)
    {
        // if ($insight->image) {
        //     Storage::disk('public')->delete($insight->image);
        // }

        $insight->delete();

        return response()->json([
            'success' => true,
            'message' => 'Insight deleted successfully!'
        ]);
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xls,xlsx,csv',
        ]);

        $file = $request->file('import_file');
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'csv') {
            $data = array_map('str_getcsv', file($file->getRealPath()));
        } else {
            $spreadsheet = IOFactory::load($file->getPathname());
            $data = $spreadsheet->getActiveSheet()->toArray();
        }

        if (count($data) < 2) {
            return response()->json(['error' => 'File is empty or missing rows.'], 400);
        }

        $rawHeaders = array_shift($data);
        $headers = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h))), $rawHeaders);

        $importedCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                $rowData = array_combine($headers, $row);

                $insightData = [
                    'name'        => $rowData['name'] ?? null,
                    'no'          => $rowData['no_'] ?? ' ',
                    'years'       => $rowData['years'] ?? null,
                    'conditions'  => $rowData['conditions'] ?? null,
                ];

                $excelImage = $rowData['link'] ?? $rowData['link'] ?? null;
                $fileName = $excelImage
                    ? 'insights/' . ltrim($excelImage, '/')
                    : 'default.jpeg';

                $insightData['image'] = $fileName;

                Insight::create($insightData);
                $importedCount++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$importedCount insights imported successfully.",
            'errors'  => $errors,
        ]);
    }
    public function downloadDemo()
    {
        $filePath = public_path('assets/demo-files/Insights.zip');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'Insights.zip');
        }

        abort(404, 'ZIP file not found.');
    }
}
