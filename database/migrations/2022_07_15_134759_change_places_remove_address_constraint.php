<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::table('public.places', function (Blueprint $table) {
            $table->integer('city_id')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('neighborhood')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('public.places', function (Blueprint $table) {
            $table->integer('city_id')->change();
            $table->string('address')->change();
            $table->string('neighborhood')->change();
            $table->string('postal_code')->change();
        });
    }
};
