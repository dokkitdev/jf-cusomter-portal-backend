<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrivateContractsCreateTable extends Migration
{
    public function up(): void
    {
        Schema::create('private_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('simpro_recurring_invoice_id');
            $table->timestamp('next_recurring_date');
            $table
                ->string('direct_date')
                ->nullable();
            $table
                ->string('direct_month')
                ->nullable();
            $table
                ->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();
            $table->foreignId('site_id')
                ->nullable()
                ->constrained('sites')
                ->nullOnDelete();
            $table
                ->string('recurring_type')
                ->nullable();
            $table
                ->string('company_name')
                ->nullable();
            $table
                ->string('period')
                ->nullable();
            $table
                ->string('payer_reference')
                ->nullable();
            $table
                ->string('payer_account_name')
                ->nullable();
            $table
                ->enum('payment_type', ['Annual payment', 'Direct Debit']);
            $table
                ->boolean('is_company')
                ->default(false);
            $table
                ->boolean('is_processed')
                ->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_contracts');
    }
}
