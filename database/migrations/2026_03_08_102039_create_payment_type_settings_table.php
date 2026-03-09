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
        Schema::create('payment_type_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('tahadhari', 12, 2)->default(0);
            $table->decimal('maktaba', 12, 2)->default(0);
            $table->decimal('ream', 12, 2)->default(0);
            $table->decimal('maendeleo_ya_shule', 12, 2)->default(0);
            $table->decimal('taaluma', 12, 2)->default(0);
            $table->decimal('pesa_ya_mtihani_wa_taifa', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_type_settings');
    }
};
