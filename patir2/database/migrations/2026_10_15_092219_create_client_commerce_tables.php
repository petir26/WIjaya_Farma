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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('category', 80);
            $table->string('package', 100);
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->text('description')->nullable();
            $table->string('content', 150)->nullable();
            $table->json('dose')->nullable();
            $table->text('warning')->nullable();
            $table->string('image')->nullable();
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->string('status', 40)->default('menunggu_pembayaran')->index();
            $table->string('fulfillment_method', 20)->default('delivery');
            $table->text('shipping_address')->nullable();
            $table->string('pharmacy_branch', 120)->nullable();
            $table->string('payment_method', 30)->nullable();
            $table->string('payment_status', 30)->default('belum_dibayar');
            $table->string('payment_reference')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_package', 100)->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('address')->nullable();
            $table->string('status', 40)->default('menunggu_verifikasi');
            $table->string('courier', 80)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();
        });

        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('doctor_name', 120)->nullable();
            $table->text('complaint');
            $table->string('status', 30)->default('menunggu_dokter')->index();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('consultation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sender_type', 20)->default('client');
            $table->text('message');
            $table->timestamps();
            $table->index(['consultation_id', 'created_at']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'order_item_id']);
        });

        Schema::create('customer_service_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->text('admin_reply')->nullable();
            $table->string('status', 30)->default('terkirim');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->string('verification_status', 30)->default('menunggu_verifikasi');
            $table->timestamps();
            $table->index(['user_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('customer_service_messages');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('consultation_messages');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('products');
    }
};
