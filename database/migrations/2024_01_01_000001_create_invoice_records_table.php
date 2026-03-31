<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_records', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->decimal('price', 10, 2);
            $table->date('document_date');        // Date from PDF (e.g. "29 Apr 2025")
            $table->string('tax_invoice_id')->nullable();
            $table->string('pdf_filename')->nullable();
            $table->integer('impressions')->default(0);
            $table->string('campaign_type')->nullable();
            $table->boolean('is_merged')->default(false);
            $table->string('merged_name')->nullable();
            $table->unsignedBigInteger('merged_group_id')->nullable(); // groups merged records
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_records');
    }
};