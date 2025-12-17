<?php

namespace App\Http\Controllers;

use App\Models\AircraftShop;
use App\Models\BusinessShipping;
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

use Illuminate\Http\Request;
use App\Models\BusinessSlot;
use App\Models\BusinessTaxi;
use App\Models\Construction;
use App\Models\ForbsSlot;


class HomeController extends Controller
{
    public function index()
    {
        $modules = [
            'Cards' => Card::count(),
            'Shares' => Share::count(),
            'Properties' => Property::count(),
            'Improvements' => Improvement::count(),
            'Car Showrooms' => Carshowroom::count(),
            'Aircraft Shops' => AircraftShop::count(),
            'Coins' => Coin::count(),
            'Paintings' => Painting::count(),
            'Unique Items' => UniqueItem::count(),
            'Retro Cars' => RetroCar::count(),
            'Jewels' => Jewelled::count(),
            'Stamps' => Stamp::count(),
            'NFTs' => NFT::count(),
            'Islands' => Island::count(),
            'Yacht Shops' => YatchShop::count(),
            'Cryptos' => Cryptocurrency::count(),
            'Insights' => Insight::count(),
            'Business Shipping' => BusinessShipping::count(),
            'Business Taxi' => BusinessTaxi::count(),
            'Business Slots' => BusinessSlot::count(),
            'Construction' => Construction::count(),
            'Forbs' => ForbsSlot::count(),
        ];

        return view('dashboard', compact('modules'));
    }
}
