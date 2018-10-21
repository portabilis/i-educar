<?php

use Phinx\Migration\AbstractMigration;

class AjustaEtapasDosModulos extends AbstractMigration
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
        $this->execute("
            update pmieducar.modulo set num_etapas = 4 where trim(lower(nm_tipo)) in ('bimestre', 'bimestral');
            update pmieducar.modulo set num_etapas = 3 where trim(lower(nm_tipo)) in ('trimestre', 'trimestral');
            update pmieducar.modulo set num_etapas = 2 where trim(lower(nm_tipo)) in ('semestre', 'semestral');
            update pmieducar.modulo set num_etapas = 1 where trim(lower(nm_tipo)) in ('anual');
        ");

        $modules = $this->fetchAll('select cod_modulo from pmieducar.modulo where num_etapas = 0 order by cod_modulo asc;');

        if (!empty($modules)) {
            $query = "
                select
                    etapas.total AS etapas
                from
                    pmieducar.modulo
                inner join
                    (
                        select
                            ref_cod_modulo,
                            total,
                            count(*) AS count
                        from
                            (
                                select
                                    ref_cod_modulo,
                                    count(*) AS total
                                from
                                    pmieducar.ano_letivo_modulo
                                inner join pmieducar.escola_ano_letivo on true
                                    and escola_ano_letivo.ano = ano_letivo_modulo.ref_ano
                                    and escola_ano_letivo.ref_cod_escola = ano_letivo_modulo.ref_ref_cod_escola
                                    and escola_ano_letivo.ativo = 1
                                group by
                                    ref_ano,
                                    ref_ref_cod_escola,
                                    ref_cod_modulo
                                order by
                                    ref_cod_modulo
                            ) AS etapas_count
                        group by
                            ref_cod_modulo,
                            total
                        order by
                            ref_cod_modulo asc,
                            total desc
                    ) as etapas on true
                    and etapas.ref_cod_modulo = modulo.cod_modulo
                where true
                    and modulo.cod_modulo = %d
                order by
                    modulo.cod_modulo asc,
                    etapas.count desc
                limit 1
            ";

            foreach ($modules as $module) {
                $q = sprintf($query, $module['cod_modulo']);
                $results = $this->fetchRow($q);

                if (empty($results)) {
                    continue;
                }

                $this->execute(sprintf(
                    "update pmieducar.modulo set num_etapas = %d where cod_modulo = %d;",
                    $results['etapas'],
                    $module['cod_modulo']
                ));
            }
        }
    }
}
