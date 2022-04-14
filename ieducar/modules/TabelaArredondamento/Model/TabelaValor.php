<?php

class TabelaArredondamento_Model_TabelaValor extends CoreExt_Entity
{
    protected $_data = [
        'tabelaArredondamento' => null,
        'nome' => null,
        'descricao' => null,
        'observacao' => null,
        'valorMinimo' => null,
        'valorMaximo' => null,
        'acao' => null,
        'casaDecimalExata' => null
    ];

    protected $_dataTypes = [
        'valorMinimo' => 'numeric',
        'valorMaximo' => 'numeric'
    ];

    protected $_references = [
        'tabelaArredondamento' => [
            'value' => null,
            'class' => 'TabelaArredondamento_Model_TabelaDataMapper',
            'file' => 'TabelaArredondamento/Model/TabelaDataMapper.php'
        ],
        'acao' => [
            'value' => 0,
            'class' => 'TabelaArredondamento_Model_TipoArredondamentoMedia',
            'file' => 'TabelaArredondamento/Model/TipoArredondamentoMedia.php',
            'null' => true
        ]
    ];

    /**
     * @see CoreExt_Entity::getDataMapper()
     */
    public function getDataMapper()
    {
        if (is_null($this->_dataMapper)) {
            $this->setDataMapper(
                new TabelaArredondamento_Model_TabelaValorDataMapper()
            );
        }

        return parent::getDataMapper();
    }

    /**
     * @see CoreExt_Entity_Validatable::getDefaultValidatorCollection()
     *
     * @todo Implementar validador que retorne um String ou Numeric, dependendo
     *   do valor do atributo (assim como validateIfEquals().
     * @todo Implementar validador que aceite um valor de comparação como
     *   alternativa a uma chave de atributo. (COMENTADO ABAIXO)
     */
    public function getDefaultValidatorCollection()
    {
        $validators = [];

        return $validators;
    }

    public function __toString()
    {
        return $this->nome;
    }
}
