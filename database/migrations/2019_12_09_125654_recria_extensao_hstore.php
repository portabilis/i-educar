<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

class RecriaExtensaoHstore extends Migration
{
    use AsView;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('relatorio.view_historico_9anos;');
        $this->dropView('relatorio.view_historico_series_anos;');
        $this->dropView('relatorio.view_historico_eja;');
        $this->dropView('relatorio.view_historico_eja_extra_curricular;');
        $this->dropView('relatorio.view_historico_series_anos_extra_curricular;');
        $this->dropView('relatorio.view_historico_series_anos_extrsa_curricular;');

        DB::unprepared('DROP EXTENSION IF EXISTS hstore;');
        DB::unprepared('CREATE EXTENSION IF NOT EXISTS hstore WITH SCHEMA relatorio;');

        $this->createView('relatorio.view_historico_9_anos-2019-09-12');
        $this->createView('relatorio.view_historico_series_anos-2019-09-12');
        $this->createView('relatorio.view_historico_eja-2019-09-12');
        $this->createView('relatorio.view_historico_eja_extra_curricular-2019-09-12');
        $this->createView('relatorio.view_historico_series_anos_extra_curricular-2019-09-12');
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
