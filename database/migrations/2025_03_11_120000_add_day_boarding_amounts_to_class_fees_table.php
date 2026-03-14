<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_fees', function (Blueprint $table) {
            $table->decimal('amount_day', 12, 2)->nullable()->after('amount');
            $table->decimal('amount_boarding', 12, 2)->nullable()->after('amount_day');
        });

        // Backfill: existing single amount applies to both day and boarding
        \DB::table('class_fees')->update([
            'amount_day' => \DB::raw('amount'),
            'amount_boarding' => \DB::raw('amount'),
        ]);
    }

    public function down(): void
    {
        Schema::table('class_fees', function (Blueprint $table) {
            $table->dropColumn(['amount_day', 'amount_boarding']);
        });
    }
};
