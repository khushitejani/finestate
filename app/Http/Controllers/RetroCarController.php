<?php

namespace App\Http\Controllers;

use App\Models\RetroCar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class RetroCarController extends Controller
{
    public function index()
    {
        $retro_cars = RetroCar::all();
        return view('retro_cars.index', compact('retro_cars'));
    }

    public function create()
    {
        return view('retro_cars.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no'         => 'nullable|numeric',
            'name'       => 'required|string|max:255',
            'start_year' => 'required|integer|min:1900|max:' . date('Y'),
            'end_year'   => 'required|integer|min:1900|max:' . date('Y'),
            'image'      => 'nullable|image|max:2048',
            'price'      => 'required|numeric',
        ]);

        $years = $request->start_year . '-' . $request->end_year;
        $data = $request->only('no', 'name', 'price');
        $data['years'] = $years;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('retro_cars', 'public');
        }

        RetroCar::create($data);
        return response()->json(['success' => true, 'message' => 'Retro Car created successfully']);
    }
    public function edit(RetroCar $retroCar)
    {
        return view('retro_cars.edit', compact('retroCar'));
    }
    public function update(Request $request, RetroCar $retroCar)
    {
        $request->validate([
            'no'         => 'nullable|numeric',
            'name'       => 'required|string|max:255',
            'start_year' => 'required|integer|min:1900|max:' . date('Y'),
            'end_year'   => 'required|integer|min:1900|max:' . date('Y'),
            'image'      => 'nullable|image|max:2048',
            'price'      => 'required|numeric',
        ]);

        $years = $request->start_year . '-' . $request->end_year;
        $data = $request->only('name', 'price', 'no');
        $data['years'] = $years;

        if ($request->hasFile('image')) {
            if ($retroCar->image) {
                Storage::disk('public')->delete($retroCar->image);
            }
            $data['image'] = $request->file('image')->store('retro_cars', 'public');
        }

        $retroCar->update($data);
        return response()->json(['success' => true, 'message' => 'Retro Car updated successfully']);
    }
    public function destroy(RetroCar $retroCar)
    {
        // if ($retroCar->image) {
        //     Storage::disk('public')->delete($retroCar->image);
        // }
        $retroCar->delete();

        return response()->json(['success' => true, 'message' => 'Retro Car deleted successfully']);
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
            $headers = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h))), $rawHeaders);

            $importedCount = 0;

            foreach ($rows as $row) {
                $rowData = @array_combine($headers, $row);
                if (!$rowData) continue;
                $rawNo = $rowData['no.'] ?? null;
                $no = (is_numeric($rawNo)) ? intval($rawNo) : null;
                $name      = $rowData['game_name_'] ?? 'Unknown';
                $year      = $rowData['year'] ?? null;
                $priceStr  = $rowData['game_price_(in_$)'] ?? '0';
                $imagePath = $rowData['link'] ?? null;

                if (empty($name) || empty($year)) continue;

                $price = floatval(str_replace(['$', ',', ' '], '', $priceStr));

                $data = [
                    'name'  => $name,
                    'no'    => $no,
                    'years' => $year,
                    'price' => $price,
                    'image' => $imagePath ? 'retro_cars/' . ltrim($imagePath, '/') : 'default.jpeg',
                ];

                RetroCar::create($data);
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
        $filePath = public_path('assets/demo-files/RetroCar.zip');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'Retro Car.zip');
        }

        abort(404, 'ZIP file not found.');
    }
}
