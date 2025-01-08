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
        // Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('user_name')->unique();
            $table->string('name');
            $table->string('passport')->default('passport.png');
            $table->string('gender')->nullable();
            $table->string('phone', 15)->unique()->nullable();
            $table->string('id_number', 20)->unique();
            $table->decimal('commission_rate', 10, 2)->default(5.00)->nullable();
            $table->string('email')->unique()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('status')->default(1);
            $table->string('otp', 10)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        // Permissions Table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Permission name (e.g., 'manage_branch')
            $table->string('slug')->unique(); // Slug for the permission
            $table->string('description')->nullable(); // Description of the permission
            $table->timestamps();
            $table->index('slug'); // Index for fast lookups by slug
        });

        // User_Permission Pivot Table (with dynamic permissions)
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Link to user
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete(); // Link to permission
            $table->json('selected_branches')->nullable(); // Store specific branches for "manage_branch"
            $table->json('selected_products')->nullable(); // Store specific products for "manage_product"
            $table->json('selected_catalogues')->nullable(); // Store specific catalogues for "manage_catalogue"
            $table->timestamps();
        });

        // Password Reset Tokens Table
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions Table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('users');
    }
};
