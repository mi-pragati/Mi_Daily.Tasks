<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('editor')->after('email'); // editor for registered users
            $table->string('company_name')->nullable()->after('role');
            $table->text('company_address')->nullable()->after('company_name');
            $table->string('company_phone')->nullable()->after('company_address');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role','company_name','company_address','company_phone']);
        });
    }
};
