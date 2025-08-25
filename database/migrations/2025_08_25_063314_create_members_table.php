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
    Schema::create('members', function (Blueprint $table) {
        $table->id();
        $table->string('member_id')->unique();
        $table->string('nik_ktp', 16)->unique();
        $table->string('nama_lengkap');
        $table->text('alamat_lengkap');
        $table->string('wilayah', 3);
        $table->string('nomor_whatsapp', 15);
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->timestamp('registration_date');
        $table->timestamps();
        
        $table->index('wilayah');
        $table->index('status');
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
