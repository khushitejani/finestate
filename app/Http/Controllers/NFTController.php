<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NFT;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;


class NFTController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Display all NFTs
    public function index()
    {
        $nfts = NFT::all();
        return view('nfts.index', compact('nfts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('nfts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no' => 'nullable|numeric',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->only(['no', 'name', 'price', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('nfts', 'public');
        }

        $nft = NFT::create($data);

        return response()->json([
            'success' => true,
            'message' => 'NFT created successfully!',
            'data' => $nft,
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
    public function edit(NFT $nft)
    {
        return view('nfts.edit', compact('nft'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NFT $nft)
    {
        $request->validate([
            'no' => 'nullable|numeric',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($nft->image && Storage::disk('public')->exists($nft->image)) {
                Storage::disk('public')->delete($nft->image);
            }
            $nft->image = $request->file('image')->store('nfts', 'public');
        }
        $nft->no = $request->no;
        $nft->name = $request->name;
        $nft->price = $request->price;
        $nft->description = $request->description;
        $nft->save();

        return response()->json([
            'success' => true,
            'message' => 'NFT updated successfully!',
            'data' => $nft,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NFT $nft)
    {
        // if ($nft->image && Storage::disk('public')->exists($nft->image)) {
        //     Storage::disk('public')->delete($nft->image);
        // }

        $nft->delete();

        return response()->json([
            'success' => true,
            'message' => 'NFT deleted successfully!',
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

            $spreadsheet =  IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            if (count($data) < 2) {
                return response()->json(['error' => 'File is empty or has no data'], 400);
            }

            $rawHeaders = array_shift($data);
            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace(' ', '_', $h)));
            }, $rawHeaders);

            $importedCount = 0;

            foreach ($data as $row) {
                $rowData = array_combine($headers, $row);

                $nftData = [
                    'no' => $rowData['no'] ?? null,
                    'name' => $rowData['name'] ?? null,
                    'price' => $rowData['price'] ?? 0,
                    'description' => $rowData['description'] ?? null,
                ];

                $excelImage = $rowData['link'] ?? $rowData['image'] ?? null;
                if ($excelImage) {
                    $fileName = 'nfts/' . ltrim($excelImage, '/');
                } else {
                    $fileName = 'default.jpeg';
                }
                $nftData['image'] = $fileName;

                NFT::create($nftData);
                $importedCount++;
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
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
        $filePath = public_path('assets/demo-files/NFT.zip');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'NFT.zip');
        }

        abort(404, 'ZIP file not found.');
    }
}
