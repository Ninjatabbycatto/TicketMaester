<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('order_column')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('clinic_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('backlog_time')->nullable();
            $table->timestamp('inprogress_time')->nullable();
            $table->timestamp('acknowledged_time')->nullable();
            $table->string('status')->default('new');
            $table->string('priority')->nullable()->default('normal');
            $table->unsignedBigInteger('taken_by')->nullable(); 
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('taken_by')->references('id')->on('users')->onDelete('set null');
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
