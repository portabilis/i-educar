<?php

use Phinx\Migration\AbstractMigration;

class AbreviaturasFuncionarioVinculo extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            UPDATE
                portal.funcionario_vinculo
            SET
                abreviatura = (
                    CASE
                        WHEN cod_funcionario_vinculo = 3 THEN
                            'Efet'
                        WHEN cod_funcionario_vinculo = 4 THEN
                            'Cont'
                        WHEN cod_funcionario_vinculo = 5 THEN
                            'Com'
                        WHEN cod_funcionario_vinculo = 6 THEN
                            'Est'
                    END
                );
        ");

        $this->execute("SELECT SETVAL('portal.funcionario_vinculo_cod_funcionario_vinculo_seq', (SELECT MAX(cod_funcionario_vinculo) + 1 FROM portal.funcionario_vinculo));");
    }

    public function down()
    {
        $this->execute("
            UPDATE
                portal.funcionario_vinculo
            SET
                abreviatura = NULL;
        ");
    }
}
