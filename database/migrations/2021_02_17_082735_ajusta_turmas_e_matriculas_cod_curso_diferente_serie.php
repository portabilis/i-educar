<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AjustaTurmasEMatriculasCodCursoDiferenteSerie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        SET "audit.context" = \'{"user_id" : 0, "user_name" : "Rodrigo Cabral", "origin": "Issue 8222"}\';
        UPDATE pmieducar.turma t
        SET ref_cod_curso = s.ref_cod_curso
        FROM pmieducar.serie s
        WHERE t.ref_ref_cod_serie = s.cod_serie
        AND t.ref_cod_curso != s.ref_cod_curso;

        UPDATE pmieducar.matricula m
        SET ref_cod_curso = s.ref_cod_curso
        FROM pmieducar.serie s
        WHERE m.ref_ref_cod_serie = s.cod_serie
        AND m.ref_cod_curso != s.ref_cod_curso;
        ');
    }
}
