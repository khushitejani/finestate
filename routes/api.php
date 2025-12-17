<?php

use App\Http\Controllers\Api\AircraftShopController;
use App\Http\Controllers\Api\BusinessShippingController;
use App\Http\Controllers\Api\BusinessSlotController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\CarshowroomController;
use App\Http\Controllers\Api\CoinController;
use App\Http\Controllers\Api\CryptocurrencyController;
use App\Http\Controllers\Api\ImprovementController;
use App\Http\Controllers\Api\InsightController;
use App\Http\Controllers\Api\IslandController;
use App\Http\Controllers\Api\JewelledController;
use App\Http\Controllers\Api\PaintingController;
use App\Http\Controllers\Api\PropertyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShareController;
use App\Http\Controllers\Api\NFTController;
use App\Http\Controllers\Api\RetroCarController;
use App\Http\Controllers\Api\StampController;
use App\Http\Controllers\Api\UniqueItemController;
use App\Http\Controllers\Api\YatchShopController;
use App\Http\Controllers\Api\BusinessTaxiController;
use App\Http\Controllers\Api\PolicyController as ApiPolicyController;
use App\Http\Controllers\Api\ConstructionController;


Route::get('/cards', [CardController::class, 'CardListAll']);
Route::get('/shares', [ShareController::class, 'ShareListAll']);
Route::get('/properties', [PropertyController::class, 'PropertyListAll']);
Route::get('/improvements', [ImprovementController::class, 'ImprovementListAll']);
Route::get('/carshowrooms', [CarshowroomController::class, 'CarshowroomListAll']);
Route::get('/aircraftshops', [AircraftShopController::class, 'AircraftShopListAll']);
Route::get('/coins', [CoinController::class, 'CoinListAll']);
Route::get('/paintings', [PaintingController::class, 'PaintingListAll']);
Route::get('/nfts', [NFTController::class, 'NFTListAll']);
Route::get('/unique-items', [UniqueItemController::class, 'UniqueItemListAll']);
Route::get('/retro-cars', [RetroCarController::class, 'RetroCarListAll']);
Route::get('/islands', [IslandController::class, 'IslandListAll']);
Route::get('/jewelleds', [JewelledController::class, 'JewelledListAll']);
Route::get('/stamps', [StampController::class, 'StampListAll']);
Route::get('/yatchshops', [YatchShopController::class, 'YatchShopListAll']);
Route::get('/insights', [InsightController::class, 'InsightListAll']);
Route::get('/cryptocurrencies',[CryptocurrencyController::class,'CryptoCurrancyListAll']);
Route::get('/businesstaxis', [BusinessTaxiController::class, 'BusinessTexilistAll']);
Route::get('/businessshippings', [BusinessShippingController::class, 'BusinessShippinglistAll']);
Route::get('/policies', [ApiPolicyController::class, 'Policylist']);
Route::get('/business_slots', [BusinessSlotController::class, 'BusinessSlotAllList']);
Route::get('/constructions', [ConstructionController::class, 'ConstructionListAll']);








