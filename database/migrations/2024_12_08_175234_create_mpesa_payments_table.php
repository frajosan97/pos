<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->string('phone');
            $table->string('shortcode')->nullable();
            $table->string('status')->nullable();
            $table->string('use_status')->default('pending');
            $table->text('response_payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_payments');
    }
};
