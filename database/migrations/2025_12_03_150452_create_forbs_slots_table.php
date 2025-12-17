<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forbs_slots', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->string('name');
            $table->string('business')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forbs_slots');
    }
};
