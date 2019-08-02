<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class FixDataInDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('UPDATE cadastro.documento SET cartorio_cert_civil = SUBSTRING(cartorio_cert_civil, 0, 190) where LENGTH(cartorio_cert_civil::TEXT) > 190');
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
