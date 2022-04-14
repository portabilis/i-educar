<?php

use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatAbandQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatAdmitQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatFalecidoQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatIniQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatReclassificadoseQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatReclassificadosQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatTransfQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatTrocaeQueryFactory;
use iEducar\Modules\Reports\QueryFactory\MovimentoMensalMatTrocasQueryFactory;

class ConsultaMovimentoMensalController extends ConsultaBaseController
{
    protected function canGetAlunos()
    {
        return (
            $this->validatesPresenceOf('ano') &&
            $this->validatesPresenceOf('instituicao') &&
            $this->validatesPresenceOf('escola') &&
            $this->validatesPresenceOf('data_inicial') &&
            $this->validatesPresenceOf('data_final')
        );
    }

    protected function getData()
    {
        $type = $this->getRequest()->tipo;
        $params = [];

        $params['ano'] = $this->getRequest()->ano;
        $params['instituicao'] = $this->getRequest()->instituicao;
        $params['escola'] = $this->getRequest()->escola;
        $params['data_inicial'] = $this->getRequest()->data_inicial;
        $params['data_final'] = $this->getRequest()->data_final;
        $params['curso'] = !empty($this->getRequest()->curso) ? $this->getRequest()->curso : 0;
        $params['serie'] = !empty($this->getRequest()->serie) ? $this->getRequest()->serie : 0;
        $params['turma'] = !empty($this->getRequest()->turma) ? $this->getRequest()->turma : 0;

        $method = $this->getMethodName($type);

        return $this->{$method}($params);
    }

    protected function getDataMatIniM($params)
    {
        $params['sexo'] = 'M';
        $queryFactory = new MovimentoMensalMatIniQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatIniF($params)
    {
        $params['sexo'] = 'F';
        $queryFactory = new MovimentoMensalMatIniQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatTransfM($params)
    {
        $params['sexo'] = 'M';
        $queryFactory = new MovimentoMensalMatTransfQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatTransfF($params)
    {
        $params['sexo'] = 'F';
        $queryFactory = new MovimentoMensalMatTransfQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatAbandM($params)
    {
        $params['sexo'] = 'M';
        $queryFactory = new MovimentoMensalMatAbandQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatAbandF($params)
    {
        $params['sexo'] = 'F';
        $queryFactory = new MovimentoMensalMatAbandQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatAdmitM($params)
    {
        $params['sexo'] = 'M';
        $queryFactory = new MovimentoMensalMatAdmitQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatAdmitF($params)
    {
        $params['sexo'] = 'F';
        $queryFactory = new MovimentoMensalMatAdmitQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatFalecidoM($params)
    {
        $params['sexo'] = 'M';
        $queryFactory = new MovimentoMensalMatFalecidoQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatFalecidoF($params)
    {
        $params['sexo'] = 'F';
        $queryFactory = new MovimentoMensalMatFalecidoQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatReclassificadosM($params)
    {
        $params['sexo'] = 'M';
        $queryFactory = new MovimentoMensalMatReclassificadosQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatReclassificadosF($params)
    {
        $params['sexo'] = 'F';
        $queryFactory = new MovimentoMensalMatReclassificadosQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatReclassificadoseM($params)
    {
        $params['sexo'] = 'M';
        $queryFactory = new MovimentoMensalMatReclassificadoseQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatReclassificadoseF($params)
    {
        $params['sexo'] = 'F';
        $queryFactory = new MovimentoMensalMatReclassificadoseQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatTrocaeM($params)
    {
        $params['sexo'] = 'M';
        $queryFactory = new MovimentoMensalMatTrocaeQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatTrocaeF($params)
    {
        $params['sexo'] = 'F';
        $queryFactory = new MovimentoMensalMatTrocaeQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatTrocasM($params)
    {
        $params['sexo'] = 'M';
        $queryFactory = new MovimentoMensalMatTrocasQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }

    protected function getDataMatTrocasF($params)
    {
        $params['sexo'] = 'F';
        $queryFactory = new MovimentoMensalMatTrocasQueryFactory($this->getPDO(), $params);

        return ['alunos' => $queryFactory->getData()];
    }
}
