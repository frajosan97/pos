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
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalogue_id')->constrained()->cascadeOnDelete();
            $table->string('barcode')->unique()->nullable();
            $table->string('name');
            $table->decimal('buying_price', 10, 2)->default(0.00);
            $table->decimal('normal_price', 10, 2)->default(0.00);
            $table->decimal('whole_sale_price', 10, 2)->default(0.00);
            $table->decimal('agent_price', 10, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00); // Tax percentage
            $table->decimal('discount', 10, 2)->default(0.00); // Discount amount
            $table->integer('quantity'); // Available stock quantity
            $table->integer('sold_quantity')->default(0); // Tracks sold stock
            $table->integer('low_stock_threshold')->default(10); // Minimum stock level for alerts
            $table->string('sku')->unique();
            $table->string('photo')->nullable();
            $table->string('unit')->default('piece'); // Unit of measure
            $table->decimal('weight', 8, 2)->nullable(); // Weight or volume for measurement
            $table->string('status')->default('active'); // active or inactive
            $table->text('description')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
