<?php

use Phinx\Migration\AbstractMigration;

class MigraValoresIniciaisParaAnosLetivos extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            UPDATE pmieducar.escola_curso
            set anos_letivos = myq.anos_letivos
            from (select ref_cod_escola, array_agg(distinct ano) as anos_letivos
            from pmieducar.escola_ano_letivo
            group by ref_cod_escola) as myq
            where myq.ref_cod_escola = escola_curso.ref_cod_escola;

            UPDATE pmieducar.escola_serie
            set anos_letivos = myq.anos_letivos
            from (select ref_cod_escola, array_agg(distinct ano) as anos_letivos
            from pmieducar.escola_ano_letivo
            group by ref_cod_escola) as myq
            where myq.ref_cod_escola = escola_serie.ref_cod_escola;

            UPDATE pmieducar.escola_serie_disciplina
            set anos_letivos = myq.anos_letivos
            from (select ref_cod_escola, array_agg(distinct ano) as anos_letivos
            from pmieducar.escola_ano_letivo
            group by ref_cod_escola) as myq
            where myq.ref_cod_escola = escola_serie_disciplina.ref_ref_cod_escola;

            UPDATE modules.componente_curricular_ano_escolar
            set anos_letivos = myq.anos_letivos
            from (
                SELECT serie, disciplina, array_agg(distinct  anos_letivos ) as anos_letivos
                    FROM (select ref_ref_cod_serie serie, ref_cod_disciplina as disciplina, unnest(anos_letivos) as anos_letivos
                from pmieducar.escola_serie_disciplina) AS myqq
                group by serie, disciplina
            ) as myq
            where myq.serie = ano_escolar_id
            AND myq.disciplina = componente_curricular_id;

            INSERT INTO modules.regra_avaliacao_serie_ano
            (serie_id, regra_avaliacao_id, regra_avaliacao_diferenciada_id, ano_letivo)
            SELECT distinct serie, s.regra_avaliacao_id, s.regra_avaliacao_diferenciada_id, anos_letivos
            FROM (
            SELECT ref_cod_serie serie, unnest(anos_letivos) as anos_letivos
            FROM pmieducar.escola_serie
            ) AS myqq
            JOIN pmieducar.serie s
            ON s.cod_serie = serie
            WHERE s.regra_avaliacao_id IS NOT NULL;
        ");
    }

    public function down()
    {
        $this->execute("
            DELETE FROM modules.regra_avaliacao_serie_ano;
            UPDATE modules.componente_curricular_ano_escolar SET anos_letivos = '{}';
            UPDATE pmieducar.escola_serie_disciplina SET anos_letivos = '{}';
            UPDATE pmieducar.escola_serie SET anos_letivos = '{}';
            UPDATE pmieducar.escola_curso SET anos_letivos = '{}';
        ");
    }
}
