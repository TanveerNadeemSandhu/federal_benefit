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
        Schema::create('fed_cases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('share_user_id')->nullable();
            $table->enum('status', ['New', 'For bos review', 'Need information', 'Completed'])->default('New');
            // employee information
            $table->string('name')->nullable();
            $table->date('dob')->nullable();
            $table->text('age')->nullable();
            $table->string('spouse_name')->nullable();
            $table->date('spouse_dob')->nullable();
            $table->text('spouse_age')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Retirement Information
            $table->string('retirement_system')->nullable();
            $table->date('retirement_system_csrs_offset')->nullable();
            $table->date('retirement_system_fers_transfer')->nullable();
            // employee type
            $table->string('employee_type')->nullable();
            // PENSION AND ELIGIBILITY
            $table->date('lscd')->nullable();
            $table->date('rscd')->nullable();
            $table->date('scd')->nullable();
            // RETIREMENT TYPE
            $table->string('retirement_type')->nullable();
            $table->text('retirement_type_age')->nullable();
            $table->date('retirement_type_date')->nullable();
            $table->date('retirement_type_voluntary')->nullable();
            // CURRENT LEAVE HOURS
            $table->string('current_hours_option')->nullable();
            $table->string('current_leave_option')->nullable();
            $table->integer('annual_leave_hours')->nullable();
            $table->integer('sick_leave_hours')->nullable();
            // INCOME AND PENSION VALUES
            $table->string('income_employee_option')->nullable();
            $table->text('salary_1')->nullable();
            $table->text('salary_2')->nullable();
            $table->text('salary_3')->nullable();
            $table->text('salary_4')->nullable();
            // SURVIVOR BENEFIT
            $table->string('employee_spouse')->nullable();
            $table->string('survior_benefit_fers')->nullable();
            $table->string('survior_benefit_csrs')->nullable();
            // SOCIAL SECURITY INCOME AND SPECIAL RETIREMENT SUPPLEMENT (SRS)
            $table->string('employee_eligible')->nullable();
            $table->text('amount_1')->nullable();
            $table->text('amount_2')->nullable();
            $table->text('amount_3')->nullable();

            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fed_cases');
    }
};
