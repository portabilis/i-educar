<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchemasToAttendanceFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(<<<'SQL'
            CREATE OR REPLACE FUNCTION modules.frequencia_da_matricula(p_matricula_id integer)
            RETURNS double precision AS
            $BODY$
                DECLARE
                    v_regra_falta integer;
                    v_falta_aluno_id  integer;
                    v_qtd_dias_letivos_serie NUMERIC;
                    v_total_faltas integer;
                    v_qtd_horas_serie integer;
                    v_hora_falta FLOAT;
                BEGIN
                    /*
                        regra_falta:
                        1- Global
                        2- Por componente
                    */
                    v_regra_falta:= (SELECT regra_avaliacao.tipo_presenca
                                    FROM pmieducar.matricula
                                                INNER JOIN pmieducar.serie ON serie.cod_serie = matricula.ref_ref_cod_serie
                                                INNER JOIN modules.regra_avaliacao_serie_ano rasa ON serie.cod_serie = rasa.serie_id
                                        AND rasa.ano_letivo = matricula.ano
                                                INNER JOIN modules.regra_avaliacao ON regra_avaliacao.id = rasa.regra_avaliacao_id
                                    WHERE matricula.cod_matricula = p_matricula_id);
                    v_falta_aluno_id := (SELECT id
                                            FROM modules.falta_aluno
                                            WHERE matricula_id = p_matricula_id
                                            ORDER BY id DESC
                                            LIMIT 1 );
                    IF (v_regra_falta = 1) THEN
                            v_qtd_dias_letivos_serie := (SELECT s.dias_letivos
                                                        FROM pmieducar.serie s
                                                        INNER JOIN pmieducar.matricula m ON (m.ref_ref_cod_serie = s.cod_serie)
                                                        WHERE m.cod_matricula = p_matricula_id);
                            v_total_faltas := (SELECT SUM(quantidade)
                                                FROM modules.falta_geral
                                                WHERE falta_aluno_id = v_falta_aluno_id);
                            RETURN TRUNC((((v_qtd_dias_letivos_serie - v_total_faltas) * 100 ) / v_qtd_dias_letivos_serie ),2);
                    ELSE

                        v_qtd_horas_serie := ( SELECT s.carga_horaria
                                                FROM pmieducar.serie s
                                                INNER JOIN pmieducar.matricula m ON (m.ref_ref_cod_serie = s.cod_serie)
                                                WHERE m.cod_matricula = p_matricula_id);

                        v_total_faltas := (SELECT SUM(quantidade)
                                            FROM modules.falta_componente_curricular
                                            WHERE falta_aluno_id = v_falta_aluno_id);
                        v_hora_falta := (SELECT hora_falta
                                        FROM pmieducar.curso c
                                        INNER JOIN pmieducar.matricula m ON (c.cod_curso = m.ref_cod_curso)
                                        WHERE m.cod_matricula = p_matricula_id);
                        RETURN  (100 - ((v_total_faltas * (v_hora_falta*100))/v_qtd_horas_serie));
                    END IF;
                END;
                $BODY$
            LANGUAGE plpgsql VOLATILE
            COST 100;
SQL
        );
    }
}
