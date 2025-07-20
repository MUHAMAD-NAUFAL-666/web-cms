<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('status', ['planning', 'development', 'testing', 'completed', 'maintenance'])
                  ->default('planning')
                  ->after('stack');
            $table->date('tanggal_mulai')->nullable()->after('status');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->boolean('is_featured')->default(false)->after('tanggal_selesai');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['status', 'tanggal_mulai', 'tanggal_selesai', 'is_featured']);
            $table->dropSoftDeletes();
        });
    }
};
