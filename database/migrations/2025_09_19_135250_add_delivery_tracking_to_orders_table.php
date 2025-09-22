<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        // Add only return_status since is_reordered already exists
        if (!Schema::hasColumn('orders', 'return_status')) {
            $table->enum('return_status', ['Pending', 'Accepted', 'Declined'])->nullable();
        }
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        if (Schema::hasColumn('orders', 'return_status')) {
            $table->dropColumn('return_status');
        }
    });
}


};
