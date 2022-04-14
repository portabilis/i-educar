<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalMatIniQueryFactory extends MovimentoMensalDetalheQueryFactory
{
    public function where()
    {
        return 'matricula_ativa and sem_dependencia and entrou_antes_inicio and saiu_depois_inicio';
    }
}
