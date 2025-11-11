<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class BulkImportController extends Controller
{
    public function upload(Request $request)
    {
        Log::info('Upload started.');

        $folder = trim($request->input('folder', ''), '/');
        $paths = $request->input('paths', []);
        $files = $request->file('files');

        if (!$files || count($files) === 0) {
            return response()->json(['error' => 'No files found'], 400);
        }

        // Determine root folder name from first file path
        $firstPath = $paths[0] ?? $files[0]->getClientOriginalName();
        $rootFolderName = explode('/', trim($firstPath, '/'))[0] ?? 'newfolder';

        // Full path where the new folder should go
        $destinationRoot = $folder ? $folder . '/' . $rootFolderName : $rootFolderName;
        $destinationRoot = trim($destinationRoot, '/');
        $fullDestinationPath = Storage::disk('public')->path($destinationRoot);

        // Auto-rename folder if it already exists at this level
        $baseFolder = $destinationRoot;
        $counter = 1;
        while (File::exists($fullDestinationPath)) {
            $destinationRoot = $baseFolder . "($counter)";
            $fullDestinationPath = Storage::disk('public')->path($destinationRoot);
            $counter++;
        }

        // Create the new root folder
        File::makeDirectory($fullDestinationPath, 0755, true);
        Log::info("Created folder: {$fullDestinationPath}");

        $uploadedPaths = [];

        foreach ($files as $index => $file) {
            $relativePath = $paths[$index] ?? $file->getClientOriginalName();
            $relativePath = trim($relativePath, '/');

            // Remove the original root folder prefix
            $relativeParts = explode('/', $relativePath);
            if (count($relativeParts) > 1) array_shift($relativeParts);
            $relativeFilePath = implode('/', $relativeParts);

            $destinationPath = $destinationRoot . '/' . dirname($relativeFilePath);
            $destinationPath = trim($destinationPath, '/');

            // Create nested folders if they don't exist
            $fullNestedPath = Storage::disk('public')->path($destinationPath);
            if (!File::exists($fullNestedPath)) {
                File::makeDirectory($fullNestedPath, 0755, true);
                Log::info("Created nested folder: {$fullNestedPath}");
            }

            // Handle file name conflicts
            $fileName = basename($relativeFilePath);
            $fileBase = pathinfo($fileName, PATHINFO_FILENAME);
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $finalPath = $destinationPath ? $destinationPath . '/' . $fileName : $fileName;
            $fileCounter = 1;

            while (File::exists(Storage::disk('public')->path($finalPath))) {
                $fileName = $fileBase . "($fileCounter)." . $fileExt;
                $finalPath = $destinationPath ? $destinationPath . '/' . $fileName : $fileName;
                $fileCounter++;
            }

            Storage::disk('public')->putFileAs($destinationPath, $file, $fileName);
            $uploadedPaths[] = $finalPath;

            Log::info("Uploaded file: {$finalPath}");
        }

        // Get all subfolders & files inside the root folder
        $subfolders = Storage::disk('public')->directories($destinationRoot);
        $allFiles = Storage::disk('public')->files($destinationRoot);

        $items = [];
        foreach ($allFiles as $file) {
            $items[] = [
                'url' => asset('storage/' . $file),
                'name' => basename($file),
                'path' => $file,
            ];
        }

        Log::info('Upload finished.', ['uploaded_files' => $uploadedPaths]);

        return response()->json([
            'message' => 'Files uploaded successfully!',
            'uploaded_files' => $uploadedPaths,
            'subfolders' => $subfolders,
            'images' => $items,
            'currentFolder' => $destinationRoot,
        ]);
    }

    public function folder(Request $request)
    {
        $folder = trim($request->input('folder', ''), '/');
        Log::info("Listing folder: {$folder}");

        $subfolders = Storage::disk('public')->directories($folder);

        $allFiles = Storage::disk('public')->allFiles($folder);

        $items = [];
        foreach ($allFiles as $file) {
            $items[] = [
                'url' => asset('storage/' . $file),
                'name' => basename($file),
                'path' => $file,
            ];
        }

        return response()->json([
            'subfolders' => $subfolders,
            'images' => $items,
            'currentFolder' => $folder,
        ]);
    }

    public function move(Request $request)
    {
        $items = $request->input('items', []);
        $target = trim($request->input('target', ''), '/');
        Log::info('Move request received.', compact('items', 'target'));

        if (empty($items)) {
            return response()->json(['error' => 'No items selected to move'], 400);
        }

        foreach ($items as $item) {
            $source = trim($item, '/');
            $destination = $target ? $target . '/' . basename($source) : basename($source);

            $destinationDir = dirname(Storage::disk('public')->path($destination));
            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
                Log::info("Created destination directory: {$destinationDir}");
            }
            $baseName = basename($source);
            $nameWithoutExt = pathinfo($baseName, PATHINFO_FILENAME);
            $extension = pathinfo($baseName, PATHINFO_EXTENSION);

            $finalDestination = $destination;
            $counter = 1;
            while (Storage::disk('public')->exists($finalDestination)) {
                $newName = $nameWithoutExt . "($counter)" . ($extension ? ".{$extension}" : '');
                $finalDestination = ($target ? $target . '/' : '') . $newName;
                $counter++;
            }

            Storage::disk('public')->move($source, $finalDestination);
            Log::info("Moved: {$source} → {$finalDestination}");
        }

        return response()->json([
            'message' => 'Items moved successfully!',
            'moved_items' => $items,
            'target' => $target,
        ]);
    }

    public function delete(Request $request)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {
            if (Storage::disk('public')->exists($item)) {
                if (is_dir(Storage::disk('public')->path($item))) {
                    Storage::disk('public')->deleteDirectory($item);
                } else {
                    Storage::disk('public')->delete($item);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Rename a folder or file
     */
    public function rename(Request $request)
    {
        $path = $request->input('path');
        $newName = $request->input('newName');

        if (!$path || !$newName) {
            return response()->json(['success' => false, 'message' => 'Invalid parameters']);
        }

        $parts = explode('/', $path);
        array_pop($parts); // remove old name
        $newPath = implode('/', $parts) . '/' . $newName;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->move($path, $newPath);
        }

        return response()->json(['success' => true]);
    }
}
