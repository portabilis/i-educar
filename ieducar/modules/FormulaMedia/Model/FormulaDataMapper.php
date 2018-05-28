<?php


require_once 'CoreExt/DataMapper.php';
require_once 'FormulaMedia/Model/Formula.php';

/**
 * FormulaMedia_Model_FormulaDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class FormulaMedia_Model_FormulaDataMapper extends CoreExt_DataMapper
{
    protected $_entityClass = 'FormulaMedia_Model_Formula';
    protected $_tableName   = 'formula_media';
    protected $_tableSchema = 'modules';

    protected $_attributeMap = array(
        'id'                    => 'id',
        'instituicao'           => 'instituicao_id',
        'nome'                  => 'nome',
        'formulaMedia'          => 'formula_media',
        'tipoFormula'           => 'tipo_formula',
        'substituiMenorNotaRc'  => 'substitui_menor_nota_rc'
    );

    protected $_primaryKey = array(
        'id'            => 'id',
        'instituicao'   => 'instituicao_id'
    );
}
