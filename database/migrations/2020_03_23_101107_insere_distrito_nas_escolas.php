<?php

use App\Models\LegacyPersonAddress;
use App\Models\LegacySchool;
use App\Models\PersonHasPlace;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsereDistritoNasEscolas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'update pmieducar.escola
                set iddis = data.id
                from (
                select d.id, e.iddis, e.cod_escola, d.city_id, count(d.city_id)
                  from pmieducar.escola e
                 inner join public.person_has_place php on php.person_id = e.ref_idpes
                 inner join public.places p on p.id = php.place_id
                 inner join public.districts d on p.city_id = d.city_id
                where e.iddis is null
                group by e.iddis, e.cod_escola, d.city_id, d.id
                having count(d.city_id) = 1) as data
                where escola.cod_escola = data.cod_escola;';
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('update pmieducar.escola set iddis = null');
    }
}
