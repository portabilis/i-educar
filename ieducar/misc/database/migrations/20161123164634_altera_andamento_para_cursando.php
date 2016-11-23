<?php

use Phinx\Migration\AbstractMigration;

class AlteraAndamentoParaCursando extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE relatorio.situacao_matricula SET descricao = 'Cursando' WHERE cod_situacao = 3;");
;    }
}
