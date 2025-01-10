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
        Schema::create('m_s_t_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fed_case_id');
            $table->foreign('fed_case_id')->references('id')->on('fed_cases')->onDelete('cascade');
            //ACTIVE-DUTY / RESERVE / ACADEMY SERVICE 
            $table->string('military_service')->nullable();
            $table->date('military_service_date_1')->nullable();
            $table->date('military_service_date_2')->nullable();
            $table->string('military_service_active_duty')->nullable();
            $table->date('military_service_active_duty_date_1')->nullable();
            $table->date('military_service_active_duty_date_2')->nullable();
            $table->string('military_service_reserve')->nullable();
            $table->date('military_service_reserve_date_1')->nullable();
            $table->date('military_service_reserve_date_2')->nullable();
            $table->string('military_service_academy')->nullable();
            $table->text('military_service_academy_amount')->nullable();
            $table->string('military_service_retire')->nullable();
            $table->string('military_service_collecting')->nullable();
            $table->string('military_service_reserves')->nullable();
            $table->longText('military_service_note')->nullable();
            $table->text('military_service_amount')->nullable();

            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_s_t_s');
    }
};
