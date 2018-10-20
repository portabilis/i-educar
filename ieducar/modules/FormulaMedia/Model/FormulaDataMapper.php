<?php

require_once 'CoreExt/DataMapper.php';
require_once 'FormulaMedia/Model/Formula.php';

class FormulaMedia_Model_FormulaDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'FormulaMedia_Model_Formula';

    protected $_tableName = 'formula_media';

    protected $_tableSchema = 'modules';

    protected $_attributeMap = [
        'id' => 'id',
        'instituicao' => 'instituicao_id',
        'nome' => 'nome',
        'formulaMedia' => 'formula_media',
        'tipoFormula' => 'tipo_formula',
        'substituiMenorNotaRc' => 'substitui_menor_nota_rc'
    ];

    protected $_primaryKey = [
        'id' => 'id',
        'instituicao' => 'instituicao_id'
    ];
}
