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
        Schema::table('artikels', function (Blueprint $table) {
            //
            $table->string('slug')->unique()->nullable()->after('judul');
            $table->string('meta_title', 60)->nullable()->after('isi');
            $table->string('meta_description')->nullable()->after('meta_title');
            $table->integer('reading_time')->nullable()->after('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artikels', function (Blueprint $table) {
            //
        });
    }
};
