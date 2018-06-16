<?php

use Phinx\Migration\AbstractMigration;

class AdicionaDependenciasSchemaRelatorio extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $sql = <<<'SQL'

        CREATE OR REPLACE FUNCTION relatorio.get_nome_escola(integer) RETURNS character varying
            LANGUAGE sql
            AS $_$SELECT COALESCE(
               (SELECT COALESCE (fcn_upper(ps.nome),fcn_upper(juridica.fantasia))
            FROM cadastro.pessoa ps, cadastro.juridica
           WHERE escola.ref_idpes = juridica.idpes
             AND juridica.idpes = ps.idpes
             AND ps.idpes = escola.ref_idpes),
               (SELECT nm_escola
            FROM pmieducar.escola_complemento
           WHERE ref_cod_escola = escola.cod_escola))
          FROM pmieducar.escola
         WHERE escola.cod_escola = $1;$_$;


        ALTER FUNCTION relatorio.get_nome_escola(integer) OWNER TO current_user;


        CREATE OR REPLACE VIEW relatorio.view_componente_curricular AS
        ( SELECT escola_serie_disciplina.ref_cod_disciplina AS id,
            turma.cod_turma,
            componente_curricular.nome,
            componente_curricular.abreviatura,
            componente_curricular.ordenamento,
            componente_curricular.area_conhecimento_id,
            escola_serie_disciplina.etapas_especificas,
            escola_serie_disciplina.etapas_utilizadas,
            escola_serie_disciplina.carga_horaria
           FROM (((pmieducar.turma
             JOIN pmieducar.escola_serie_disciplina ON (((escola_serie_disciplina.ref_ref_cod_serie = turma.ref_ref_cod_serie) AND (escola_serie_disciplina.ref_ref_cod_escola = turma.ref_ref_cod_escola) AND (escola_serie_disciplina.ativo = 1))))
             JOIN modules.componente_curricular ON (((componente_curricular.id = escola_serie_disciplina.ref_cod_disciplina) AND (( SELECT count(cct.componente_curricular_id) AS count
                   FROM modules.componente_curricular_turma cct
                  WHERE (cct.turma_id = turma.cod_turma)) = 0))))
             JOIN modules.area_conhecimento ON ((area_conhecimento.id = componente_curricular.area_conhecimento_id)))
          ORDER BY area_conhecimento.ordenamento_ac, area_conhecimento.nome, componente_curricular.ordenamento, componente_curricular.nome)
        UNION ALL
        ( SELECT componente_curricular_turma.componente_curricular_id AS id,
            componente_curricular_turma.turma_id AS cod_turma,
            componente_curricular.nome,
            componente_curricular.abreviatura,
            componente_curricular.ordenamento,
            componente_curricular.area_conhecimento_id,
            componente_curricular_turma.etapas_especificas,
            componente_curricular_turma.etapas_utilizadas,
            componente_curricular_turma.carga_horaria
           FROM ((modules.componente_curricular_turma
             JOIN modules.componente_curricular ON ((componente_curricular.id = componente_curricular_turma.componente_curricular_id)))
             JOIN modules.area_conhecimento ON ((area_conhecimento.id = componente_curricular.area_conhecimento_id)))
          ORDER BY area_conhecimento.ordenamento_ac, area_conhecimento.nome, componente_curricular.ordenamento, componente_curricular.nome);


        ALTER TABLE relatorio.view_componente_curricular OWNER TO current_user;

SQL;

        $this->execute($sql);
    }
}
