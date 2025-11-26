<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Rename column from image to images (use raw SQL for reliability)
        DB::statement('ALTER TABLE islands CHANGE COLUMN image images VARCHAR(255) NULL');

        // Step 2: Change column type to JSON and allow NULL
        DB::statement('ALTER TABLE islands MODIFY COLUMN images JSON NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Change type back to VARCHAR
        DB::statement('ALTER TABLE islands MODIFY COLUMN images VARCHAR(255) NULL');

        // Step 2: Rename column back to image
        DB::statement('ALTER TABLE islands CHANGE COLUMN images image VARCHAR(255) NULL');
    }
};
