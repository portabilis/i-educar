<?php

use iEducar\Modules\Reports\QueryFactory\MovimentoGeralAlunosAbandonosQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoGeralAlunosAdmitidosQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoGeralAlunosAnosQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoGeralAlunosEdInfIntQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoGeralAlunosEdInfParcQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoGeralAlunosObitoQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoGeralAlunosReclaQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoGeralAlunosRemQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoGeralAlunosTransferidosQueryFactory;

class ConsultaMovimentoGeralController extends ConsultaBaseController
{
    protected function canGetAlunos()
    {
        return (
            $this->validatesPresenceOf('escola') &&
            $this->validatesPresenceOf('tipo') &&
            $this->validatesPresenceOf('data_inicial') &&
            $this->validatesPresenceOf('data_final') &&
            $this->validatesPresenceOf('ano')
        );
    }

    protected function getData()
    {
        $type = $this->getRequest()->tipo;
        $params = [];

        $params['escola'] = $this->getRequest()->escola;
        $params['data_inicial'] = $this->getRequest()->data_inicial;
        $params['data_final'] = $this->getRequest()->data_final;
        $params['ano'] = $this->getRequest()->ano;
        $params['curso'] = !empty($this->getRequest()->curso) ? $this->getRequest()->curso : 0;

        if ($params['curso'] !== 0) {
            $params['curso'] = explode(',', $params['curso']);
            $params['seleciona_curso'] = 1;
        } else {
            unset($params['curso']);
        }

        $method = $this->getMethodName($type);

        return $this->{$method}($params);
    }

    protected function getDataObito($params)
    {
        $queryFactory = new MovimentoGeralAlunosObitoQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataRecla($params)
    {
        $queryFactory = new MovimentoGeralAlunosReclaQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataRem($params)
    {
        $queryFactory = new MovimentoGeralAlunosRemQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataTransf($params)
    {
        $queryFactory = new MovimentoGeralAlunosTransferidosQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataAband($params)
    {
        $queryFactory = new MovimentoGeralAlunosAbandonosQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataAdmitidos($params)
    {
        $queryFactory = new MovimentoGeralAlunosAdmitidosQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataEdInfInt($params)
    {
        $queryFactory = new MovimentoGeralAlunosEdInfIntQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataEdInfParc($params)
    {
        $queryFactory = new MovimentoGeralAlunosEdInfParcQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataAnos($params)
    {
        $queryFactory = new MovimentoGeralAlunosAnosQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataAno1($params)
    {
        $params['ano_coluna'] = 1;

        return $this->getDataAnos($params);
    }

    protected function getDataAno2($params)
    {
        $params['ano_coluna'] = 2;

        return $this->getDataAnos($params);
    }

    protected function getDataAno3($params)
    {
        $params['ano_coluna'] = 3;

        return $this->getDataAnos($params);
    }

    protected function getDataAno4($params)
    {
        $params['ano_coluna'] = 4;

        return $this->getDataAnos($params);
    }

    protected function getDataAno5($params)
    {
        $params['ano_coluna'] = 5;

        return $this->getDataAnos($params);
    }

    protected function getDataAno6($params)
    {
        $params['ano_coluna'] = 6;

        return $this->getDataAnos($params);
    }

    protected function getDataAno7($params)
    {
        $params['ano_coluna'] = 7;

        return $this->getDataAnos($params);
    }

    protected function getDataAno8($params)
    {
        $params['ano_coluna'] = 8;

        return $this->getDataAnos($params);
    }

    protected function getDataAno9($params)
    {
        $params['ano_coluna'] = 9;

        return $this->getDataAnos($params);
    }
}
