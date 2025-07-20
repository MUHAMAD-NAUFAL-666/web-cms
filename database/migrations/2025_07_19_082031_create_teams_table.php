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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
           $table->string('nama');
$table->string('posisi');
$table->string('foto')->nullable();
$table->text('bio')->nullable();
$table->string('email')->nullable();
$table->string('phone')->nullable();
$table->json('sosial_media')->nullable();
$table->string('spesialisasi')->nullable();
$table->string('pengalaman_tahun')->default(0);
$table->boolean('is_active')->default(true);
$table->boolean('is_featured')->default(false);
$table->date('tanggal_bergabung')->nullable();
$table->enum('status', ['aktif', 'cuti', 'tidak_aktif'])->default('aktif');
$table->timestamps();
$table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
