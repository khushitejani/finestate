<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\AircraftShop;
use App\Models\BusinessShipping;
use App\Models\BusinessTaxi;
use App\Models\Card;
use App\Models\Carshowroom;
use App\Models\Coin;
use App\Models\Cryptocurrency;
use App\Models\Improvement;
use App\Models\Insight;
use App\Models\Island;
use App\Models\Jewelled;
use App\Models\NFT;
use App\Models\Painting;
use App\Models\Property;
use App\Models\RetroCar;
use App\Models\Share;
use App\Models\Stamp;
use App\Models\UniqueItem;
use App\Models\YatchShop;
use App\Models\Construction;
use App\Models\ForbsSlot;
use App\Models\BusinessSlot;

class TrashedController extends Controller
{
    public function Index()
    {
        $trashedData = [
            'aircraft_shops' => AircraftShop::onlyTrashed()->get(),
            'business_shippings' => BusinessShipping::onlyTrashed()->get(),
            'business_taxis' => BusinessTaxi::onlyTrashed()->get(),
            'cards' => Card::onlyTrashed()->get(),
            'carshowrooms' => Carshowroom::onlyTrashed()->get(),
            'coins' => Coin::onlyTrashed()->get(),
            'cryptocurrencies' => Cryptocurrency::onlyTrashed()->get(),
            'improvements' => Improvement::onlyTrashed()->get(),
            'insights' => Insight::onlyTrashed()->get(),
            'islands' => Island::onlyTrashed()->get(),
            'jewelleds' => Jewelled::onlyTrashed()->get(),
            'nfts' => NFT::onlyTrashed()->get(),
            'paintings' => Painting::onlyTrashed()->get(),
            'Property' => Property::onlyTrashed()->get(),
            'retro_cars' => RetroCar::onlyTrashed()->get(),
            'shares' => Share::onlyTrashed()->get(),
            'stamps' => Stamp::onlyTrashed()->get(),
            'unique_items' => UniqueItem::onlyTrashed()->get(),
            'yatch_shops' => YatchShop::onlyTrashed()->get(),
            'constructions' => Construction::onlyTrashed()->get(),
            'forbs_slots' => ForbsSlot::onlyTrashed()->get(),
            'business_slots' => BusinessSlot::onlyTrashed()->get(),
        ];

        return view('trashed.index', compact('trashedData'));
    }


    public function restore($table, $id)
    {
        $model = "App\\Models\\" . ucfirst(Str::camel(rtrim($table, 's')));
        $model::withTrashed()->findOrFail($id)->restore();

        return response()->json(['success' => true]);
    }

    public function forceDelete($table, $id)
    {
        $model = "App\\Models\\" . ucfirst(Str::camel(rtrim($table, 's')));
        $item = $model::withTrashed()->findOrFail($id);

        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        $item->forceDelete();
        return response()->json(['success' => true]);
    }
    public function generateUniqueNumber($table)
    {
        $model = "App\\Models\\" . ucfirst(Str::camel(rtrim($table, 's')));
        do {
            $number = rand(1000, 9999);
            $existsInTable = $model::where('no', $number)->exists();
            $existsInFolder = Storage::disk('public')->exists("no/$number");
        } while ($existsInTable || $existsInFolder);

        return response()->json([
            'success' => true,
            'number' => $number
        ]);
    }
}
