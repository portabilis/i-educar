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
 * @author      Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     RegraAvaliacao
 * @subpackage  Modules
 *
 * @since       ?
 *
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';

/**
 * RegraAvaliacao_Model_RegraRecuperacao class.
 *
 * @author      Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     RegraAvaliacao
 * @subpackage  Modules
 *
 * @since       ?
 *
 * @version     @@package_version@@
 */
class RegraAvaliacao_Model_RegraRecuperacao extends CoreExt_Entity
{
    protected $_data = [
    'regraAvaliacao' => null,
    'descricao'            => null,
    'etapasRecuperadas'          => null,
    'substituiMenorNota'          => null,
    'media'          => null,
    'notaMaxima'          => null
  ];

    protected $_dataTypes = [
    'substituiMenorNota' => 'boolean',
    'media' => 'numeric',
    'nota_maxima' => 'numeric'
  ];

    protected $_references = [
    'regraAvaliacao' => [
      'value' => null,
      'class' => 'RegraAvaliacao_Model_RegraDataMapper',
      'file'  => 'RegraAvaliacao/Model/RegraDataMapper.php'
    ]
  ];

    /**
     * @see CoreExt_Entity#getDataMapper()
     */
    public function getDataMapper()
    {
        if (is_null($this->_dataMapper)) {
            require_once 'RegraAvaliacao/Model/RegraRecuperacaoDataMapper.php';
            $this->setDataMapper(new RegraAvaliacao_Model_RegraRecuperacaoDataMapper());
        }

        return parent::getDataMapper();
    }

    public function getEtapas()
    {
        return explode(';', $this->get('etapasRecuperadas'));
    }

    public function getLastEtapa()
    {
        return max($this->getEtapas());
    }

    /**
     * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
     *
     * @todo Implementar validador que retorne um String ou Numeric, dependendo
     *   do valor do atributo (assim como validateIfEquals().
     * @todo Implementar validador que aceite um valor de comparação como
     *   alternativa a uma chave de atributo. (COMENTADO ABAIXO)
     */
    public function getDefaultValidatorCollection()
    {
        return [
      'descricao' => new CoreExt_Validate_String(['min' => 1, 'max' => 25]),
      'etapasRecuperadas' => new CoreExt_Validate_String(['min' => 1, 'max' => 25]),
      'media' => new CoreExt_Validate_Numeric(['min' => 0.001, 'max' => 9999.0]),
      'notaMaxima' => new CoreExt_Validate_Numeric(['min' => 0.001, 'max' => 9999.0])
    ];
    }

    public function __toString()
    {
        return $this->descricao;
    }
}
