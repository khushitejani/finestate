<?php

use App\Http\Controllers\AircraftShopController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CarshowroomController;
use App\Http\Controllers\CoinController;
use App\Http\Controllers\ImprovementController;
use App\Http\Controllers\IslandController;
use App\Http\Controllers\JewelledController;
use App\Http\Controllers\NFTController;
use App\Http\Controllers\PaintingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RetroCarController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\UniqueItemController;
use App\Http\Controllers\StampController;
use App\Http\Controllers\YatchShopController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptocurrencyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsightController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;


Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth:admin');

Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    //Admin-Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Cards
    Route::resource('cards', CardController::class);
    Route::resource('shares', ShareController::class);
    Route::resource('properties', PropertyController::class);
    Route::resource('improvements', ImprovementController::class);
    Route::resource('carshowrooms', CarshowroomController::class);
    Route::resource('aircraftshops', AircraftShopController::class);
    Route::resource('coins', CoinController::class);
    Route::resource('paintings', PaintingController::class);
    Route::resource('unique_items', UniqueItemController::class);
    Route::resource('retro_cars', RetroCarController::class);
    Route::resource('jewelleds', JewelledController::class);
    Route::resource('stamps', StampController::class);
    Route::resource('nfts', NFTController::class);
    Route::resource('islands', IslandController::class);
    Route::resource('yatchshop', YatchShopController::class);
    Route::resource('cryptos', CryptocurrencyController::class);
    Route::resource('insights', InsightController::class);

    // Route::get('/bulk-import-form', function (Request $request) {
    //     $folder = $request->get('folder', '');
    //     $directories = Storage::disk('public')->directories();

    //     $images = [];

    //     if ($folder && Storage::disk('public')->exists($folder)) {
    //         $files = Storage::disk('public')->files($folder);
    //     } else {
    //         $files = Storage::disk('public')->allFiles();
    //     }

    //     foreach ($files as $file) {
    //         if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
    //             $images[] = [
    //                 'url' => asset('storage/' . $file),
    //                 'path' => $file,
    //                 'folder' => Str::beforeLast($file, '/'),
    //                 'name' => basename($file),
    //             ];
    //         }
    //     }

    //     return view('bulk-import-form', compact('images', 'directories', 'fo lder'));
    // })->name('bulk.import.form');
    // Route::get('/bulk-import-form', function (Request $request) {
    //     $folder = $request->get('folder', '');

    //     $directories = Storage::disk('public')->directories();
    //     $subfolders = [];
    //     $images = [];

    //     if ($folder && Storage::disk('public')->exists($folder)) {
    //         $subfolders = Storage::disk('public')->directories($folder);
    //         $allFiles = Storage::disk('public')->allFiles($folder);

    //         foreach ($allFiles as $file) {
    //             if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
    //                 $images[] = [
    //                     'url' => asset('storage/' . $file),
    //                     'path' => $file,
    //                     'name' => basename($file),
    //                     'folder' => Str::beforeLast($file, '/')
    //                 ];
    //             }
    //         }
    //     }

    //     if (request()->ajax()) {
    //         return response()->json([
    //             'subfolders' => $subfolders,
    //             'images' => $images,
    //         ]);
    //     }

    //     return view('bulk-import-form', compact('directories', 'subfolders', 'images', 'folder'));
    // })->name('bulk.import.form');

    Route::get('/bulk-import-form', function (Request $request) {
        $folder = $request->get('folder', ''); // relative path inside storage/app/public

        // Get top-level directories
        $directories = Storage::disk('public')->directories();

        $subfolders = [];
        $images = [];

        if ($folder) {
            // Subfolders inside current folder
            $subfolders = Storage::disk('public')->directories($folder);

            // All files (including images) inside current folder
            $allFiles = Storage::disk('public')->files($folder);

            foreach ($allFiles as $file) {
                if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                    $images[] = [
                        'url' => asset('storage/' . $file),
                        'path' => $file,
                        'name' => basename($file),
                        'folder' => Str::beforeLast($file, '/')
                    ];
                }
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'subfolders' => $subfolders,
                'images' => $images,
            ]);
        }

        return view('bulk-import-form', compact('directories', 'subfolders', 'images', 'folder'));
    })->name('bulk.import.form');

    Route::post('/shares/bulk-import', [ShareController::class, 'bulkImport'])->name('share.bulk.import');
    Route::post('/properties/bulk-import', [PropertyController::class, 'bulkImport'])->name('properties.bulk.import');
    Route::post('/yachts/bulk-import', [YatchShopController::class, 'bulkImport'])->name('yatch.bulk.import');
    Route::post('/islands/bulk-import', [IslandController::class, 'bulkImport'])->name('islands.bulk.import');
    Route::post('aircraftshops/bulk-import', [AircraftShopController::class, 'bulkImport'])->name('aircraftshops.bulkImport');
    Route::post('carshowrooms/bulk-import', [CarshowroomController::class, 'bulkImport'])->name('carshowrooms.bulk.import');
    Route::post('/retro-cars/bulk-import', [RetroCarController::class, 'bulkImport'])->name('retro_cars.bulk.import');
    Route::post('/coins/bulk-import', [CoinController::class, 'bulkImport'])->name('coins.bulk.import');
    Route::post('/bulk-import', [CardController::class, 'bulkImport'])->name('cards.bulk.import');
    Route::post('/paintings/bulk-import', [PaintingController::class, 'bulkImport'])->name('paintings.bulk.import');
    Route::post('/unique-items/bulk-import', [UniqueItemController::class, 'bulkImport'])->name('unique_items.bulk.import');
    Route::post('/jewelleds/bulk-import', [JewelledController::class, 'bulkImport'])->name('jewelleds.bulk.import');
    Route::post('/stamps/bulk-import', [StampController::class, 'bulkImport'])->name('stamps.bulk.import');
    Route::post('/insights/bulk-import', [InsightController::class, 'bulkImport'])->name('insights.bulk.import');
    Route::post('/improvements/bulk-import', [ImprovementController::class, 'bulkImport'])->name('improvements.bulk.import');
    Route::get('/cards/demo/download', [CardController::class, 'downloadDemo'])->name('cards.demo.download');
    Route::get('/shares/demo/download', [ShareController::class, 'downloadDemo'])->name('shares.demo.download');
    Route::get('/properties/demo/download', [PropertyController::class, 'downloadDemo'])->name('properties.demo.download');
    Route::get('/improvements/demo/download', [ImprovementController::class, 'downloadDemo'])->name('improvements.demo.download');
    Route::get('/carshowrooms/demo/download', [CarshowroomController::class, 'downloadDemo'])->name('carshowrooms.demo.download');
    Route::get('/aircraftshops/demo/download', [AircraftShopController::class, 'downloadDemo'])->name('aircraftshops.demo.download');
    Route::get('/coins/demo/download', [CoinController::class, 'downloadDemo'])->name('coins.demo.download');
    Route::get('/paintings/demo/download', [PaintingController::class, 'downloadDemo'])->name('paintings.demo.download');
    Route::get('/unique-items/demo/download', [UniqueItemController::class, 'downloadDemo'])->name('unique_items.demo.download');
    Route::get('/retro-cars/demo/download', [RetroCarController::class, 'downloadDemo'])->name('retro_cars.demo.download');
    Route::get('/islands/demo/download', [IslandController::class, 'downloadDemo'])->name('islands.demo.download');
    Route::get('/jewels/demo/download', [JewelledController::class, 'downloadDemo'])->name('jewelleds.demo.download');
    Route::get('/stamps/demo/download', [StampController::class, 'downloadDemo'])->name('stamps.demo.download');
    Route::get('/yatch-shops/demo/download', [YatchShopController::class, 'downloadDemo'])->name('yatch_shops.demo.download');
    Route::get('/insights/demo/download', [InsightController::class, 'downloadDemo'])->name('insights.demo.download');
    Route::get('/storage/images', [InsightController::class, 'showAllImages'])->name('storage.images');














    // Route::get('/demo', [ShareController::class, 'intradayChart']);
    // Route::get('/chart', function () {
    //     return view('demo');
    // });
    Route::fallback(function () {
        return redirect()->route('login');
    });
});

require __DIR__ . '/auth.php';
