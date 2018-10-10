<?php

use Phinx\Migration\AbstractMigration;

class AjustaModulosDeTurmas extends AbstractMigration
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

        $generator = function () {
            $id = 0;

            do {
                $query = sprintf(
                    "
                        select
                            turma_modulo.ref_cod_turma,
                            count(*) as etapas
                        from
                            pmieducar.turma_modulo
                        inner join pmieducar.turma on true
                            and turma.cod_turma = turma_modulo.ref_cod_turma
                        where true
                            and turma.ativo = 1
                            and turma.cod_turma > %d
                        group by
                            turma_modulo.ref_cod_turma
                        order by
                            turma_modulo.ref_cod_turma
                        limit 1
                    ",
                    $id
                );

                $result = $this->fetchRow($query);

                if ($result) {
                    yield $result;

                    $id = $result['ref_cod_turma'];
                }
            } while ($result);
        };

        foreach ($generator() as $record) {
            $steps = $this->fetchAll(sprintf(
                'select * from pmieducar.turma_modulo where ref_cod_turma = %d',
                $record['ref_cod_turma']
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
                'UPDATE pmieducar.turma_modulo SET ref_cod_modulo = %d WHERE ref_cod_turma = %d',
                $module,
                $record['ref_cod_turma']
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
