<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracked_order_items', function (Blueprint $table) {
            $table->string('sku')->after('product_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tracked_order_items', function (Blueprint $table) {
            $table->dropColumn('sku');
        });
    }
};
