<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique(); // Misal: IN-20250121-001
            $table->string('supplier_name')->nullable()->default('Pasar/Umum');
            $table->date('transaction_date');
            $table->decimal('total_cost', 15, 2); // Total uang keluar belanja
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
