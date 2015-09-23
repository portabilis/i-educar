<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @package   Avaliacao
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';
require_once 'Usuario/Mailers/UsuarioMailer.php';

class AlterarEmailController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'Alterar e-mail';
  protected $_processoAp = 0;

  protected $backwardCompatibility = true;

  protected $_formMap    = array(
    'matricula' => array(
      'label'  => 'Matr&iacute;cula',
      'help'   => '',
    ),
    'email' => array(
      'label'  => 'E-mail',
      'help'   => 'E-mail utilizado para recuperar sua senha.',
    ),
  );


  protected function _preConstruct()
  {
    $this->_options = $this->mergeOptions(array('edit_success' => 'intranet/index.php'), $this->_options);
  }


  // this controller always edit an existing resource
  protected function _initNovo() {
    return false;
  }


  protected function _initEditar() {
    $this->setEntity($this->getDataMapper()->find($this->getOption('id_usuario')));
    return true;
  }


  public function Gerar()
  {
    $validEmail = filter_var($this->getEntity()->email, FILTER_VALIDATE_EMAIL) == true;

    if (empty($this->getRequest()->email) &&  ! $validEmail)
      $this->messenger()->append("Por favor informe um e-mail v&aacute;lido, para ser usado caso voc&ecirc; esque&ccedil;a sua senha.");

    $this->campoRotulo('matricula', $this->_getLabel('matricula'), $this->getEntity()->matricula);
    $this->campoTexto('email', $this->_getLabel('email'), $this->getEntity()->email,
      50, 50, TRUE, FALSE, FALSE, $this->_getHelp('email'));

    $this->url_cancelar = '/intranet/index.php';

    if (! $validEmail)
      $this->nome_url_cancelar = 'Deixar para depois';
  }


  public function save()
  {
    $this->getEntity()->setOptions(array('email' => $_POST['email']));
    $this->getDataMapper()->save($this->getEntity());
  }
}
?>
