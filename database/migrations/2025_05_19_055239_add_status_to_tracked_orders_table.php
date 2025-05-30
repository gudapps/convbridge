<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracked_orders', function (Blueprint $table) {
            $table->string('status')->nullable()->after('order_id');
            $table->string('status_id')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tracked_orders', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('status_id');
        });
    }
};
