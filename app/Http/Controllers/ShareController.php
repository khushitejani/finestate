<?php

namespace App\Http\Controllers;

use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ShareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shares = Share::latest()->get();
        return view('shares.index', compact('shares'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('shares.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'no' => 'nullable|numeric',
                'name' => 'required|string|max:255',
                'share_price' => 'required|numeric',
                'dividend' => 'required|numeric',
                'time_period' => 'required|numeric',
                'capitalization' => 'required|numeric',
                'available_shares' => 'required|numeric',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'day_prices_input' => 'nullable|string',
            ]);
            $fileName = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $uniqueName = uniqid('share_') . '.' . $file->getClientOriginalExtension();
                $fileName = $file->storeAs('shares', $uniqueName, 'public');
            }
            $dayPrices = [];
            if ($request->filled('day_prices_input')) {
                $dayPrices = array_map('floatval', explode(',', $request->day_prices_input));
            }
            $share = Share::create([
                'no' => $request->input('no'),
                'name' => $request->input('name'),
                'share_price' => $request->input('share_price'),
                'dividend' => $request->input('dividend'),
                'time_period' => $request->input('time_period'),
                'capitalization' => $request->input('capitalization'),
                'available_shares' => $request->input('available_shares'),
                'image' => $fileName,
                'day_prices'       => ['base' => $dayPrices],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Share created successfully.',
                'data' => $share,
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
    public function edit(string $id)
    {
        $share = Share::findOrFail($id);

        return view('shares.edit', compact('share'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $share = Share::findOrFail($id);
            $request->validate([
                'no' => 'nullable|numeric',
                'name' => 'required|string|max:255',
                'share_price' => 'required|numeric',
                'dividend' => 'required|numeric',
                'time_period' => 'required|numeric',
                'capitalization' => 'required|numeric',
                'available_shares' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'day_prices_input' => 'nullable|string',
            ]);

            $data = $request->only([
                'no',
                'name',
                'share_price',
                'dividend',
                'time_period',
                'capitalization',
                'available_shares',
            ]);
            if ($request->hasFile('image')) {
                if ($share->image && Storage::disk('public')->exists($share->image)) {
                    Storage::disk('public')->delete($share->image);
                }
                $file = $request->file('image');
                $uniqueName = uniqid('share_') . '.' . $file->getClientOriginalExtension();
                $fileName = $file->storeAs('shares', $uniqueName, 'public');
                $data['image'] = $fileName;
            }

            if ($request->filled('day_prices_input')) {
                $newPrices = array_map('floatval', explode(',', $request->day_prices_input));
                $data['day_prices'] = [
                    'base' => $newPrices
                ];
            }
            $share->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Share updated successfully.',
                'data' => $share
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating share.',
                'error' => $e->getMessage()
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $share = Share::find($id);

        if (!$share) {
            return response()->json([
                'ok' => false,
                'message' => 'Share not found.',
            ], 404);
        }


        // if ($share->image && Storage::disk('public')->exists($share->image)) {
        //     Storage::disk('public')->delete($share->image);
        // }

        $share->delete();

        return response()->json([
            'success' => true,
            'message' => 'Share deleted successfully.',
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

            // Normalize headers
            $rawHeaders = array_shift($rows);
            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace([' ', '.', '-'], '_', $h)));
            }, $rawHeaders);

            $importedCount = 0;

            foreach ($rows as $row) {
                $rowData = array_combine($headers, $row);

                $no = $rowData['no'] ?? null;
                $name = $rowData['game_name'] ?? $rowData['name'] ?? 'Unknown';
                $price = isset($rowData['price_in_$']) ? floatval(str_replace(['$', ','], '', $rowData['price_in_$'])) : 0;
                $dividend = isset($rowData['dividend_in_%']) ? floatval(str_replace(['%', ','], '', $rowData['dividend_in_%'])) / 100 : 0;
                $capitalization = isset($rowData['company_capitalization']) ? floatval(str_replace(['$', ','], '', $rowData['company_capitalization'])) : 0;
                $availableShares = isset($rowData['number_of_available_shares']) ? intval($rowData['number_of_available_shares']) : 0;

                $logoPath = $rowData['link'] ?? null;
                $storedImagePath = $logoPath ? 'shares/' . ltrim($logoPath, '/') : 'default.jpeg';

                Share::create([
                    'no'                => $no,
                    'name'              => $name,
                    'share_price'       => $price,
                    'dividend'          => $dividend,
                    'time_period'       => 3,
                    'capitalization'    => $capitalization,
                    'available_shares'  => $availableShares,
                    'image'             => $storedImagePath,
                ]);
                $importedCount++;
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
        $filePath = public_path('assets/demo-files/Share.zip');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'Share.zip');
        }

        abort(404, 'ZIP file not found.');
    }
}
