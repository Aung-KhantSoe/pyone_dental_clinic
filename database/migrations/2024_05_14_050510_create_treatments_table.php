<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreatmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')
                    ->references('id')->on('doctors')
                    ->onDelete('cascade');
            $table->foreignId('patient_id')
                    ->references('id')->on('patients')
                    ->onDelete('cascade');
            $table->foreignId('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade')->nullable();
            $table->text('treatment_type');
            $table->text('diagnosis')->nullable();
            $table->date('treatment_date');
            $table->double("treatment_charges");
            $table->double("xray_fees")->nullable();
            $table->double("medication_fees")->nullable();
            $table->double("total");
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
        Schema::dropIfExists('treatments');
    }
}
