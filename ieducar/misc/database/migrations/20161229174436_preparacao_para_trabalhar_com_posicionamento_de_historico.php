<?php

use Phinx\Migration\AbstractMigration;

class PreparacaoParaTrabalharComPosicionamentoDeHistorico extends AbstractMigration
{
    $this->execute("INSERT INTO pmieducar.historico_grade_curso VALUES (3, 'EJA', NOW(), null, null, 1)");

    $this->execute("ALTER TABLE pmieducar.instituicao ADD COLUMN controlar_posicao_historicos boolean;");

    $this->execute("ALTER TABLE pmieducar.historico_escolar ADD COLUMN posicao integer;");
}
