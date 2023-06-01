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
        Schema::create('intern_forms', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id');
            $table->foreign('job_id')->references('job_id')->on('job_forms');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('pdf');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intern_forms');
    }
};
