<?php

use Phinx\Migration\AbstractMigration;

class CriaViewHistoricoSeriesAnos extends AbstractMigration
{
    public function change()
    {
        $sql = file_get_contents(__DIR__.'/../../../modules/Reports/DbViews/relatorio_view_historico_series_anos.sql');
        $this->execute($sql);
    }
}
