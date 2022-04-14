<?php

class RotasController extends ApiCoreController
{
    protected function canGetRotas()
    {
        return $this->validatesPresenceOf('ano_rota');
    }

    protected function getRotas()
    {
        if ($this->canGetRotas()) {
            $anoRota = $this->getRequest()->ano_rota;

            $sql = 'SELECT descricao,
                     cod_rota_transporte_escolar
              FROM modules.rota_transporte_escolar
              WHERE ano = $1';

            $rotas = $this->fetchPreparedQuery($sql, [$anoRota]);
            $options = [];

            foreach ($rotas as $rota) {
                $options['__' . $rota['cod_rota_transporte_escolar']] = $this->toUtf8($rota['descricao']);
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'rotas')) {
            $this->appendResponse($this->getRotas());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
