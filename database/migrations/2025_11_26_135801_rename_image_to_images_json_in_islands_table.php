<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Rename column from image → images
        Schema::table('islands', function (Blueprint $table) {
            $table->renameColumn('image', 'images');
        });

        // Step 2: Convert existing values to JSON
        $islands = DB::table('islands')->get();
        foreach ($islands as $island) {
            if ($island->images) {
                // Wrap existing string into a JSON array
                DB::table('islands')
                    ->where('id', $island->id)
                    ->update(['images' => json_encode([$island->images])]);
            } else {
                DB::table('islands')
                    ->where('id', $island->id)
                    ->update(['images' => json_encode([])]);
            }
        }

        // Step 3: Change column type to JSON
        Schema::table('islands', function (Blueprint $table) {
            $table->json('images')->nullable()->change();
        });
    }

    public function down(): void
    {
        $islands = DB::table('islands')->get();
        foreach ($islands as $island) {
            $images = json_decode($island->images, true);
            $firstImage = is_array($images) && count($images) > 0 ? $images[0] : null;
            DB::table('islands')
                ->where('id', $island->id)
                ->update(['images' => $firstImage]);
        }

        Schema::table('islands', function (Blueprint $table) {
            $table->string('images')->nullable()->change();
        });

        Schema::table('islands', function (Blueprint $table) {
            $table->renameColumn('images', 'image');
        });
    }
};
