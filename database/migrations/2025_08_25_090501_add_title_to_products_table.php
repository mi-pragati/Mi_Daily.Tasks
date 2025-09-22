<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'title')) {
                $table->string('title')->nullable()->after('product_category_id');
            }
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('stock');
            }
            if (!Schema::hasColumn('products', 'media')) {
                $table->string('media')->nullable()->after('image');
            }
        });
    }

    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'title')) $table->dropColumn('title');
            if (Schema::hasColumn('products', 'image')) $table->dropColumn('image');
            if (Schema::hasColumn('products', 'media')) $table->dropColumn('media');
        });
    }
};
