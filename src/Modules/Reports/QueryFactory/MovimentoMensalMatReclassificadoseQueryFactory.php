<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalMatReclassificadoseQueryFactory extends MovimentoMensalDetalheQueryFactory
{
    public function where()
    {
        return 'matricula_ativa and entrada_reclassificado and entrou_durante';
    }
}
