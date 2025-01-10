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
        Schema::create('insurance_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fed_case_id');
            $table->foreign('fed_case_id')->references('id')->on('fed_cases')->onDelete('cascade');

            // FEDERAL EMPLOYEE GROUP LIFE INSURANCE (FEGLI) METLIFE
            $table->string('insurance')->nullable();
            $table->string('insurance_emloyee')->nullable();
            $table->string('insurance_retirement')->nullable();
            $table->string('insurance_coverage')->nullable();
            $table->string('insurance_employee_dependent')->nullable();
            $table->string('insurance_coverage_basic_option')->nullable();
            $table->string('basic_option_select')->nullable();
            $table->string('insurance_coverage_a_option')->nullable();
            $table->string('insurance_coverage_b_option')->nullable();
            $table->text('option_b_value')->nullable();
            $table->text('insurance_coverage_c_option')->nullable();
            $table->string('insurance_employee_coverage_c')->nullable();
            $table->string('insurance_employee_coverage_pp')->nullable();
            $table->string('insurance_employee_coverage_age')->nullable();
            $table->string('insurance_employee_coverage_self_age')->nullable();
            $table->string('insurance_analysis')->nullable();
            // FEDERAL EMPLOYEE HEALTH BENEFITS (FEHB) VARIOUS PLANS
            $table->string('federal')->nullable();
            $table->string('plan_type')->nullable();
            $table->string('premium')->nullable();
            $table->string('coverage')->nullable();
            $table->string('coverage_retirement')->nullable();
            $table->string('coverage_retirement_dependent')->nullable();
            $table->string('coverage_retirement_insurance')->nullable();
            $table->string('coverage_retirement_insurance_why')->nullable();
            $table->string('coverage_retirement_insurance_who')->nullable();//check this colummn in front end
            // FEDERAL DENTAL AND VISION INSURANCE PLAN (FEDVIP) VARIOUS PLANS
            $table->string('dental')->nullable();
            $table->string('dental_retirement')->nullable();
            $table->string('dental_premium')->nullable();
            $table->string('vision')->nullable();
            $table->string('vision_retirement')->nullable();
            $table->string('vision_premium')->nullable();
            $table->string('vision_total_cost')->nullable();
            // FEDERAL LONG TERM CARE INSURANCE PROGRAM (FLTCIP) JOHN HANCOCK
            $table->string('insurance_program')->nullable();//
            $table->integer('insurance_age')->nullable();//
            $table->string('insurance_purchase_premium')->nullable();//
            $table->string('insurance_program_retirement')->nullable();//
            $table->string('insurance_program_plan')->nullable();//
            $table->string('insurance_program_daily')->nullable();//
            $table->string('insurance_purpose_coverage')->nullable();//
            $table->string('insurance_program_purpose')->nullable();//
            $table->string('max_lifetime')->nullable();//
            $table->longText('notes')->nullable();//

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_plans');
    }
};
