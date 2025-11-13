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
use App\Http\Controllers\BulkImportController;
use App\Http\Controllers\BusinessTaxiController;

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
    Route::post('/bulk-import', [CardController::class, 'bulkImport'])->name('cards.bulk.import');
    Route::get('/cards/demo/download', [CardController::class, 'downloadDemo'])->name('cards.demo.download');

    //shares
    Route::resource('shares', ShareController::class);
    Route::post('/shares/bulk-import', [ShareController::class, 'bulkImport'])->name('share.bulk.import');
    Route::get('/shares/demo/download', [ShareController::class, 'downloadDemo'])->name('shares.demo.download');

    //properties
    Route::resource('properties', PropertyController::class);
    Route::post('/properties/bulk-import', [PropertyController::class, 'bulkImport'])->name('properties.bulk.import');
    Route::get('/properties/demo/download', [PropertyController::class, 'downloadDemo'])->name('properties.demo.download');

    //improvements
    Route::resource('improvements', ImprovementController::class);
    Route::post('/improvements/bulk-import', [ImprovementController::class, 'bulkImport'])->name('improvements.bulk.import');
    Route::get('/improvements/demo/download', [ImprovementController::class, 'downloadDemo'])->name('improvements.demo.download');

    // carshowrooms
    Route::resource('carshowrooms', CarshowroomController::class);
    Route::post('carshowrooms/bulk-import', [CarshowroomController::class, 'bulkImport'])->name('carshowrooms.bulk.import');
    Route::get('/carshowrooms/demo/download', [CarshowroomController::class, 'downloadDemo'])->name('carshowrooms.demo.download');


    // aircraftshops
    Route::resource('aircraftshops', AircraftShopController::class);
    Route::get('/aircraftshops/demo/download', [AircraftShopController::class, 'downloadDemo'])->name('aircraftshops.demo.download');
    Route::post('aircraftshops/bulk-import', [AircraftShopController::class, 'bulkImport'])->name('aircraftshops.bulkImport');

    // coins
    Route::resource('coins', CoinController::class);
    Route::post('/coins/bulk-import', [CoinController::class, 'bulkImport'])->name('coins.bulk.import');
    Route::get('/coins/demo/download', [CoinController::class, 'downloadDemo'])->name('coins.demo.download');

    // paintings
    Route::resource('paintings', PaintingController::class);
    Route::get('/paintings/demo/download', [PaintingController::class, 'downloadDemo'])->name('paintings.demo.download');
    Route::post('/paintings/bulk-import', [PaintingController::class, 'bulkImport'])->name('paintings.bulk.import');


    // unique_items
    Route::resource('unique_items', UniqueItemController::class);
    Route::post('/unique-items/bulk-import', [UniqueItemController::class, 'bulkImport'])->name('unique_items.bulk.import');
    Route::get('/unique-items/demo/download', [UniqueItemController::class, 'downloadDemo'])->name('unique_items.demo.download');

    // retro_cars
    Route::resource('retro_cars', RetroCarController::class);
    Route::post('/retro-cars/bulk-import', [RetroCarController::class, 'bulkImport'])->name('retro_cars.bulk.import');
    Route::get('/retro-cars/demo/download', [RetroCarController::class, 'downloadDemo'])->name('retro_cars.demo.download');

    // jewelleds
    Route::resource('jewelleds', JewelledController::class);
    Route::post('/jewelleds/bulk-import', [JewelledController::class, 'bulkImport'])->name('jewelleds.bulk.import');
    Route::get('/jewels/demo/download', [JewelledController::class, 'downloadDemo'])->name('jewelleds.demo.download');

    // stamps
    Route::resource('stamps', StampController::class);
    Route::post('/stamps/bulk-import', [StampController::class, 'bulkImport'])->name('stamps.bulk.import');
    Route::get('/stamps/demo/download', [StampController::class, 'downloadDemo'])->name('stamps.demo.download');

    // nfts
    Route::resource('nfts', NFTController::class);

    // islands
    Route::resource('islands', IslandController::class);
    Route::post('/islands/bulk-import', [IslandController::class, 'bulkImport'])->name('islands.bulk.import');
    Route::get('/islands/demo/download', [IslandController::class, 'downloadDemo'])->name('islands.demo.download');

    // yatchshop
    Route::resource('yatchshop', YatchShopController::class);
    Route::post('/yachts/bulk-import', [YatchShopController::class, 'bulkImport'])->name('yatch.bulk.import');
    Route::get('/yatch-shops/demo/download', [YatchShopController::class, 'downloadDemo'])->name('yatch_shops.demo.download');

    // cryptos
    Route::resource('cryptos', CryptocurrencyController::class);

    // insights
    Route::resource('insights', InsightController::class);
    Route::post('/insights/bulk-import', [InsightController::class, 'bulkImport'])->name('insights.bulk.import');
    Route::get('/insights/demo/download', [InsightController::class, 'downloadDemo'])->name('insights.demo.download');

    // business-taxis
    Route::resource('business-taxis', BusinessTaxiController::class);
    Route::post('/businesstaxis/bulk-import', [BusinessTaxiController::class, 'bulkImport'])->name('business-taxis.bulk.import');
    Route::get('/businesstaxis/demo-download', [BusinessTaxiController::class, 'downloadDemo'])->name('business-taxis.demo.download');

    // BulkImport
    Route::get('/bulk-import-modal', [BulkImportController::class, 'Index'])->name('bulk.import.form');
    Route::get('/bulk-import-folder', [BulkImportController::class, 'getAllFolders'])->name('bulk.import.folder');
    Route::post('/bulk-import/upload', [BulkImportController::class, 'upload'])->name('bulk.import.upload');
    Route::get('/bulk-import/list', [BulkImportController::class, 'list'])->name('bulk.import.list');
    Route::post('/move', [BulkImportController::class, 'move'])->name('bulk.import.move');
    Route::post('/bulk/delete', [BulkImportController::class, 'delete'])->name('bulk.import.delete');
    Route::post('/bulk/rename', [BulkImportController::class, 'rename'])->name('bulk.import.rename');
    Route::post('/bulk-import/paste', [BulkImportController::class, 'paste'])->name('bulk.import.paste');
    Route::post('/bulk-import/create-folder', [BulkImportController::class, 'createFolder'])->name('bulk.import.createFolder');

    // Route::get('/demo', [ShareController::class, 'intradayChart']);
    // Route::get('/chart', function () {
    //     return view('demo');
    // });
    Route::fallback(function () {
        return redirect()->route('login');
    });
});

require __DIR__ . '/auth.php';
