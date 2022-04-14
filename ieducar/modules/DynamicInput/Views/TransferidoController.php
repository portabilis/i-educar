<?php

class TransferidoController extends ApiCoreController
{
    protected function canGetTransferido()
    {
        return $this->validatesId('turma') &&
           $this->validatesPresenceOf('ano');
    }

    protected function getTransferido()
    {
        if ($this->canGetTransferido()) {
            $matriculas = new clsPmieducarMatricula();
            $matriculas->setOrderby('sequencial_fechamento , translate(nome,\''.'åáàãâäéèêëíìîïóòõôöúùüûçÿýñÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ'.'\', \''. 'aaaaaaeeeeiiiiooooouuuucyynAAAAAAEEEEIIIIOOOOOUUUUCYN'.'\') ');
            $matriculas = $matriculas->lista_transferidos(
                null,
                null,
                $this->getRequest()->escola_id,
                $this->getRequest()->serie_id,
                null,
                null,
                $this->getRequest()->aluno_id,
                '4',
                null,
                null,
                null,
                null,
                $ativo = 1,
                $this->getRequest()->ano,
                null,
                $this->getRequest()->instituicao_id,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->getRequest()->curso_id,
                null,
                $this->getRequest()->matricula_id,
                null,
                null,
                null,
                null,
                $this->getRequest()->turma_id,
                null,
                false
            ); // Mostra alunos em abandono/transferidos se não existir nenhuma matricula_turma ativa pra outra turma

            $options = [];

            foreach ($matriculas as $matricula) {
                $options['__' . $matricula['cod_matricula']] = $this->toUtf8($matricula['nome']);
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'transferidos')) {
            $this->appendResponse($this->getTransferido());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
