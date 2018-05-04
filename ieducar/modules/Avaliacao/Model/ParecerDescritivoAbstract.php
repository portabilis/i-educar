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

require_once 'Avaliacao/Model/Etapa.php';

/**
 * Avaliacao_Model_ParecerDescritivoAbstract abstract class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
abstract class Avaliacao_Model_ParecerDescritivoAbstract extends Avaliacao_Model_Etapa
{
  protected $_data = array(
    'parecerDescritivoAluno' => NULL,
    'parecer'                => NULL
  );

  protected $_references = array(
    'parecerDescritivoAluno' => array(
      'value' => NULL,
      'class' => 'Avaliacao_Model_ParecerDescritivoAluno',
      'file'  => 'Avaliacao/Model/ParecerDescritivoAluno.php'
    )
  );

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   */
  public function getDefaultValidatorCollection()
  {
    $etapa  = $this->getValidator('etapa');
    $etapas = $etapa->getOption('choices');
    $etapas[] = 'An';

    $etapa->setOptions(array('choices' => $etapas));

    return array(
      'etapa'   => $etapa,
      'parecer' => new CoreExt_Validate_String()
    );
  }

  /**
   * Implementa método mágico __toString().
   * @link http://br.php.net/__toString
   * @return string
   */
  public function __toString()
  {
    return (string)$this->parecer;
  }
}
