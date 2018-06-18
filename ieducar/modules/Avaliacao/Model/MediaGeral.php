<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Gabriel Matos de Souza <gabriel@portabilis.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     Avaliacao
 * @subpackage  Modules
 *
 * @since       Arquivo disponível desde a versão 1.1.0
 *
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';

/**
 * Avaliacao_Model_MediaGeral class.
 *
 * @author      Gabriel Matos de Souza <gabriel@portabilis.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     Avaliacao
 * @subpackage  Modules
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class Avaliacao_Model_MediaGeral extends CoreExt_Entity
{
    protected $_data = [
    'notaAluno'            => null,
    'media'                => null,
    'mediaArredondada'     => null,
    'etapa'                => null
  ];

    protected $_dataTypes = [
    'media' => 'numeric'
  ];

    protected $_references = [
    'notaAluno' => [
      'value' => null,
      'class' => 'Avaliacao_Model_NotaAlunoDataMapper',
      'file'  => 'Avaliacao/Model/NotaAlunoDataMapper.php'
    ]
  ];

    public function __construct($options = [])
    {
        parent::__construct($options);
        unset($this->_data['id']);
    }

    /**
     * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        return [
      'media' => new CoreExt_Validate_Numeric(['min' => 0, 'max' => 10]),
      'mediaArredondada' => new CoreExt_Validate_String(['max' => 5]),
      'etapa' => new CoreExt_Validate_String(['max' => 2])
    ];
    }
}
