<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up():void
{
    Schema::create('products', function (Blueprint $table) {
         $table->id();
$table->string('title'); // <-- changed from name to title
$table->string('slug')->unique();
$table->foreignId('product_category_id')->nullable()->constrained()->nullOnDelete();
$table->enum('status', ['draft', 'published'])->default('draft');
$table->text('description')->nullable();
$table->decimal('price', 10, 2)->default(0);
$table->unsignedInteger('stock')->default(0);
$table->unsignedBigInteger('user_id')->nullable();
$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
$table->string('image')->nullable();
$table->timestamps();

        });
}

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
