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
        Schema::create('m_user_role_tabs', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('title');
        });

        DB::table('m_user_role_tabs')->insert(
            array(
                ['title' => 'Super Admin'],
                ['title' => 'Admin'],
                ['title' => 'Cashier'],
            )
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_user_role_tabs');
    }
};
