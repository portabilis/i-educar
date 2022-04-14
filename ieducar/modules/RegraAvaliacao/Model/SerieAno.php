<?php

class RegraAvaliacao_Model_SerieAno extends CoreExt_Entity
{
    protected $_data = [
        'regraAvaliacao' => null,
        'regraAvaliacaoDiferenciada' => null,
        'anoLetivo' => null,
        'serie' => null,
    ];
    public function getDefaultValidatorCollection()
    {
        return [];
    }

    // Override para Entity não forçar a coluna id no attributo $_data
    protected function _createIdentityField()
    {
        return $this;
    }
}
