<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->string('dispositions')->nullable();
            $table->string('offer');
            $table->string('creative');
            $table->string('billable_payout')->nullable();
            $table->string('media_payout')->nullable();
            $table->string('margin')->nullable();
            $table->integer('billable_call_duration')->nullable();
            $table->integer('de_dupe')->nullable();
            $table->boolean('status')->default(1);
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
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
        Schema::dropIfExists('offers');
    }
};
