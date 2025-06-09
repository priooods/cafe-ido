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
        Schema::create('t_transaction_checkout_tabs', function (Blueprint $table) {
            $table->id();
            $table->string('session_checkout')->comment('yyyymmddhhmmss;tablenumber');
            $table->unsignedInteger('m_status_tabs_id');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('notes')->nullable();
            $table->tinyInteger('cashier')->default(0)->comment('0 = on cashier, 1 = on debit');
            $table->integer('table_number');
            $table->string('path')->nullable();
            $table->string('amount_paid')->nullable();
            $table->string('amount_change')->nullable();
            $table->timestamps();
            $table->foreign('m_status_tabs_id')->references('id')->on('m_status_tabs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_transaction_checkout_tabs');
    }
};
