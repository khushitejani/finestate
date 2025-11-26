<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Coin;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CoinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coins = Coin::all();
        return view('coins.index', compact('coins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('coins.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no'             => 'nullable|numeric',
            'name'          => 'required|string|max:255',
            'years'         => 'required|string|max:50',
            'price'         => 'required|numeric',
            'cropped_image' => 'nullable|string',
        ]);

        $fileName = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uniqueName = uniqid('coin_') . '.' . $file->getClientOriginalExtension();
            $fileName = $file->storeAs('coins', $uniqueName, 'public');
        }

        Coin::create([
            'no'    => $request->no,
            'name'  => $request->name,
            'years' => $request->years,
            'price' => $request->price,
            'image' => $fileName,
        ]);

        return response()->json(['success' => true, 'message' => 'Coin created successfully.']);
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
    public function edit(Coin $coin)
    {
        return view('coins.edit', compact('coin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coin $coin)
    {
        $request->validate([
            'no'    => 'nullable|numeric',
            'name'  => 'required|string|max:255',
            'years' => 'required|string|max:50',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $coin->no    = $request->no;
        $coin->name = $request->name;
        $coin->years = $request->years;
        $coin->price = $request->price;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($coin->image && Storage::disk('public')->exists($coin->image)) {
                Storage::disk('public')->delete($coin->image);
            }

            $file = $request->file('image');
            $uniqueName = uniqid('coin_') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('coins', $uniqueName, 'public');

            $coin->image = $filePath;
        }
        $coin->save();
        return response()->json(['success' => true, 'message' => 'Coin updated successfully.']);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $coin = Coin::findOrFail($id);
        // if ($coin->image && Storage::disk('public')->exists($coin->image)) {
        //     Storage::disk('public')->delete($coin->image);
        // }
        $coin->delete();
        return response()->json([
            'success' => true,
            'message' => 'Coin deleted successfully.'
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
                'name'  => $rowData['name'] ?? null,
                'no'    => $rowData['no_'] ?? null,
                'years' => $rowData['years'] ?? null,
                'price' => isset($rowData['price'])
                    ? floatval(str_replace([',', '$'], '', $rowData['price']))
                    : 0
            ];
            $fileName = null;
            $excelImage = $rowData['link'] ?? $rowData['link'] ?? null;
            if ($excelImage) {
                $fileName = 'coins/' . ltrim($excelImage, '/');
            } else {
                $fileName = 'default.jpeginde';
            }
            Coin::create([
                'name'  => $coinData['name'],
                'no'    => $coinData['no'],
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
        $filePath = public_path('assets/demo-files/coins.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'coins_demo.xlsx');
        }

        abort(404, 'Demo Excel file not found.');
    }
}
