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
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('phone_code', 5)->nullable()->after('language');
            $table->string('phone_number', 15)->nullable()->after('phone_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['phone_code', 'phone_number']);
        });
    }
};
