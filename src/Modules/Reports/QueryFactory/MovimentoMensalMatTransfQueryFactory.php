<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalMatTransfQueryFactory extends MovimentoMensalDetalheQueryFactory
{
    public function where()
    {
        return 'transferido and saiu_durante';
    }
}
