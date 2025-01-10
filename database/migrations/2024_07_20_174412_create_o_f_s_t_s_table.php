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
        Schema::create('o_f_s_t_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fed_case_id');
            $table->foreign('fed_case_id')->references('id')->on('fed_cases')->onDelete('cascade');
            // PART TIME SERVICE
            $table->string('employee_work')->nullable();
            $table->string('empolyee_hours_work')->nullable();
            $table->date('empolyee_multiple_date')->nullable();
            $table->date('empolyee_multiple_date_to')->nullable();
            // NON-DEDUCTION SERVICE
            $table->string('non_deduction_service')->nullable();
            $table->date('non_deduction_service_date')->nullable();
            $table->date('non_deduction_service_date_2')->nullable();
            $table->string('non_deduction_service_deposit')->nullable();
            $table->integer('non_deduction_service_deposit_owned')->nullable();
            // BREAK IN SERVICE / REFUNDED SERVICE
            $table->string('break_in_service')->nullable();
            $table->date('break_in_service_date_1')->nullable();
            $table->date('break_in_service_date_2')->nullable();
            $table->date('break_in_service_return_date')->nullable();
            $table->string('break_in_service_refund')->nullable();
            $table->string('break_in_service_redeposite')->nullable();
            $table->integer('break_in_service_amount_redeposite')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_f_s_t_s');
    }
};
