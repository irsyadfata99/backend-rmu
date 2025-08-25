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
        Schema::create('wilayah_counters', function (Blueprint $table) {
            $table->id();
            $table->string('wilayah_code', 3)->unique();
            $table->unsignedInteger('current_number')->default(0);
            $table->timestamps();
            
            $table->index('wilayah_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayah_counters');
    }
};