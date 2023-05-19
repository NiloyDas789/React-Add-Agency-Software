<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_toll_free_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('toll_free_number_id')->constrained()->cascadeOnDelete();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->string('lead_sku')->nullable();
            $table->string('state')->nullable();
            $table->integer('length');
            $table->string('master')->nullable();
            $table->string('ad_id')->nullable();
            $table->tinyInteger('source_type');
            $table->string('website')->nullable();
            $table->string('terminating_number')->nullable();
            $table->tinyInteger('data_type');
            $table->date('assigned_at')->nullable();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->date('test_call_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_toll_free_numbers');
    }
};
