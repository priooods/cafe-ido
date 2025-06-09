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
        Schema::create('t_product_tabs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('desc')->nullable();
            $table->string('price');
            $table->unsignedInteger('m_category_tabs_id');
            $table->unsignedInteger('m_status_tabs_id');
            $table->timestamps();
            $table->foreign('m_category_tabs_id')->references('id')->on('m_category_tabs')->cascadeOnDelete();
            $table->foreign('m_status_tabs_id')->references('id')->on('m_status_tabs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_product_tabs');
    }
};