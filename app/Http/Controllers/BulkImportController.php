<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class BulkImportController extends Controller
{
    public function Index(Request $request)
    {
        $subfolders = Storage::disk('public')->directories();
        return view('bulk-import-form', compact('subfolders'));
    }

    public function getAllFolders(Request $request)
    {
        $folder = $request->get('folder', '');
        $disk = Storage::disk('public');

        $subfolders = [];
        $images = [];

        if (empty($folder)) {
            $subfolders = $disk->directories('');
        } elseif ($disk->exists($folder)) {
            $subfolders = $disk->directories($folder);

            $files = $disk->files($folder);
            foreach ($files as $file) {
                if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                    $images[] = [
                        'url' => asset('storage/' . $file),
                        'name' => basename($file),
                        'path' => $file,
                    ];
                }
            }
        }

        return response()->json([
            'subfolders' => $subfolders,
            'images' => $images,
            'currentFolder' => $folder,
        ]);
    }
    public function upload(Request $request)
    {
        $folder = trim($request->input('folder', ''), '/');
        $paths = $request->input('paths', []);
        $files = $request->file('files');

        if (!$files || count($files) === 0) {
            return response()->json(['error' => 'No files found'], 400);
        }

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

    /**
     * List files and folders inside a folder.
     */
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

    /**
     * Move selected items to target folder.
     */
    public function move(Request $request)
    {
        $items = $request->input('items', []);
        $target = trim($request->input('target', ''), '/');
        if (empty($items)) {
            return response()->json(['error' => 'No items selected to move'], 400);
        }

        foreach ($items as $item) {
            $source = trim($item, '/');
            $sourcePath = Storage::disk('public')->path($source);
            $basename = basename($source);

            $destination = $target ? $target . '/' . $basename : $basename;
            $destinationPath = Storage::disk('public')->path($destination);

            $destinationDir = dirname($destinationPath);
            if (!File::exists($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }


            if (is_dir($sourcePath)) {
                $finalDestination = $destinationPath;
                $counter = 1;
                while (File::exists($finalDestination)) {
                    $finalDestination = $destinationDir . '/' . $basename . "($counter)";
                    $counter++;
                }
                File::moveDirectory($sourcePath, $finalDestination);
            } else {
                $nameWithoutExt = pathinfo($basename, PATHINFO_FILENAME);
                $extension = pathinfo($basename, PATHINFO_EXTENSION);
                $finalDestination = $destinationPath;
                $counter = 1;
                while (File::exists($finalDestination)) {
                    $finalDestination = $destinationDir . '/' . $nameWithoutExt . "($counter)" . ($extension ? ".{$extension}" : '');
                    $counter++;
                }
                File::move($sourcePath, $finalDestination);
            }
        }

        return response()->json([
            'message' => 'Items moved successfully!',
            'moved_items' => $items,
            'target' => $target,
        ]);
    }

    /**
     * Delete selected files or folders.
     */
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
     * Rename a file or folder.
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
    /**
     * Paste selected files or folders to the target folder.
     */
    // public function paste(Request $request)
    // {
    //     $items = $request->input('items', []);
    //     $targetFolder = trim($request->input('target', ''), '/');

    //     foreach ($items as $itemPath) {
    //         $source = trim($itemPath, '/');
    //         $basename = basename($source);
    //         $destination = $targetFolder ? $targetFolder . '/' . $basename : $basename;

    //         if ($targetFolder === $source || str_starts_with($targetFolder, $source . '/')) {
    //             continue;
    //         }

    //         $counter = 1;
    //         $originalDestination = $destination;
    //         while (Storage::disk('public')->exists($destination)) {
    //             $fileBase = pathinfo($basename, PATHINFO_FILENAME);
    //             $fileExt = pathinfo($basename, PATHINFO_EXTENSION);
    //             $fileExt = $fileExt ? '.' . $fileExt : '';
    //             $destination = $targetFolder
    //                 ? $targetFolder . '/' . $fileBase . "($counter)" . $fileExt
    //                 : $fileBase . "($counter)" . $fileExt;
    //             $counter++;
    //         }

    //         if (Storage::disk('public')->exists($source)) {
    //             if (is_dir(Storage::disk('public')->path($source))) {
    //                 File::copyDirectory(
    //                     Storage::disk('public')->path($source),
    //                     Storage::disk('public')->path($destination)
    //                 );
    //             } else {
    //                 File::copy(
    //                     Storage::disk('public')->path($source),
    //                     Storage::disk('public')->path($destination)
    //                 );
    //             }
    //         }
    //     }

    //     return response()->json(['success' => true]);
    // }

    public function paste(Request $request)
    {
        $items = $request->input('items', []);
        $targetFolder = trim($request->input('target', ''), '/');

        foreach ($items as $itemPath) {
            $source = trim($itemPath, '/');
            $basename = basename($source);
            $destination = $targetFolder ? $targetFolder . '/' . $basename : $basename;

            if ($targetFolder === $source || str_starts_with($targetFolder, $source . '/')) {
                continue;
            }
            $counter = 1;
            $originalDestination = $destination;
            while (Storage::disk('public')->exists($destination)) {
                $fileBase = pathinfo($basename, PATHINFO_FILENAME);
                $fileExt = pathinfo($basename, PATHINFO_EXTENSION);
                $fileExt = $fileExt ? '.' . $fileExt : '';
                $destination = $targetFolder
                    ? $targetFolder . '/' . $fileBase . "($counter)" . $fileExt
                    : $fileBase . "($counter)" . $fileExt;
                $counter++;
            }

            $fullSource = Storage::disk('public')->path($source);
            $fullDestination = Storage::disk('public')->path($destination);

            if (is_dir($fullSource)) {
                File::copyDirectory($fullSource, $fullDestination);
            } elseif (file_exists($fullSource)) {
                File::copy($fullSource, $fullDestination);
            }
        }

        return response()->json(['success' => true]);
    }
    public function createFolder(Request $request)
    {
        $currentFolder = trim($request->input('folder', ''), '/');
        $newFolderName = trim($request->input('name'));

        if (!$newFolderName) {
            return response()->json([
                'success' => false,
                'message' => 'Folder name is required'
            ], 400);
        }

        $newFolderPath = $currentFolder ? $currentFolder . '/' . $newFolderName : $newFolderName;
        $newFolderPath = trim($newFolderPath, '/');

        $originalPath = $newFolderPath;
        $counter = 1;
        while (Storage::disk('public')->exists($newFolderPath)) {
            $newFolderPath = $originalPath . "($counter)";
            $counter++;
        }
        Storage::disk('public')->makeDirectory($newFolderPath, 0755, true);
        return response()->json([
            'success' => true,
            'folder' => $newFolderPath,
            'message' => 'Folder created successfully'
        ]);
    }
}
