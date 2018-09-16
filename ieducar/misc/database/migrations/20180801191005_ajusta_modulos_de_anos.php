<?php

use Phinx\Migration\AbstractMigration;

class AjustaModulosDeAnos extends AbstractMigration
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
        $this->execute("SELECT SETVAL('pmieducar.modulo_cod_modulo_seq', (SELECT MAX(cod_modulo) + 1 FROM pmieducar.modulo));");

        $query = "
            select
                ref_ano,
                ref_ref_cod_escola AS ref_cod_escola,
                count(*) AS etapas
            from
                pmieducar.ano_letivo_modulo
            inner join pmieducar.escola_ano_letivo on true
                and escola_ano_letivo.ref_cod_escola = ano_letivo_modulo.ref_ref_cod_escola
                and escola_ano_letivo.ano = ano_letivo_modulo.ref_ano
            where true
                and escola_ano_letivo.ativo = 1
            group by
                ref_ano,
                ref_ref_cod_escola
            order by
                ref_ano,
                ref_cod_escola
        ";

        $records = $this->fetchAll($query);

        foreach ($records as $record) {
            $steps = $this->fetchAll(sprintf(
                'select * from pmieducar.ano_letivo_modulo where ref_ano = %d and ref_ref_cod_escola = %d',
                $record['ref_ano'],
                $record['ref_cod_escola']
            ));

            $modules = [];
            $module = 0;

            foreach ($steps as $step) {
                $modules[] = $step['ref_cod_modulo'];
            }

            $modules = array_unique($modules);

            foreach ($modules as $mod) {
                $m = $this->fetchRow(sprintf('select * from pmieducar.modulo where cod_modulo = %d', $mod));

                if ((int)$m['num_etapas'] === (int)$record['etapas']) {
                    $module = (int)$mod;
                    break;
                }
            }

            if ($module === 0) {
                $m = $this->fetchRow(sprintf('select * from pmieducar.modulo where num_etapas = %d limit 1', $record['etapas']));

                if ($m) {
                    if ((bool)$m['ativo'] === false) {
                        $this->execute(sprintf('update pmieducar.modulo set ativo = 1 where cod_modulo = %d;', (int)$m['cod_modulo']));
                    }

                    $module = (int)$m['cod_modulo'];
                }
            }

            if ($module === 0) {
                $module = $this->createModule((int)$record['etapas']);
            }

            $query = sprintf(
                'UPDATE pmieducar.ano_letivo_modulo SET ref_cod_modulo = %d WHERE ref_ano = %d AND ref_ref_cod_escola = %d',
                $module,
                $record['ref_ano'],
                $record['ref_cod_escola']
            );

            $this->execute($query);
        }
    }

    private function createModule(int $steps) {
        $map = [
            1 => 'Anual',
            2 => 'Semestral',
            3 => 'Trimestral',
            4 => 'Bimestral',
        ];

        $data = [
            'ref_usuario_cad' => "'1'",
            'nm_tipo' => !empty($map[$steps]) ? "'$map[$steps]'" : sprintf("'Módulo %d etapas'", $steps),
            'num_meses' => "'1'",
            'num_semanas' => "'1'",
            'data_cadastro' => 'NOW()',
            'ativo' => "'1'",
            'ref_cod_instituicao' => "'1'",
            'num_etapas' => "'$steps'"
        ];

        $query = sprintf(
            'INSERT INTO pmieducar.modulo (%s) VALUES (%s) RETURNING cod_modulo',
            join(', ', array_keys($data)),
            join(', ', $data)
        );

        $result = $this->fetchRow($query);

        return (int)$result['cod_modulo'];
    }
}
