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
        Schema::table('risk_factors', function (Blueprint $table) {
            $table->foreignId('destination_id')->constrained()->onDelete('cascade');
            $table->string('category'); // war, natural_disaster, disease, etc
            $table->decimal('multiplier', 3, 2); // 1.5, 2.0, etc
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
