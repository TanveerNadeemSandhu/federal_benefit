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
        Schema::create('t_s_p_calculates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fed_case_id');
            $table->foreign('fed_case_id')->references('id')->on('fed_cases')->onDelete('cascade');
            $table->float('gFund')->nullable();
            $table->float('fFund')->nullable();
            $table->float('cFund')->nullable();
            $table->float('sFund')->nullable();
            $table->float('iFund')->nullable();
            $table->float('lFund')->nullable();
            $table->float('l2025Fund')->nullable();
            $table->float('l2030Fund')->nullable();
            $table->float('l2035Fund')->nullable();
            $table->float('l2040Fund')->nullable();
            $table->float('l2045Fund')->nullable();
            $table->float('l2050Fund')->nullable();
            $table->float('l2055Fund')->nullable();
            $table->float('l2060Fund')->nullable();
            $table->float('l2065Fund')->nullable();
            $table->float('totalContribution')->nullable();
            $table->float('totalMatching')->nullable();
            $table->float('totalTSPCalculate')->nullable();
            $table->float('matchingPercentage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_s_p_calculates');
    }
};
