<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(){
        Schema::create('vat_invoice_updates', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('vat_invoice_id')->nullable();
            $table->json('updated_data');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('description', ['add', 'update', 'delete'])->default('update');
            $table->timestamps();
            $table->foreign('vat_invoice_id')->references('id')->on('vat_invoices')->onDelete('cascade');
        });
        
    }
    public function down(){
        Schema::dropIfExists('vat_invoice_updates');
    }
};
