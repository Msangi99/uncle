<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('term_percentages', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('term_number'); // 1, 2, 3, 4
            $table->decimal('percent_paid', 5, 2)->default(25); // asilimia ya ada iliyolipwa kwa msimu huo
            $table->timestamps();

            $table->unique('term_number');
        });

        // Default: misimu 4 sawa 25% kila moja
        DB::table('term_percentages')->insert([
            ['term_number' => 1, 'percent_paid' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['term_number' => 2, 'percent_paid' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['term_number' => 3, 'percent_paid' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['term_number' => 4, 'percent_paid' => 25, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('term_percentages');
    }
};
