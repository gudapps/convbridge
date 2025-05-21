<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('store_url')->nullable()->after('context');
            $table->string('store_name')->nullable()->after('store_url');
            $table->json('store_data')->nullable()->after('store_name');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('store_url');
            $table->dropColumn('store_name');
            $table->dropColumn('store_data');
        });
    }
};
