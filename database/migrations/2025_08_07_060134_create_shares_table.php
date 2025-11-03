<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('shares', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->decimal('share_price', 10, 2);
            $table->decimal('dividend', 10, 4);
            $table->string('time_period');
            $table->bigInteger('capitalization');
            $table->bigInteger('available_shares');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shares');
    }
};
