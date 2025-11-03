<?php

namespace App\Http\Controllers;

use App\Models\AircraftShop;
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

class HomeController extends Controller
{
    public function index()
    {
        $modules = [
            'Cards' => \App\Models\Card::count(),
            'Shares' => \App\Models\Share::count(),
            'Properties' => \App\Models\Property::count(),
            'Improvements' => \App\Models\Improvement::count(),
            'Car Showrooms' => \App\Models\Carshowroom::count(),
            'Aircraft Shops' => \App\Models\AircraftShop::count(),
            'Coins' => \App\Models\Coin::count(),
            'Paintings' => \App\Models\Painting::count(),
            'Unique Items' => \App\Models\UniqueItem::count(),
            'Retro Cars' => \App\Models\RetroCar::count(),
            'Jewels' => \App\Models\Jewelled::count(),
            'Stamps' => \App\Models\Stamp::count(),
            'NFTs' => \App\Models\NFT::count(),
            'Islands' => \App\Models\Island::count(),
            'Yacht Shops' => \App\Models\YatchShop::count(),
            'Cryptos' => \App\Models\Cryptocurrency::count(),
            'Insights' => \App\Models\Insight::count(),
        ];

        return view('dashboard', compact('modules'));
    }
}
