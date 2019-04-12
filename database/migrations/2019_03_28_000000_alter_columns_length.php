<?php

use App\Support\Database\AsView;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnsLength extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('portal.v_funcionario');

        Schema::table('portal.funcionario', function (Blueprint $table) {
            $table->string('senha')->nullable()->change();
            $table->string('status_token')->nullable()->change();
        });

        $this->createView('portal.v_funcionario');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
