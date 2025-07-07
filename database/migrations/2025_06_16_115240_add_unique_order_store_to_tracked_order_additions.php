<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracked_order_additions', function (Blueprint $table) {
            $table->string('store_id')->nullable(false)->change(); // Make it required
            $table->unique(['order_id', 'store_id'], 'unique_order_store');
        });
    }

    public function down(): void
    {
        Schema::table('tracked_order_additions', function (Blueprint $table) {
            $table->dropUnique('unique_order_store');
        });
    }
};
