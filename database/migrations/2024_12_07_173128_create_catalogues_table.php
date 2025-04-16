<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This migration creates the 'catalogues' table with fields
 * for name, status, tracking of who created/updated, and timestamps.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This method defines the structure of the 'catalogues' table.
     */
    public function up(): void
    {
        Schema::create('catalogues', function (Blueprint $table) {
            // Auto-incrementing primary key ID
            $table->id();

            // Catalogue name - required string
            $table->string('name');

            // Active status
            $table->boolean('is_active')->default(false);

            // Who created the record - optional string
            $table->string('created_by')->nullable();

            // Who last updated the record - optional string
            $table->string('updated_by')->nullable();

            // Adds created_at and updated_at timestamp columns
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method will drop the 'catalogues' table if it exists.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogues');
    }
};
