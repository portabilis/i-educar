<?php

require_once 'Avaliacao/Model/ParecerDescritivoAbstractDataMapper.php';
require_once 'Avaliacao/Model/ParecerDescritivoComponente.php';

/**
 * Avaliacao_Model_ParecerDescritivoComponenteDataMapper class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Model_ParecerDescritivoComponenteDataMapper extends Avaliacao_Model_ParecerDescritivoAbstractDataMapper
{
    protected $_entityClass = 'Avaliacao_Model_ParecerDescritivoComponente';
    protected $_tableName   = 'parecer_componente_curricular';

    protected $_attributeMap = array(
        'id'                    => 'id',
        'componenteCurricular'  => 'componente_curricular_id',
        'parecer'               => 'parecer',
        'etapa'                 => 'etapa'
    );

    protected $_primaryKey = array(
        'parecerDescritivoAluno' => 'parecer_aluno_id',
        'componenteCurricular'  => 'componente_curricular_id',
        'etapa' => 'etapa'
    );
}
