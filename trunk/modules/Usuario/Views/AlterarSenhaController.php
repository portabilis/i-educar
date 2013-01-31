 <?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * @package   Usuario
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';
require_once 'Usuario/Mailers/UsuarioMailer.php';
require_once 'Usuario/Validators/UsuarioValidator.php';

class AlterarSenhaController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'Alterar senha';
  protected $_processoAp = 0;

  protected $backwardCompatibility = true;

  protected $_formMap    = array(
    'matricula'         => array('label' => 'Matr&iacute;cula',                   'help' => ''),
    'nova_senha'        => array('label' => 'Nova senha',                         'help' => ''),
    'confirmacao_senha' => array('label' => 'Confirma&ccedil;&atilde;o de senha', 'help' => ''),
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
    if (! isset($_POST['password']))
      $this->messenger()->append('Para sua seguran&ccedil;a mude sua senha periodicamente, por favor, informe uma nova senha.', 'info');

    $this->campoRotulo('matricula', $this->_getLabel('matricula'), $this->getEntity()->matricula);
    $this->campoSenha('password', $this->_getLabel('nova_senha'), @$_POST['password'], TRUE);
    $this->campoSenha('password_confirmation', $this->_getLabel('confirmacao_senha'), @$_POST['password_confirmation'], TRUE);

    $this->nome_url_sucesso  = 'Alterar';
    $this->nome_url_cancelar = 'Deixar para depois';

    if ($GLOBALS['coreExt']['Config']->app->user_accounts->force_password_update != true)
      $this->url_cancelar      = '/intranet/index.php';
  }


  protected function canSave()
  {
    return UsuarioValidator::validatePassword($this->messenger(),
                                  $this->getEntity()->senha,
                                  $_POST['password'],
                                  $_POST['password_confirmation'],
                                  md5($_POST['password']),
                                  $this->getEntity()->matricula);
  }

  protected function save()
  {
    $this->getEntity()->setOptions(array('senha' => md5($_POST['password']), 'data_troca_senha' => 'now()'));
    $this->getDataMapper()->save($this->getEntity());

    $linkToReset = $_SERVER['HTTP_HOST'] . $this->getRequest()->getBaseurl() . '/' . 'Usuario/AlterarSenha';
    UsuarioMailer::updatedPassword($user = $this->getEntity(), $linkToReset);
  }
}
?>
