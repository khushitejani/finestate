<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Share;
use App\Events\ShareUpdates;
use Carbon\Carbon;

class UpdateSharePrices extends Command
{
    protected $signature = 'shares:update-prices';
    protected $description = 'Update share prices in real-time';
    public function handle()
    {
        $shares = Share::all();
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();


        foreach ($shares as $share) {
            $change = (rand(-50, 50) / 100);
            $share->share_price = round($share->share_price + $change, 2);

            $dayPrices = $share->day_prices ?? [];
            $dayPrices = [
                $yesterday => $dayPrices[$yesterday] ?? [],
                $today => $dayPrices[$today] ?? [],
            ];

            // $dayPrices[$today] = $dayPrices[$today] ?? [];
            // $currentTime = now()->format('H:i') . ':00';
            $currentTime = Carbon::now()->subMinute()->setSecond(0)->format('H:i:s');
            $dayPrices[$today][$currentTime] = $share->share_price;

            $share->day_prices = $dayPrices;
            $share->save();

            event(new \App\Events\ShareUpdates([
                'price' => $share->share_price,
                'time' => now()->format('H:i:s')
            ]));
        }

        $this->info("Shares updated at " . now());
    }
}
