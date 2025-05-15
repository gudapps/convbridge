<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            Schema::table('stores', function (Blueprint $table) {
                $table->text('scope')->change();
            });
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            Schema::table('stores', function (Blueprint $table) {
                $table->string('scope')->change();
            });
        });
    }
};
