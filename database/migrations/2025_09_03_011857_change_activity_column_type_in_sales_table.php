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
        Schema::table('sales', function (Blueprint $table) {
            // Ubah kolom activity jadi string
            $table->string('activity')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Balikkan ke enum jika diperlukan (isi sesuai enum sebelumnya)
            $table->enum('activity', [
                'breakfast', 'breakfast inclusive', 'lunch', 'dinner', 'afternoontea','entertainment','officer',
                'room service','dinner inclusive','lunch inclusive','drink','candle light dinner','supper',
                'red light special dinner','afternoon tea'
            ])->change();
        });
    }
};
