<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run all related administrative boundary migrations:
     * - counties
     * - constituencies
     * - wards
     * - locations
     */
    public function up(): void
    {
        /**
         * --------------------------------------------
         * 1. Counties Table
         * --------------------------------------------
         * Stores list of counties.
         */
        Schema::create('counties', function (Blueprint $table) {
            $table->id();                       // Primary key
            $table->string('name');             // County name
            $table->timestamps();               // Created_at and updated_at
        });

        /**
         * --------------------------------------------
         * 2. Constituencies Table
         * --------------------------------------------
         * Stores constituencies linked to a county.
         */
        Schema::create('constituencies', function (Blueprint $table) {
            $table->id();                                               // Primary key
            $table->foreignId('county_id')                              // FK to counties
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('name');                                     // Constituency name
            $table->timestamps();                                       // Created_at and updated_at
        });

        /**
         * --------------------------------------------
         * 3. Wards Table
         * --------------------------------------------
         * Stores wards linked to a county and constituency.
         */
        Schema::create('wards', function (Blueprint $table) {
            $table->id();                                               // Primary key
            $table->foreignId('county_id')                              // FK to counties
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('constituency_id')                        // FK to constituencies
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('name');                                     // Ward name
            $table->timestamps();                                       // Created_at and updated_at
        });

        /**
         * --------------------------------------------
         * 4. Locations Table
         * --------------------------------------------
         * Stores specific locations linked to county, 
         * constituency, and ward.
         */
        Schema::create('locations', function (Blueprint $table) {
            $table->id();                                               // Primary key
            $table->foreignId('county_id')                              // FK to counties
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('constituency_id')                        // FK to constituencies
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('ward_id')                                // FK to wards
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('name');                                     // Location name
            $table->timestamps();                                       // Created_at and updated_at
        });
    }

    /**
     * Reverse all the migrations in proper order
     * to respect foreign key constraints.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
        Schema::dropIfExists('wards');
        Schema::dropIfExists('constituencies');
        Schema::dropIfExists('counties');
    }
};
