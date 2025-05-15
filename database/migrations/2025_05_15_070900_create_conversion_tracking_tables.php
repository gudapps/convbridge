<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. conversion_settings table
        Schema::create('conversion_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // e.g., facebook, google, bing
            $table->json('settings');
            $table->unique(['store_id', 'platform']); // Prevent duplicate config per platform per store
            $table->timestamps();
        });

        // 2. tracked_orders table
        Schema::create('tracked_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->bigInteger('order_id')->unique(); // BigCommerce order ID
            $table->json('order_data'); // Raw or parsed order info
            $table->timestamps();
        });

        // 3. tracked_order_items table
        Schema::create('tracked_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracked_order_id')->constrained()->onDelete('cascade');
            $table->bigInteger('product_id')->nullable(); // BigCommerce product ID
            $table->string('name');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // 4. conversion_logs table
        Schema::create('conversion_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->bigInteger('order_id');
            $table->string('platform');
            $table->string('status'); // success, failed, pending
            $table->text('response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->index(['order_id', 'platform']);
            $table->timestamps();
        });

        // 5. tracked_customers table
        Schema::create('tracked_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('tracked_order_id')->constrained()->onDelete('cascade');
            $table->bigInteger('customer_id')->nullable(); // From BigCommerce
            $table->string('email')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversion_logs');
        Schema::dropIfExists('tracked_order_items');
        Schema::dropIfExists('tracked_orders');
        Schema::dropIfExists('conversion_settings');
        Schema::dropIfExists('tracked_customers');

    }
};
