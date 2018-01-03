<?php

use Phinx\Migration\AbstractMigration;

class AlteraAnoInstituicao extends AbstractMigration
{
    /**
     * Referente a task https://sprint.ly/product/18123/item/2072
     *
     * Afim de reduzir possiveis problemas com os processos envolvendo estes campos
     * será alterado somente o ano, quando os mesmos estiverem preenchidos.
     *
     * Este processo devera ser realizado anualmente
     *
     */
    public function change()
    {
        $anoCorrente = date('Y');

        $this->execute("update pmieducar.instituicao set data_base_transferencia = to_date('{$anoCorrente}'||substring(to_char(data_base_transferencia, 'YYYY-MM-DD'), 5), 'YYYY-MM-DD') where data_base_transferencia is not null;");
        $this->execute("update pmieducar.instituicao set data_base_remanejamento = to_date('{$anoCorrente}'||substring(to_char(data_base_remanejamento, 'YYYY-MM-DD'), 5), 'YYYY-MM-DD') where data_base_remanejamento is not null;");
        $this->execute("update pmieducar.instituicao set data_expiracao_reserva_vaga = to_date('{$anoCorrente}'||substring(to_char(data_expiracao_reserva_vaga, 'YYYY-MM-DD'), 5), 'YYYY-MM-DD') where data_expiracao_reserva_vaga is not null;");
    }
}
