<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BulkImportController extends Controller
{
    // public function upload(Request $request)
    // {
    //     // Validate file
    //     $request->validate([
    //         'import_file' => 'required|file|max:5120', // max 5MB
    //         'folder' => 'nullable|string'
    //     ]);

    //     $file = $request->file('import_file');
    //     $folder = $request->input('folder', ''); 


    //     $path = $file->storeAs($folder, $file->getClientOriginalName(), 'public');

    //     if ($path) {
    //         return response()->json([
    //             'success' => true,
    //             'path' => $path
    //         ]);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'File could not be saved.'
    //         ], 500);
    //     }
    // }

    public function upload(Request $request)
    {
        if ($request->hasFile('files')) {
            $folder = $request->input('folder', '');

            foreach ($request->file('files') as $index => $file) {
                $relativePath = $request->input("paths.$index");
                $savePath = trim("improvements/" . dirname($relativePath), '/');
                $file->storeAs($savePath, $file->getClientOriginalName(), 'public');
            }

            // ✅ Return one clean JSON object
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'No files found']);
    }

    // public function upload(Request $request)
    // {
    //     if ($request->hasFile('files')) {
    //         foreach ($request->file('files') as $index => $file) {
    //             $relativePath = $request->input("paths.$index");
    //             $folder = $request->input('folder', '');

    //             // Keep relative subfolder structure
    //             $savePath = trim("uploads/$folder/" . dirname($relativePath), '/');

    //             // Store file
    //             $file->storeAs($savePath, $file->getClientOriginalName(), 'public');
    //         }

    //         return response()->json(['success' => true]);
    //     }

    //     return response()->json(['success' => false, 'message' => 'No files found.']);
    // }



    public function folder(Request $request)
    {
        $folder = trim($request->input('folder', ''));
        $path = $folder ? "improvements/$folder" : 'improvements';

        $subfolders = Storage::disk('public')->directories($path);
        $files = Storage::disk('public')->files($path);

        $images = [];
        foreach ($files as $file) {
            $images[] = [
                'url' => asset('storage/' . $file),
                'name' => basename($file),
                'path' => $file,
            ];
        }

        return response()->json([
            'subfolders' => $subfolders,
            'images' => $images,
            'currentFolder' => $folder
        ]);
    }
}
