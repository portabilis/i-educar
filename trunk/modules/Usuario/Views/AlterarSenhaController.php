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

require_once 'lib/Portabilis/Controller/Page/EditCoreController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';
require_once 'Usuario/Mailers/UsuarioMailer.php';

class AlterarSenhaController extends Portabilis_Controller_Page_EditCoreController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo   = 'Redefinir senha';
  protected $_processoAp = 0;

  protected $_formMap = array(
    'matricula' => array(
      'label'  => 'Matr&iacute;cula',
      'help'   => '',
    ),
    'nova_senha' => array(
      'label'  => 'Nova senha',
      'help'   => '',
    ),
    'confirmacao_senha' => array(
      'label'  => 'Confirma&ccedil;&atilde;o de senha',
      'help'   => '',
    ),
  );


  // Portabilis_Controller_Page_EditCoreController methods

  public function render()
  {
    $this->messenger()->append('Por motivos de seguran&ccedil;a, periodicamente sua senha deve ser atualizada, por favor, informe uma nova senha.', 'info');

    $this->loadCurrentUser();
    //$superUser = $GLOBALS['coreExt']['Config']->app->superuser == $this->getEntity()->matricula;

    $this->campoRotulo('matricula', $this->_getLabel('matricula'), $this->getEntity()->matricula);
    $this->campoSenha('password', $this->_getLabel('nova_senha'), @$_POST['password'], TRUE);
    $this->campoSenha('password_confirmation', $this->_getLabel('confirmacao_senha'), @$_POST['password_confirmation'], TRUE);

    $this->nome_url_sucesso  = 'Alterar';
    $this->nome_url_cancelar = 'Deixar para depois';
    $this->url_cancelar      = '/intranet/index.php';

    $this->mensagem = $this->messenger()->toHtml();
  }


  public function edit()
  {
    $this->loadCurrentUser();

    if (! $this->messenger()->hasMsgWithType('error') && $this->canUpdatePassword()) {
      $this->updatePassword();
      UsuarioMailer::UpdatedPassword($user  = $this->getEntity());
    }
  }


  function afterSave()
  {
    $this->redirectTo('/intranet/index.php');
  }


  // custom methods

  protected function loadCurrentUser() {
    $user = $this->getDataMapper()->findAllUsingPreparedQuery(array(),
                                                              array('ref_cod_pessoa_fj = $1'),
                                                              array(Portabilis_Utils_User::currentUserId()),
                                                              array(),
                                                              false);

    if(is_array($user) && count($user) > 0)
      $this->setEntity($user[0]);
    else
      $this->messenger()->append('N&atilde;o foi possivel recuperar o usuário atual do banco de dados.', 'error', false, 'error');
  }


  protected function canUpdatePassword() {
    $password             = $_POST['password'];
    $passwordConfirmation = $_POST['password_confirmation'];

    if (empty($password))
      $this->messenger()->append('Por favor informe uma senha.', 'error');
    elseif (strlen($password) < 8)
      $this->messenger()->append('Por favor informe uma senha segura, com pelo menos 8 caracteres.', 'error');
    elseif ($password != $passwordConfirmation)
      $this->messenger()->append('A senha e confirma&ccedil;&atilde;o de senha devem ser as mesmas.', 'error');
    elseif ($password == $user->matricula)
      $this->messenger()->append('Informe uma senha diferente da matricula.', 'error');

    return ! $this->messenger()->hasMsgWithType('error');
  }


  protected function updatePassword() {
    $user     = $this->getEntity();
    $password = $_POST['password'];

    try {
      $user->setOptions(array('senha' => md5($password)));
      $this->getDataMapper()->save($user);

      $this->messenger()->append('Senha alterada com sucesso.', 'success');
    }
    catch (Exception $e) {
      $this->messenger()->append('Erro ao atualizar de senha.', 'error');
      error_log("Exception ocorrida ao atualizar senha, matricula: {$user->matricula}, erro: " .  $e->getMessage());
    }
  }

}
?>
