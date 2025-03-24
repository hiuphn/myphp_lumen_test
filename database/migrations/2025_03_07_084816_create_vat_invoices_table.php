<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('vat_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('short_name', 30)->unique();
            $table->string('business_name', 70);
            $table->string('buyer_name', 50);
            $table->string('tax_code', 20)->unique();
            $table->string('invoice_address', 255);
            $table->string('invoice_email', 255);
            $table->string('receiver_name', 20);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('vat_invoices');
    }
};
