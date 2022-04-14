<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalMatAbandQueryFactory extends MovimentoMensalDetalheQueryFactory
{
    public function where()
    {
        return 'abandono and enturmacao_abandono and saiu_durante';
    }
}
