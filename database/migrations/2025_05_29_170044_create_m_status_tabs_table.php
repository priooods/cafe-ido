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
        Schema::create('m_status_tabs', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('title');
        });

        DB::table('m_status_tabs')->insert(
            array(
                ['title' => 'Draft'], //1
                ['title' => 'Available'], //2
                ['title' => 'Not Available'], //3
                ['title' => 'Waiting Order'], //4
                ['title' => 'Complete Order'], //5
                ['title' => 'Complete Payment'], //6
                ['title' => 'Waiting Payment'], //7
                ['title' => 'Failure Payment'], //8
                ['title' => 'Refund Payment'], //9
                ['title' => 'Active'], //10
                ['title' => 'Not Active'], //11
            )
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_status_tabs');
    }
};
