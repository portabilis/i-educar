<?php

use Phinx\Migration\AbstractMigration;

class RefatoraViewHistoricoNoveAnos extends AbstractMigration
{
    public function change()
    {
        $this->execute("CREATE EXTENSION IF NOT EXISTS hstore;");
        $this->execute("DROP VIEW relatorio.view_historico_9anos;");
        $sql = file_get_contents(__DIR__.'/../../../modules/Reports/DbViews/relatorio_view_historico_9anos.sql');
        $this->execute($sql);
    }
}