<?php

namespace App\Http\Controllers;

use App\Models\ForbsSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ForbsSlotController extends Controller
{
    public function index()
    {
        $forbs_slots = ForbsSlot::latest()->get();
        return view('forbs_slots.index', compact('forbs_slots'));
    }

    public function create()
    {
        return view('forbs_slots.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'no'       => 'nullable|string|max:255',
            'business' => 'nullable|string|max:255',
            'price'    => 'nullable|numeric',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = $request->all();

        // Upload Image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('forbs_slots', 'public');
        }

        ForbsSlot::create($data);

        return response()->json(['success' => true, 'message' => 'Forbs Slot created successfully']);
    }

    public function edit($id)
    {
        $forbsSlot = ForbsSlot::findOrFail($id); // Make sure to use the model
        return view('forbs_slots.edit', compact('forbsSlot'));
    }

    public function update(Request $request, $id)
    {
        $slot = ForbsSlot::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'no'       => 'nullable|string|max:255',
            'business' => 'nullable|string|max:255',
            'price'    => 'nullable|numeric',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = $request->all();

        // Replace Image
        if ($request->hasFile('image')) {
            if ($slot->image && Storage::disk('public')->exists($slot->image)) {
                Storage::disk('public')->delete($slot->image);
            }
            $data['image'] = $request->file('image')->store('forbs_slots', 'public');
        }

        $slot->update($data);

        return response()->json(['success' => true, 'message' => 'Forbs Slot updated successfully']);
    }

    public function destroy($id)
    {
        $slot = ForbsSlot::findOrFail($id);
        $slot->delete();

        return response()->json(['success' => true, 'message' => 'Forbs Slot deleted successfully']);
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

            $rawHeaders = array_shift($rows);
            $headers = array_map(function ($h) {
                return strtolower(trim(str_replace([' ', '.', '-', '(', ')', '$'], ['_', '_', '_', '', '', ''], $h)));
            }, $rawHeaders);

            $importedCount = 0;

            foreach ($rows as $row) {
                $rowData = array_combine($headers, $row);

                $name = $rowData['name'] ?? 'Unknown';
                $no = $rowData['no'] ?? null;
                $business = $rowData['business'] ?? null;
                $price = isset($rowData['price']) ? floatval(str_replace(['$', ',', '₹'], '', $rowData['price'])) : 0;
                $image = $rowData['image'] ?? null; 

                $data = [
                    'name' => $name,
                    'no' => $no,
                    'business' => $business,
                    'price' => $price,
                    'image' => $image
                ];

                ForbsSlot::create($data);
                $importedCount++;
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => "{$importedCount} Forbs Slots imported successfully!",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // --------- DEMO FILE DOWNLOAD ---------
    public function downloadDemo()
    {
        $filePath = public_path('assets/demo-files/ForbsSlotsDemo.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'ForbsSlotsDemo.xlsx');
        }

        abort(404, 'Demo file not found.');
    }
}
