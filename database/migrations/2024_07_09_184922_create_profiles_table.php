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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('title')->nullable();
            $table->text('company_name')->nullable();
            $table->text('phone_1')->nullable();
            $table->text('phone_1_type')->nullable();
            $table->text('phone_2')->nullable();
            $table->text('phone_2_type')->nullable();
            $table->text('address')->nullable();
            $table->text('city')->nullable();
            $table->text('statement')->nullable();
            $table->text('image')->nullable();
            $table->text('bg_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
