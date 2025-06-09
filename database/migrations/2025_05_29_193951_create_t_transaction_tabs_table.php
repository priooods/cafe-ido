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
        Schema::create('t_transaction_tabs', function (Blueprint $table) {
            $table->id();
            $table->string('session_product')->comment('encrypted table_number');
            $table->unsignedBigInteger('t_transaction_checkout_tabs_id')->nullable();
            $table->unsignedBigInteger('t_product_tabs_id');
            $table->smallInteger('count')->default(1)->comment('Count Product');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->foreign('t_product_tabs_id')->references('id')->on('t_product_tabs')->cascadeOnDelete();
            $table->foreign('t_transaction_checkout_tabs_id')->references('id')->on('t_transaction_checkout_tabs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_transaction_tabs');
    }
};
