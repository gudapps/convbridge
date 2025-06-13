<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracked_order_additions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->string('fbp')->nullable();
            $table->string('fbc')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracked_order_additions');
    }
};
