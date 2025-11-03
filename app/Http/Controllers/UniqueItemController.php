<?php

namespace App\Http\Controllers;

use App\Models\UniqueItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UniqueItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unique_items = UniqueItem::all();
        return view('unique_items.index', compact('unique_items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('unique_items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255|unique:unique_items,name',
            'years'         => 'required|string|max:20',
            'price'         => 'required|numeric',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uniqueName = uniqid('unique_item_') . '.' . $file->getClientOriginalExtension();
            $fileName = $file->storeAs('unique_items', $uniqueName, 'public');
        }

        UniqueItem::create([
            'name'  => $request->name,
            'years' => $request->years,
            'price' => $request->price,
            'image' => $fileName,
        ]);

        return response()->json(['success' => true, 'message' => 'Item created successfully.']);
    }
    /**
     * Display the specified resource.
     */
    public function show(UniqueItem $uniqueItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UniqueItem $uniqueItem)
    {
        return view('unique_items.edit', compact('uniqueItem'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UniqueItem $uniqueItem)
    {
        $request->validate([
            'name'          => 'required|string|max:255|unique:unique_items,name,' . $uniqueItem->id,
            'years'         => 'required|string|max:20',
            'price'         => 'required|numeric',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name'  => $request->name,
            'years' => $request->years,
            'price' => $request->price,
        ];
        if ($request->hasFile('image')) {
            if ($uniqueItem->image && Storage::disk('public')->exists($uniqueItem->image)) {
                Storage::disk('public')->delete($uniqueItem->image);
            }

            $file = $request->file('image');
            $uniqueName = uniqid('unique_item_') . '.' . $file->getClientOriginalExtension();
            $fileName = $file->storeAs('unique_items', $uniqueName, 'public');
            $data['image'] = $fileName;
        }
        $uniqueItem->update($data);
        return response()->json(['success' => true, 'message' => 'Item updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = UniqueItem::findOrFail($id);
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();

        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
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
                $fileName = 'unique_items/' . ltrim($excelImage, '/');
            } else {
                $fileName = 'default.jpeg';
            }
            UniqueItem::create([
                'name'  => $coinData['name'],
                'years' => $coinData['years'],
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
        $filePath = public_path('assets/demo-files/unique_items.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'unique_items_demo.xlsx');
        }

        abort(404, 'Demo Excel file not found.');
    }
}
