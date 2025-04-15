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

            /** ---------------------------------------------
             *  RELATIONSHIPS
             *  --------------------------------------------- */
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalogue_id')->constrained()->cascadeOnDelete();

            /** ---------------------------------------------
             *  IDENTIFIERS & BASIC INFO
             *  --------------------------------------------- */
            $table->string('barcode')->unique()->nullable();
            $table->string('sku')->unique();              // Stock Keeping Unit
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('photo')->nullable();          // Product image path

            /** ---------------------------------------------
             *  PRICING INFORMATION
             *  --------------------------------------------- */
            $table->decimal('buying_price', 10, 2)->default(0.00);
            $table->decimal('normal_price', 10, 2)->default(0.00);
            $table->decimal('whole_sale_price', 10, 2)->default(0.00);
            $table->decimal('agent_price', 10, 2)->default(0.00);
            $table->decimal('commission_on_sale', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00); // As percentage

            /** ---------------------------------------------
             *  STOCK & INVENTORY MANAGEMENT
             *  --------------------------------------------- */
            $table->integer('quantity')->default(0);                // Current stock
            $table->integer('sold_quantity')->default(0);           // For analytics
            $table->integer('low_stock_threshold')->default(10);    // Alert level

            /** ---------------------------------------------
             *  ADDITIONAL DETAILS
             *  --------------------------------------------- */
            $table->string('unit')->default('piece');        // e.g., piece, kg, liter
            $table->decimal('weight', 8, 2)->nullable();     // Product weight

            /** ---------------------------------------------
             *  STATUS & APPROVAL
             *  --------------------------------------------- */
            $table->enum('status', ['active', 'inactive'])->default('active'); // enforce valid states
            $table->boolean('is_verified')->default(false);  
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            /** ---------------------------------------------
             *  AUDIT TRAIL
             *  --------------------------------------------- */
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            /** ---------------------------------------------
             *  SOFT DELETES
             *  --------------------------------------------- */
            $table->softDeletes();

            /** ---------------------------------------------
             *  TIMESTAMPS
             *  --------------------------------------------- */
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
