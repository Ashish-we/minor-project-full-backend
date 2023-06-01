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
        Schema::create('job_forms', function (Blueprint $table) {
            $table->id('job_id');
            $table->string('title');
            $table->string('description');
            $table->string('skills');
            $table->date('due_date')->format('d-m-Y');
            $table->integer('company_id')->unsigned();
            // $table->boolean('status')->default(1)->comment("1 for active 0 for expire");
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_forms');
    }
};
