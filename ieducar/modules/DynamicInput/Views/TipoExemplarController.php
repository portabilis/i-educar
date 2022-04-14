<?php

class TipoExemplarController extends ApiCoreController
{
    protected $_dataMapper  = 'Biblioteca_Model_TipoExemplarDataMapper';

    protected function canGetTiposExemplar()
    {
        return $this->validatesId('biblioteca');
    }

    protected function getTiposExemplar()
    {
        if ($this->canGetTiposExemplar()) {
            $columns = ['cod_exemplar_tipo', 'nm_tipo'];

            $where   = ['ref_cod_biblioteca' => $this->getRequest()->biblioteca_id,
                       'ativo'              => '1'];

            $records = $this->getDataMapper()->findAll(
                $columns,
                $where,
                $orderBy = ['nm_tipo' => 'ASC'],
                $addColumnIdIfNotSet = false
            );

            $options = [];

            foreach ($records as $record) {
                $options[$record->cod_exemplar_tipo] = Portabilis_String_Utils::toUtf8($record->nm_tipo);
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'tipos_exemplar')) {
            $this->appendResponse($this->getTiposExemplar());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
