<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add 'role' column only if it doesn't already exist
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('editor')->after('email'); // default 'editor' for registered users
            });

            // Backfill existing users with 'editor' role if it's NULL
            DB::table('users')->whereNull('role')->update(['role' => 'editor']);
        }

        // Add company-related fields only if they don't already exist
        if (!Schema::hasColumn('users', 'company_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('company_name')->nullable()->after('role');
            });
        }

        if (!Schema::hasColumn('users', 'company_address')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('company_address')->nullable()->after('company_name');
            });
        }

        if (!Schema::hasColumn('users', 'company_phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('company_phone')->nullable()->after('company_address');
            });
        }
    }

    public function down()
    {
        // Drop columns only if they exist
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        if (Schema::hasColumn('users', 'company_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('company_name');
            });
        }

        if (Schema::hasColumn('users', 'company_address')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('company_address');
            });
        }

        if (Schema::hasColumn('users', 'company_phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('company_phone');
            });
        }
    }
};
