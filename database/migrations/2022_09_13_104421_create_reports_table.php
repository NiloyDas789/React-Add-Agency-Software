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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->string('toll_free_number')->nullable();
            $table->string('lead_sku')->nullable();
            $table->string('revenue')->nullable();
            $table->string('terminating_number')->nullable();
            $table->string('ani')->nullable();
            $table->integer('duration')->nullable();
            $table->string('disposition')->nullable();
            $table->string('call_status')->nullable();
            $table->string('state')->nullable();
            $table->integer('area_code')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('call_recording')->nullable();
            $table->tinyInteger('credit')->nullable();
            $table->tinyInteger('credit_reason')->nullable();
            $table->dateTime('called_at');
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
        Schema::dropIfExists('reports');
    }
};
