<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalMatTrocaeQueryFactory extends MovimentoMensalDetalheQueryFactory
{
    public function where()
    {
        return 'matricula_ativa and entrou_durante and sequencial > 1';
    }
}
