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
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';
require_once 'Avaliacao/Model/Etapa.php';

/**
 * Avaliacao_Model_NotaComponente class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Model_NotaComponente extends Avaliacao_Model_Etapa
{
  protected $_data = array(
    'notaAluno'               => NULL,
    'componenteCurricular'    => NULL,
    'nota'                    => NULL,
    'notaArredondada'         => NULL,
    'notaRecuperacaoParalela' => NULL,
    'notaRecuperacaoEspecifica' => NULL,
    'notaOriginal'            => NULL
  );

  protected $_dataTypes = array(
    'nota' => 'numeric'
  );

  protected $_references = array(
    'notaAluno' => array(
      'value' => NULL,
      'class' => 'Avaliacao_Model_NotaAluno',
      'file'  => 'Avaliacao/Model/NotaAluno.php'
    ),
    'componenteCurricular' => array(
      'value' => NULL,
      'class' => 'ComponenteCurricular_Model_Componente',
      'file'  => 'ComponenteCurricular/Model/Componente.php'
    )
  );

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   */
  public function getDefaultValidatorCollection()
  {
    // Aceita etapas de 0 a 10 + a string Rc
    $etapas = range(0, 10, 1) + array('Rc');

    return array(
      'nota' => new CoreExt_Validate_Numeric(array('min' => 0, 'max' => 10)),
      'notaArredondada'  => new CoreExt_Validate_String(array('max' => 5))
    );
  }
}