<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalMatTrocasQueryFactory extends MovimentoMensalDetalheQueryFactory
{
    public function where()
    {
        return 'matricula_ativa and enturmacao_inativa and saiu_durante and sequencial < maior_sequencial';
    }
}
