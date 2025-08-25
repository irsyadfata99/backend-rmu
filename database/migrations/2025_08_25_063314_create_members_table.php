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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id', 8)->unique(); // e.g., BDG-0001
            $table->string('nik_ktp', 16)->unique();
            $table->string('nama_lengkap');
            $table->text('alamat_lengkap');
            $table->string('wilayah', 3);
            $table->string('nomor_whatsapp', 15);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('registration_date');
            $table->timestamps();
            
            $table->index('member_id');
            $table->index('nik_ktp');
            $table->index('wilayah');
            $table->index('status');
            $table->index('registration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};