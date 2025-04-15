<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for:
     * - Companies
     * - Branches
     */
    public function up(): void
    {
        /**
         * --------------------------------------------
         * 1. Companies Table
         * --------------------------------------------
         * Stores information about registered companies.
         */
        Schema::create('companies', function (Blueprint $table) {
            $table->id();                                               // Primary key
            $table->string('name');                                     // Company name
            $table->string('address');                                  // Company address
            $table->string('phone', 15)->unique();                      // Unique phone number
            $table->string('email')->unique()->index();                 // Unique and indexed email
            $table->string('logo')->nullable();                         // Optional logo path
            $table->string('color')->default('red');                    // UI theme color
            $table->string('commission_by')->nullable()->default('employee'); // Commission assigned by (employee/other)
            $table->string('sms_mode')->default('online');              // SMS mode: online/offline
            $table->string('sms_partner_id')->nullable();               // SMS integration partner ID
            $table->string('sms_api_key')->nullable();                  // SMS API key
            $table->string('sms_sender_id')->nullable();                // SMS sender ID
            $table->string('sms_api_url')->nullable();                  // SMS endpoint URL
            $table->string('status')->default('active');                // Status: active/inactive
            $table->timestamps();                                       // created_at, updated_at
        });

        /**
         * --------------------------------------------
         * 2. Branches Table
         * --------------------------------------------
         * Stores information about company branches and
         * links them to geographic locations.
         */
        Schema::create('branches', function (Blueprint $table) {
            $table->id();                                               // Primary key
            $table->foreignId('company_id')                             // FK to companies
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('county_id')                              // FK to counties
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('constituency_id')                        // Optional FK to constituencies
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('ward_id')                                // Optional FK to wards
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('location_id')                            // Optional FK to locations
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('name');                                     // Branch name
            $table->timestamps();                                       // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations in the correct order
     * to maintain foreign key integrity.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
        Schema::dropIfExists('companies');
    }
};
