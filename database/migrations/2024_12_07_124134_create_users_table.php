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
        /**
         * ===========================
         * USERS TABLE
         * ===========================
         */
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // RELATIONSHIPS
            $table->foreignId('branch_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();

            // PERSONAL DETAILS
            $table->string('user_name')->unique();                    // System username
            $table->string('name');                                   // Full name
            $table->string('passport')->default('passport.png');      // Profile photo
            $table->string('gender')->nullable();                     // Gender
            $table->string('phone', 15)->unique()->nullable();        // Phone number
            $table->string('id_number', 20)->unique();                // National ID

            // COMMISSION & AUTH
            $table->decimal('commission_rate', 10, 2)
                  ->default(5.00)
                  ->nullable();                                       // Commission percentage
            $table->string('email')->unique()->index();               // Email
            $table->timestamp('email_verified_at')->nullable();       // Verification time

            // ACCOUNT STATUS
            $table->integer('status')->default(1);                    // 1=Active, 0=Inactive
            $table->string('otp', 10)->nullable();                    // One-Time Password
            $table->timestamp('otp_expires_at')->nullable();          // OTP Expiry

            // SIGNATURE & AUTH
            $table->text('signature')->nullable();                    // Digital signature
            $table->timestamp('signed_at')->nullable();               // Signature timestamp
            $table->string('password');                               // Password
            $table->rememberToken();                                  // Remember me token

            // AUDIT TRAIL
            $table->string('created_by')->nullable();                 // Created by user token
            $table->string('updated_by')->nullable();                 // Updated by user token

            $table->timestamps();                                     // created_at & updated_at
            $table->softDeletes();                                    // Enables soft delete support
        });

        /**
         * ===========================
         * PERMISSIONS TABLE
         * ===========================
         */
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                         // Permission name (e.g. manage_branch)
            $table->string('slug')->unique();                         // Slug (e.g. manage_branch)
            $table->string('description')->nullable();                // Optional description
            $table->timestamps();
            $table->index('slug');                                    // Index for quick lookup
        });

        /**
         * ===========================
         * USER_PERMISSIONS PIVOT TABLE
         * ===========================
         */
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();                                // Related user

            $table->foreignId('permission_id')
                  ->constrained()
                  ->cascadeOnDelete();                                // Related permission

            $table->json('selected_branches')->nullable();            // Specific branches for permission
            $table->json('selected_products')->nullable();            // Specific products
            $table->json('selected_catalogues')->nullable();          // Specific catalogues

            $table->timestamps();
        });

        /**
         * ===========================
         * PASSWORD RESET TOKENS TABLE
         * ===========================
         */
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();                         // User email
            $table->string('token');                                  // Reset token
            $table->timestamp('created_at')->nullable();              // Time token was created
        });

        /**
         * ===========================
         * SESSIONS TABLE
         * ===========================
         */
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();                          // Session ID

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();                                // Related user

            $table->string('ip_address', 45)->nullable();             // IP Address
            $table->text('user_agent')->nullable();                   // Browser details
            $table->longText('payload');                              // Session payload
            $table->integer('last_activity')->index();                // Last activity timestamp
        });

        /**
         * ===========================
         * KYC DATA TABLE
         * ===========================
         */
        Schema::create('k_y_c_data', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();                                // Related user

            $table->string('doc_type')->nullable();                   // Type of document (e.g. ID, Passport)
            $table->string('document')->nullable();                   // Document file path or name
            $table->string('status')->default('pending')->nullable(); // Verification status
            $table->text('description')->nullable();                  // Additional info or rejection reason

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('k_y_c_data');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('users');
    }
};
