<?php

use Phinx\Migration\AbstractMigration;

class CriaTabelaHistoricoAlturaPesoAluno extends AbstractMigration
{
    public function change()
    {
        $this->execute("CREATE TABLE pmieducar.aluno_historico_altura_peso (
                            ref_cod_aluno INTEGER NOT NULL,
                            data_historico DATE NOT NULL,
                            altura NUMERIC(12,2) NOT NULL,
                            peso NUMERIC(12,2) NOT NULL,
                            FOREIGN KEY(ref_cod_aluno)
                            REFERENCES pmieducar.aluno(cod_aluno)
                        );");
    }
}
