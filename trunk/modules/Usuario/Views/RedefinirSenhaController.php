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
require_once 'Portabilis/View/Helper/Application.php';
require_once 'lib/Portabilis/Utils/ReCaptcha.php';
require_once 'Usuario/Validators/UsuarioValidator.php';

require_once 'include/clsControlador.inc.php';

class RedefinirSenhaController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'Redefinir senha';
  protected $_processoAp = 0;

  protected $backwardCompatibility = true;

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


  public function _preConstruct() {
    $this->_options = $this->mergeOptions(array('edit_success' => 'intranet/index.php'), $this->_options);
  }


  /* overwrite Core/Controller/Page/Abstract.php para renderizar html
     sem necessidade de usuário estar logado */
  public function generate(CoreExt_Controller_Page_Interface $instance)
  {
    require_once 'Core/View.php';
    $viewBase = new Core_View($instance);
    $viewBase->titulo = 'i-Educar - Redefini&ccedil;&atilde;o senha';
    $instance->titulo = 'Redefini&ccedil;&atilde;o senha';
    $viewBase->addForm($instance);
    //$viewBase->Formular();

    $html = $viewBase->MakeHeadHtml();
    foreach ($viewBase->clsForm as $form) {
      $html .= $form->RenderHTML();
    }

    $html .= $form->getAppendedOutput();
    $html .= $viewBase->MakeFootHtml();

    echo $html;
  }


  // inicia um novo processo de redefinição de senha, quando nao recebe um token
  protected function _initNovo()
  {
    return ! isset($_GET['token']);
  }


  // continua o processo de redefinição de senha, quando recebe o token
  protected function _initEditar()
  {
    return isset($_GET['token']);
  }


  public function Gerar()
  {
    if (! isset($_GET['token']))
      $this->GerarNovo();
    else
      $this->GerarEditar();

    $this->url_cancelar     = '/intranet/index.php';
    $this->nome_url_sucesso = 'Redefinir';
  }


  protected function GerarNovo() {
    $this->nome_url_cancelar = 'Entrar';
    $matricula               = $_POST['matricula'];

    if (empty($matricula) && is_numeric($this->getOption('id_usuario'))) {
      $user      = Portabilis_Utils_User::load($id = $this->getOption('id_usuario'));
      $matricula = $user['matricula'];
    }

    $this->campoTexto('matricula', $this->_getLabel('matricula'), $matricula,
      50, 50, TRUE, FALSE, FALSE, $this->_getHelp('matricula'));

    echo Portabilis_Utils_ReCaptcha::getWidget();
    $this->reCaptchaFixup();
  }


  protected function GerarEditar()
  {
    $this->loadUserByStatusToken('redefinir_senha-' . $_GET['token']);

    $this->campoRotulo('matricula', $this->_getLabel('matricula'), $this->getEntity()->matricula);
    $this->campoSenha('password', $this->_getLabel('nova_senha'), @$_POST['password'], TRUE);
    $this->campoSenha('password_confirmation', $this->_getLabel('confirmacao_senha'), @$_POST['password_confirmation'], TRUE);
  }


  public function Novo()
  {
    if (! $this->messenger()->hasMsgWithType('error')) {
      if (! Portabilis_Utils_ReCaptcha::getWidget()->validate()) {
        $this->messenger()->append('Por favor, informe a confirma&ccedil;&atilde;o visual no respectivo campo.', 'error');
      }
      elseif ($this->loadUserByMatricula($_POST['matricula']))
        $this->sendResetPasswordMail();
    }

    return ! $this->messenger()->hasMsgWithType('error');
  }


  public function Editar()
  {
    if (! $this->messenger()->hasMsgWithType('error') && $this->loadUserByStatusToken('redefinir_senha-' . $_GET['token']))
      $this->updatePassword();

    return ! $this->messenger()->hasMsgWithType('error');
  }


  protected function updatePassword() {
    $user = $this->getEntity();

    try {
      if ($this->canUpdate($user)) {
        $user->setOptions(array('senha' => md5($_POST['password']), 'status_token' => '', 'data_troca_senha' => 'now()'));
        $this->getDataMapper()->save($user);

        $linkToReset = $_SERVER['HTTP_HOST'] . $this->getRequest()->getBaseurl() . '/' . 'Usuario/RedefinirSenha';
        UsuarioMailer::updatedPassword($user = $this->getEntity(), $linkToReset);

        // #FIXME adicionar flash ao session, para persistr ao redirecionar ?
        $this->messenger()->append('Senha alterada com sucesso.', 'success');

        $this->logInUser();
      }
    }
    catch (Exception $e) {
      $this->messenger()->append('Erro ao atualizar de senha.', 'error');
      error_log("Exception ocorrida ao atualizar senha, matricula: {$user->matricula}, erro: " .  $e->getMessage());
    }
  }


  protected function canUpdate($user)
  {
    return UsuarioValidator::validatePassword($this->messenger(),
                                  $user->senha,
                                  $_POST['password'],
                                  $_POST['password_confirmation'],
                                  md5($_POST['password']),
                                  $user->matricula);
  }


  protected function setTokenRedefinicaoSenha() {
    $user = $this->getEntity();
    try {
      $token = md5(uniqid($user->email));
      $statusToken = 'redefinir_senha-' . $token;

      $user->setOptions(array('status_token' => $statusToken));
      $this->getDataMapper()->save($user);
      return $token;
    }
    catch (Exception $e) {
      $this->messenger()->append('Erro ao setar token redefini&ccedil;&atilde;o de senha.', 'error');
      error_log("Exception ocorrida ao setar token reset senha, matricula: {$user->matricula}, erro: " .  $e->getMessage());
      return false;
    }
  }


  protected function loadUserByStatusToken($statusToken) {
    $result = false;
    try {
      if(empty($statusToken) && ! is_numeric($statusToken))
        $this->messenger()->append('Deve ser recebido um token.', 'error');
      else {
        $user = $this->getDataMapper()->findAllUsingPreparedQuery(array(),
                                                               array('status_token' => '$1'),
                                                               array($statusToken),
                                                               array(),
                                                               false);

        if(! empty($user) && ! empty($user[0]->ref_cod_pessoa_fj)) {
          $this->setEntity($user[0]);
          $result = true;
        }
        else
          $this->messenger()->append('Link inv&aacutelido ou j&aacute utilizado, por favor, <a class="light decorated" href="RedefinirSenha">solicite a redefini&ccedil;&atilde;o de senha novamente</a>.', 'error', false, 'error');
      }
    }
    catch (Exception $e) {
      $this->messenger()->append('Ocorreu um erro inesperado ao recuperar o usu&aacute;rio, por favor, ' .
                       'tente novamente.', 'error');

      error_log("Exception ocorrida ao redefinir senha (loadUserByStatusToken), " .
                "matricula: $matricula, erro: " .  $e->getMessage());
    }

    return $result;
  }


  protected function loadUserByMatricula($matricula) {
    $result = false;
    $user = null;

    try {
      if(empty($matricula) && ! is_numeric($matricula))
        $this->messenger()->append('Informe uma matr&iacute;cula.', 'error');
      else {
        $user = $this->getDataMapper()->findAllUsingPreparedQuery(array(),
                                                               array('matricula' => '$1'),
                                                               array($matricula),
                                                               array(),
                                                               false);

        if(! empty($user) && ! empty($user[0]->ref_cod_pessoa_fj)) {
          $this->setEntity($user[0]);
          $result = true;
        }
        else
          $this->messenger()->append('Nenhum usu&aacute;rio encontrado com a matr&iacute;cula informada.',
                           'error', false, 'error');
      }
    }
    catch (Exception $e) {
      $this->messenger()->append('Ocorreu um erro inesperado ao recuperar o usu&aacute;rio, por favor, ' .
                       'verifique o valor informado e tente novamente.', 'error');

      error_log("Exception ocorrida ao redefinir senha (loadUserByMatricula), " .
                "matricula: $matricula, erro: " .  $e->getMessage());
    }

    return $result;
  }


  protected function sendResetPasswordMail() {
    $user = $this->getEntity();

    if(empty($user->email)) {
      $this->messenger()->append('Parece que seu usu&aacute;rio n&atilde;o possui um e-mail definido, por favor, '.
                       'solicite ao administrador do sistema para definir seu e-mail (em DRH > Cadastro '.
                       'de funcion&aacute;rios) e tente novamente.', 'error');
    }
    else {
      $token = $this->setTokenRedefinicaoSenha();

      if ($token != false) {
        $link = $_SERVER['HTTP_REFERER'] . "?token=$token";

        if(UsuarioMailer::passwordReset($user, $link)) {
          $successMsg = 'Enviamos um e-mail para voc&ecirc;, por favor, clique no link recebido para redefinir sua senha.';
          $this->messenger()->append($successMsg, 'success');
        }
        else {
          $errorMsg = 'N&atilde;o conseguimos enviar um e-mail para voc&ecirc;, por favor, tente novamente mais tarde.';
          $this->messenger()->append($errorMsg, 'error');
        }

      }
    }
  }


  public function logInUser()
  {
    $controlador = new clsControlador();

    // #TODO migrar para Portabilis_Utils_User carregar usuário usando funcionario data mapper
    //       e então nesta classe usar $this->getEntity
    $user = Portabilis_Utils_User::load($id = $this->getEntity()->ref_cod_pessoa_fj);

    if ($controlador->canStartLoginSession($user))
      $controlador->startLoginSession($user, '/intranet/index.php');

    $this->messenger()->merge($controlador->messages);
  }


  // fixup para mover o widget para o local correto, necessário pois chrome não executa
  // o script caso seja usado $this->campoRotulo('...', '...', '<script...>')
  protected function reCaptchaFixup() {
    $this->campoRotulo('replace_by_recaptcha_widget_wrapper',
                       'Confirma&ccedil;&atilde;o visual',
                       '<div id="replace_by_recaptcha_widget"></div>');

    $js = "function replaceRecaptchaWidget() {
              var emptyElement = document.getElementById('replace_by_recaptcha_widget');
              var originElement = document.getElementById('recaptcha_widget_div');
              emptyElement.parentNode.replaceChild(originElement, emptyElement);
            }

            window.onload = replaceRecaptchaWidget;";

    Portabilis_View_Helper_Application::embedJavascript($this, $js);
  }

}
?>
