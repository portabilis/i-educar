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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/View/Helper/Abstract.php';

/**
 * MenusSelecaoHelper class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class MenusSelecaoHelper extends CoreExt_View_Helper_Abstract {

  /**
   * Construtor singleton.
   */
  protected function __construct()
  {
  }


  /**
   * Retorna uma instância singleton.
   * @return CoreExt_View_Helper_Abstract
   */
  public static function getInstance()
  {
    return self::_getInstance(__CLASS__);
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public static function instituicao($viewInstance, $options = array()) {
    $defaultOptions = array('id'       => 'ref_cod_instituicao',
                            'label'    => 'Institui&ccedil;&atilde;o',
                            'required' => true,
                            'enabled'  => true,
                            'value'    => null,
                            'callback' => '',
                            'hint'     => '');

    #TODO pegar os args recebidos e >> $options ?
    var_dump(func_get_args());

    $options = array_merge($defaultOptions, $options);

    # TODO obter instituicoes conforme permissoes / tipo usuário
    $instituicoes = array();

		$viewInstance->campoLista($options['id'],
                              $options['label'],
                              $instituicoes,
                              $options['value'],
                              $options['callback'],
                              null,
                              $options['hint'],
                              null,
                              ! $options['enabled'],
                              $options['required']);
  }
}
?>
