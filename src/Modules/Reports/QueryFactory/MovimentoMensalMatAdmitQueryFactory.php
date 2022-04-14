<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalMatAdmitQueryFactory extends MovimentoMensalDetalheQueryFactory
{
    public function where()
    {
        return 'matricula_ativa and sequencial = 1 and entrada_reclassificado = false and entrou_durante';
    }
}
