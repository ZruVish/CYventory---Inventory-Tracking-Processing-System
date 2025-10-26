<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('category', 100);
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 10, 2);
            $table->integer('reorder_level')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['category', 'status']);
            $table->index('item_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventories');
    }
};