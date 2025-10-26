<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained();
            $table->enum('transaction_type', ['inbound', 'outbound']);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('processed_by');
            $table->timestamp('processed_at');
            $table->timestamps();
            
            $table->index(['transaction_type', 'created_at']);
            $table->index('processed_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};