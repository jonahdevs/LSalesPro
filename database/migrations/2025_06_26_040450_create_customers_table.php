<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->enum('category', ['A', 'A+', 'B', 'C']);
            $table->string('contact_person');
            $table->string('phone');
            $table->string('email');
            $table->string('tax_id');
            $table->string('payment_terms');
            $table->decimal('credit_limit', 10, 2);
            $table->decimal('current_balance', 10, 2);
            $table->decimal('latitude', 10, 2);
            $table->decimal('longitude', 10, 2);
            $table->string('address');
            $table->foreignId('territory_id')->nullable()->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
