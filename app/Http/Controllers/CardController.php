<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cards = Card::latest()->get();
        return view('cards.index', compact('cards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cards.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no'            => 'nullable|numeric',
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric',
            'sign_price'    => 'required|string|max:5',
            'image'      => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
        $fileName = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uniqueName = uniqid('card_') . '.' . $file->getClientOriginalExtension();
            $fileName = $file->storeAs('cards', $uniqueName, 'public');
        }
        $card = new Card();
        $card->no         = $request->no;
        $card->name       = $request->name;
        $card->price      = $request->price;
        $card->sign_price = $request->sign_price;
        $card->image      = $fileName;
        $card->save();
        return response()->json([
            'success' => true,
            'message' => 'Card created successfully.'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $card = Card::findOrFail($id);

        return view('cards.edit', compact('card'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        $request->validate([
            'no'            => 'nullable|numeric',
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric',
            'sign_price'    => 'required|string|max:5',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $card->no         = $request->no;
        $card->name       = $request->name;
        $card->price      = $request->price;
        $card->sign_price = $request->sign_price;

        if ($request->hasFile('image')) {
            if ($card->image && Storage::disk('public')->exists($card->image)) {
                Storage::disk('public')->delete($card->image);
            }
            $file = $request->file('image');
            $uniqueName = uniqid('card_') . '.' . $file->getClientOriginalExtension();
            $fileName = $file->storeAs('cards', $uniqueName, 'public');

            $card->image = $fileName;
        }
        $card->save();

        return response()->json([
            'success' => true,
            'message' => 'Card updated successfully.',
            'card' => $card,
        ]);
    }


    public function destroy(Card $card)
    {
        // if ($card->image && Storage::disk('public')->exists($card->image)) {
        //     Storage::disk('public')->delete($card->image);
        // }

        $card->delete();

        return response()->json([
            'success' => true,
            'message' => 'Card deleted successfully.',
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
            return response()->json(['error' => 'File is empty or missing data'], 400);
        }

        $rawHeaders = array_shift($data);
        $headers = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h))), $rawHeaders);

        $importedCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            if (count($row) < count($headers)) continue;

            $rowData = array_combine($headers, $row);

            $excelImage = $rowData['link'] ?? $rowData['link'] ?? null;

            if ($excelImage) {
                $fileName = 'cards/' . ltrim($excelImage, '/');
            } else {
                $fileName = 'default.jpeg';
            }

            try {
                Card::create([
                    'name'        => $rowData['name'] ?? ' ',
                    'no'          => $rowData['no_'] ?? ' ',
                    'price'       => $rowData['price'] ?? 0,
                    'sign_price'  => $rowData['sign_price'] ?? '$',
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
        $filePath = public_path('/demo-files/cards.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'cards_demo.xlsx');
        }

        abort(404, 'Demo Excel file not found.');
    }
}
