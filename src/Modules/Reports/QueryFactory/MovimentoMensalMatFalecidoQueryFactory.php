<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalMatFalecidoQueryFactory extends MovimentoMensalDetalheQueryFactory
{
    public function where()
    {
        return 'falecido and saiu_durante';
    }
}
