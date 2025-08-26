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
        Schema::table('printer_tasks', function (Blueprint $table) {
            $table->string('device_name')->after('id');
            $table->uuid('device_uuid')->unique()->after('device_name');
            $table->string('ip_address', 45)->nullable()->after('device_uuid'); // IPv4/IPv6
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('printer_tasks', function (Blueprint $table) {
            $table->dropColumn(['device_name','device_uuid','ip_address']);
          
        });
    }
};
