<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cryptocurrency;
use App\Events\CryptoUpdates;
use Carbon\Carbon;

class UpdateCryptoPrices extends Command
{
    protected $signature = 'crypto:update-prices';
    protected $description = 'Update cryptocurrency prices in real-time';

    public function handle()
    {
        $cryptos = Cryptocurrency::all();
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();


        foreach ($cryptos as $crypto) {

            $Change = rand(-50, 50) / 100;
            $crypto->price = round($crypto->price + $Change, 2);

            $dayPrices = $crypto->day_prices ?? [];
            $dayPrices = [
                $yesterday => $dayPrices[$yesterday] ?? [],
                $today => $dayPrices[$today] ?? [],
            ];

            $currentTime = Carbon::now()->subMinute()->setSecond(0)->format('H:i:s');
            $dayPrices[$today][$currentTime] = $crypto->price;

            $crypto->day_prices = $dayPrices;
            $crypto->save();

            event(new CryptoUpdates([
                'price'  => $crypto->price,
                'time'   => now()->format('H:i:s')
            ]));
        }

        $this->info("Crypto prices updated at " . now());
    }
}
