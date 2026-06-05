<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('color')->nullable();
            $table->string('frame_material')->nullable(); // e.g. acetate, titanium, TR90
            $table->string('lens_type')->nullable();      // e.g. polarized, mirrored, gradient
            $table->string('lens_color')->nullable();
            $table->string('size')->nullable();           // e.g. small, medium, large
            $table->decimal('price_modifier', 8, 2)->default(0);
            $table->integer('stock_qty')->default(0);
            $table->string('sku')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
