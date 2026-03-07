<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->unsignedTinyInteger('term_number'); // 1-4
            $table->string('year', 50);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'term_number', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
