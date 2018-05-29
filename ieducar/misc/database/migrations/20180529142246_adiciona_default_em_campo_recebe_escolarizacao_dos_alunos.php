<?php

use Phinx\Migration\AbstractMigration;

class AdicionaDefaultEmCampoRecebeEscolarizacaoDosAlunos extends AbstractMigration
{
    public function up()
    {
        $sql = "ALTER TABLE pmieducar.aluno ALTER COLUMN recebe_escolarizacao_em_outro_espaco SET DEFAULT 3;
                ALTER TABLE pmieducar.aluno ALTER COLUMN recebe_escolarizacao_em_outro_espaco SET NOT NULL; ";
        $this->execute($sql);
    }

    public function down()
    {
        $sql = "ALTER TABLE pmieducar.aluno ALTER COLUMN recebe_escolarizacao_em_outro_espaco DROP NOT NULL;
                ALTER TABLE pmieducar.aluno ALTER COLUMN recebe_escolarizacao_em_outro_espaco DROP DEFAULT; ";
        $this->execute($sql);
    }
}
