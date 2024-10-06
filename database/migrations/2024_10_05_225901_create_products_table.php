<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_set_id')->constrained()->onDelete('cascade');
            $table->string('sku');
            $table->string('name', 40);
            $table->string('slug');
            $table->enum('type', ['device', 'service']);
            $table->enum('condition', ['new', 'used', 'refurbished']);
            $table->string('description_title');
            $table->text('description_text');
            $table->decimal('price');
            $table->decimal('wholesale_price')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
