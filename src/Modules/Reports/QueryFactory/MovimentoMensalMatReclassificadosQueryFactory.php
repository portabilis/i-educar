<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalMatReclassificadosQueryFactory extends MovimentoMensalDetalheQueryFactory
{
    public function where()
    {
        return 'reclassificado and saiu_durante';
    }
}
