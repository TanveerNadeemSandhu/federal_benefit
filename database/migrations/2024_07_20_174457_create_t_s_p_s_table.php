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
        Schema::create('t_s_p_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fed_case_id');
            $table->foreign('fed_case_id')->references('id')->on('fed_cases')->onDelete('cascade');
            // CONTRIBUTIONS / LOAN(S)
            $table->string('contribute')->nullable();
            $table->string('contribute_pp')->nullable();
            $table->string('contribute_pp_percentage')->nullable();
            $table->string('contribute_tsp')->nullable();
            $table->string('contribute_tsp_pp')->nullable();
            $table->string('contribute_tsp_pp_percentage')->nullable();
            $table->string('contribute_limit')->nullable();

            $table->string('contribute_tsp_loan')->nullable();
            $table->string('contribute_pay_pp')->nullable();
            $table->string('contribute_pay_pp_value')->nullable();
            $table->string('contribute_own_loan')->nullable();
            $table->string('contribute_own_loan_2')->nullable();
            $table->date('contribute_pay_date')->nullable();
            $table->string('contribute_tsp_loan_gen')->nullable();
            $table->string('contribute_tsp_res')->nullable();
            // IN RETIREMENT
            $table->string('employee_not_sure')->nullable();
            $table->string('employee_imd')->nullable();
            $table->string('at_age')->nullable();
            $table->integer('employee_at_age')->nullable();
            $table->string('employee_loss')->nullable();
            $table->string('employee_income')->nullable();
            // GOALS
            $table->string('goal')->nullable();
            $table->string('goal_amount')->nullable();
            $table->string('goal_tsp')->nullable();
            $table->string('goal_retirement')->nullable();
            $table->string('goal_track')->nullable();
            $table->string('goal_comfor')->nullable();
            $table->string('goal_professional')->nullable();
            $table->string('goal_why')->nullable();
            // FUNDS CHOICES
            $table->string('g_name')->nullable();
            $table->string('g_value')->nullable();
            $table->string('f_name')->nullable();
            $table->string('f_value')->nullable();
            $table->string('c_name')->nullable();
            $table->string('c_value')->nullable();
            $table->string('s_name')->nullable();
            $table->string('s_value')->nullable();
            $table->string('i_name')->nullable();
            $table->string('i_value')->nullable();
            $table->string('l_income')->nullable();
            $table->string('l_income_value')->nullable();
            $table->string('l_2025')->nullable();
            $table->string('l_2025_value')->nullable();
            $table->string('l_2030')->nullable();
            $table->string('l_2030_value')->nullable();
            $table->string('l_2035')->nullable();
            $table->string('l_2035_value')->nullable();
            $table->string('l_2040')->nullable();
            $table->string('l_2040_value')->nullable();
            $table->string('l_2045')->nullable();
            $table->string('l_2045_value')->nullable();
            $table->string('l_2050')->nullable();
            $table->string('l_2050_value')->nullable();
            $table->string('l_2055')->nullable();
            $table->string('l_2055_value')->nullable();
            $table->string('l_2060')->nullable();
            $table->string('l_2060_value')->nullable();
            $table->string('l_2065')->nullable();
            $table->string('l_2065_value')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('total_amount_percentage')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_s_p_s');
    }
};
