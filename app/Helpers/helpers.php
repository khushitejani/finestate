<?php

if (!function_exists('getImageUrl')) {
    function getImageUrl(?string $path): string
    {
        return asset('storage/' . $path);
    }
}
// if (!function_exists('getChartDataForToday')) {
//     function getChartDataForToday($model)
//     {
//         $today = now()->toDateString();
//         $dayPrices = $model->day_prices ?? [];
//         $basePrices = $dayPrices['base'] ?? [];

//         if (!empty($dayPrices[$today])) {
//             return $dayPrices[$today];
//         }

//         $chartData = [];
//         $startOfDay = now()->startOfDay();

//         foreach ($basePrices as $index => $_) {
//             $price = $basePrices[array_rand($basePrices)];

//             $randomSeconds = rand(0, 86400);
//             $time = $startOfDay->copy()
//                 ->addSeconds($randomSeconds)
//                 ->format('H:i:s');

//             $chartData[$time] = $price;
//         }

//         ksort($chartData); 
//         $model->day_prices = [
//             'base' => $basePrices,
//             $today => $chartData
//         ];

//         $model->save();

//         return $chartData;
//     }
// }

if (!function_exists('getChartDataForToday')) {
    function getChartDataForToday($model)
    {
        $today = now()->toDateString();
        $dayPrices = $model->day_prices ?? [];
        $basePrices = $dayPrices['base'] ?? [];

        // Return today's prices if already set
        if (!empty($dayPrices[$today])) {
            return $dayPrices[$today];
        }

        if (empty($basePrices)) {
            $basePrices = [10, 20, 30, 50, 100]; 
        }

        $chartData = [];
        $startOfDay = now()->startOfDay();

        foreach ($basePrices as $index => $_) {
            $price = $basePrices[array_rand($basePrices)];
            $randomSeconds = rand(0, 86400);
            $time = $startOfDay->copy()
                ->addSeconds($randomSeconds)
                ->format('H:i:s');

            $chartData[$time] = $price;
        }

        ksort($chartData);

        $model->day_prices = [
            'base' => $basePrices,
            $today => $chartData
        ];
        $model->save();

        return $chartData;
    }
}
