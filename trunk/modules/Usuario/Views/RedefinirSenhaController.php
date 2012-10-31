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
 * @package   Usuario
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';
require_once 'include/clsControlador.inc.php';
require_once 'lib/Portabilis/Mailer.php';

class RedefinirSenhaController extends Core_Controller_Page_EditController
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


  public function _preConstruct() {
    $this->_msgs  = array();
    $this->mailer = new Mailer();
  }


  public function Gerar()
  {
    if (! isset($_GET['token'])) {
      $this->nome_url_cancelar = 'Entrar';

      $this->campoTexto('matricula', $this->_getLabel('matricula'), $_POST['matricula'],
        50, 50, TRUE, FALSE, FALSE, $this->_getHelp('matricula'));


      // fixup para mover o widget para o local correto, necessário pois chrome não executa
      // o script caso seja usado $this->campoRotulo('...', '...', '<script...>')
      $this->campoRotulo('replace_by_recaptcha_widget_wrapper',
                         'Confirma&ccedil;&atilde;o visual',
                         '<div id="replace_by_recaptcha_widget"></div>');

      echo $this->getRecaptchaWidget();
      echo "<script type='text/javascript'>
              function replaceRecaptchaWidget() {
                var emptyElement = document.getElementById('replace_by_recaptcha_widget');
                var originElement = document.getElementById('recaptcha_widget_div');
                var movedElement = emptyElement.parentNode.replaceChild(originElement, emptyElement);
              }

              window.onload = replaceRecaptchaWidget;
            </script>";
    }
    else {
      $this->setUserByStatusToken('redefinir_senha-' . $_GET['token']);

      $this->campoRotulo('matricula', $this->_getLabel('matricula'), $this->getEntity()->matricula);
      $this->campoSenha('password', $this->_getLabel('nova_senha'), @$_POST['password'], TRUE);
      $this->campoSenha('password_confirmation', $this->_getLabel('confirmacao_senha'), @$_POST['password_confirmation'], TRUE);
    }

    $this->url_cancelar = '/intranet/index.php';
    $this->nome_url_sucesso = 'Redefinir';
    $this->mensagem = $this->getMsgs();
  }


  // considera como novo quando nao recebe token
  protected function _initNovo()
  {
    return ! isset($_GET['token']);
  }


  protected function _initEditar()
  {
    return isset($_GET['token']);
  }


  public function Novo()
  {
    if (! $this->hasMsgWithType('error')) {
      if (! $this->getRecaptchaWidget()->validate()) {
        $this->appendMsg('Por favor, informe a confirma&ccedil;&atilde;o visual no respectivo campo.' .
                         'tente novamente.', 'error');
      }
      elseif ($this->setUserByMatricula($_POST['matricula']))
        $this->sendResetPasswordMail();
    }

    $this->mensagem = $this->getMsgs();
    return ! $this->hasMsgWithType('error');
  }


  public function Editar()
  {
    $controlador = new clsControlador();

    if (! $this->hasMsgWithType('error') &&
        $this->setUserByStatusToken('redefinir_senha-' . $_GET['token']) &&
        $this->updatePassword() &&
        $controlador->canStartLoginSession($this->getEntity()->ref_cod_pessoa_fj)) {
      $this->sendUpdatedPasswordMail();
      $controlador->startLoginSession($this->getEntity()->ref_cod_pessoa_fj, '/intranet/index.php');
    }

    // #TODO refatorar, criar metodo mergeMsgs em lib/Portabilis/Message,
    // mover referências msgs */services para instancia desta classe
    foreach($controlador->messages->getMsgs() as $msg) {
      $this->appendMsg($msg['msg'], $msg['type']);
    }

    $this->mensagem = $this->getMsgs();
    return ! $this->hasMsgWithType('error');
  }


  function Excluir()
  {
    return false;
  }


  protected function _save()
  {
    return false;
  }


  /* overwrite Core/Controller/Page/Abstract.php para renderizar html,
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
    $html .= $viewBase->MakeFootHtml();

    echo $html;
  }


  protected function setUserByStatusToken($statusToken) {
    $result = false;
    try {
      if(empty($statusToken) && ! is_numeric($statusToken))
        $this->appendMsg('Deve ser recebido um token.', 'error');
      else {
        $user = $this->getDataMapper()->findAllUsingPreparedQuery(array(),
                                                               array('status_token = $1'),
                                                               array($statusToken),
                                                               array(),
                                                               false);

        if(! empty($user) && ! empty($user[0]->ref_cod_pessoa_fj)) {
          $this->setEntity($user[0]);
          $result = true;
        }
        else
          $this->appendMsg('Nenhum usu&aacute;rio encontrado com o token recebido. Verifique se voc&ecirc; ' .
                           'esta acessando o link enviado no ultimo e-mail.', 'error', false, 'error');
      }
    }
    catch (Exception $e) {
      $this->appendMsg('Ocorreu um erro inesperado ao recuperar o usu&aacute;rio, por favor, ' .
                       'tente novamente.', 'error');

      error_log("Exception ocorrida ao redefinir senha (setUserByStatusToken), " .
                "matricula: $matricula, erro: " .  $e->getMessage());
    }

    return $result;
  }


  protected function setUserByMatricula($matricula) {
    $result = false;
    $user = null;

    try {
      if(empty($matricula) && ! is_numeric($matricula))
        $this->appendMsg('Informe uma matr&iacute;cula.', 'error');
      else {
        $user = $this->getDataMapper()->findAllUsingPreparedQuery(array(),
                                                               array('matricula = $1'),
                                                               array($matricula),
                                                               array(),
                                                               false);

        if(! empty($user) && ! empty($user[0]->ref_cod_pessoa_fj)) {
          $this->setEntity($user[0]);
          $result = true;
        }
        else
          $this->appendMsg('Nenhum usu&aacute;rio encontrado com a matr&iacute;cula informada.',
                           'error', false, 'error');
      }
    }
    catch (Exception $e) {
      $this->appendMsg('Ocorreu um erro inesperado ao recuperar o usu&aacute;rio, por favor, ' .
                       'verifique o valor informado e tente novamente.', 'error');

      error_log("Exception ocorrida ao redefinir senha (setUserByMatricula), " .
                "matricula: $matricula, erro: " .  $e->getMessage());
    }

    return $result;
  }


  protected function sendResetPasswordMail() {
    $user  = $this->getEntity();
    $email = $user->email;

    if(empty($email)) {
      $this->appendMsg('Parece que seu usu&aacute;rio n&atilde;o possui um e-mail definido, por favor, '.
                       'solicite ao administrador do sistema para definir seu e-mail (em DRH > Cadastro '.
                       'de funcion&aacute;rios) e tente novamente.', 'error');
    }
    else {
      $token = $this->setTokenRedefinicaoSenha();
      if ($token != false) {

        $link = $_SERVER['HTTP_REFERER'] . "?token=$token";

        $subject = "Redefinição de senha - i-Educar - {$_SERVER['HTTP_HOST']}";
        $message = "Olá!\n\n" .
                   "Recebemos uma solicitação de redefinição de senha para a matrícula {$user->matricula}.\n\n" .
                   "Para redefinir sua senha acesse o link: $link\n\n" .
                   "Caso você não tenha feito esta solicitação, por favor, ignore esta mensagem.";

        $successMsg = 'Enviamos um e-mail para voc&ecirc;, por favor, clique no link recebido para redefinir sua senha.';
        $errorMsg   = 'N&atilde;o conseguimos enviar um e-mail para voc&ecirc;, por favor, tente novamente mais tarde.';

        if($this->mailer->sendMail($email, $subject, $message))
          $this->appendMsg($successMsg, 'success');
        else
          $this->appendMsg($errorMsg, 'error');
      }
    }
  }


  protected function sendUpdatedPasswordMail() {
    $user  = $this->getEntity();
    $email = $user->email;

    $link = explode('?', $_SERVER['HTTP_REFERER']);
    $link = $link[0];
    $subject = "Sua senha foi alterada - i-Educar - {$_SERVER['HTTP_HOST']}";
    $message = "Olá!\n\n" .
               "A senha da matrícula {$user->matricula} foi alterada recentemente.\n\n" .
               "Caso você não tenha feito esta alteração, por favor, tente alterar sua senha acessando o link $link ou entre em contato com o administrador do sistema (solicitando mudança da sua senha), pois sua conta pode estar sendo usada por alguma pessoa não autorizada.";

    return $this->mailer->sendMail($email, $subject, $message);
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
      $this->appendMsg('Erro ao setar token redefini&ccedil;&atilde;o de senha.', 'error');
      error_log("Exception ocorrida ao setar token reset senha, matricula: {$user->matricula}, erro: " .  $e->getMessage());
      return false;
    }
  }


  protected function updatePassword() {
    $result = false;
    $user = $this->getEntity();

    try {
      $password = $_POST['password'];
      $passwordConfirmation = $_POST['password_confirmation'];
      $statusToken = '';

      if (empty($password))
        $this->appendMsg('Por favor informe uma senha.', 'error');
      elseif (strlen($password) < 8)
        $this->appendMsg('Por favor informe uma senha segura, com pelo menos 8 caracteres.', 'error');
      elseif ($password != $passwordConfirmation)
        $this->appendMsg('A senha e confirma&ccedil;&atilde;o de senha devem ser as mesmas.', 'error');
      elseif ($password == $user->matricula)
        $this->appendMsg('Informe uma senha diferente da matricula.', 'error');
      else {
        $user->setOptions(array('senha' => md5($password), 'status_token' => $statusToken));
        $this->getDataMapper()->save($user);

        $this->appendMsg('Senha alterada com sucesso.', 'success');
        $result = true;
      }
    }
    catch (Exception $e) {
      $this->appendMsg('Erro ao atualizar de senha.', 'error');
      error_log("Exception ocorrida ao atualizar senha, matricula: {$user->matricula}, erro: " .  $e->getMessage());
    }
    return $result;
  }


  //#TODO mover metodos *msg* para modulo genérico?
  protected function appendMsg($msg, $type="error", $encodeToUtf8 = false, $ignoreIfHasMsgWithType = ''){

    if (empty($ignoreIfHasMsgWithType) || ! $this->hasMsgWithType($ignoreIfHasMsgWithType)) {
      if ($encodeToUtf8)
        $msg = utf8_encode($msg);

      //error_log("$type msg: '$msg'");
      $this->_msgs[] = array('msg' => $msg, 'type' => $type);
    }
  }


  protected function hasMsgWithType($type) {
    $hasMsg = false;

    foreach ($this->_msgs as $m){
      if ($m['type'] == $type) {
        $hasMsg = true;
        break;
      }
    }

    return $hasMsg;
  }


  protected function getMsgs() {
    $msgs = '';
    foreach($this->_msgs as $m)
      $msgs .= "<span class='{$m['type']}'>{$m['msg']}</span>";
    return $msgs;
  }

  #TODO generalizar este metodo (duplicado em clsControlador)
  // see http://www.google.com/recaptcha && http://pear.php.net/package/Services_ReCaptcha
  protected function getRecaptchaWidget() {
    if (! isset($this->_recaptchaWidget)) {
      $recaptchaConfigs = $GLOBALS['coreExt']['Config']->app->recaptcha;
      $this->_recaptchaWidget = new Services_ReCaptcha($recaptchaConfigs->public_key,
                                          $recaptchaConfigs->private_key,
                                          array('lang' => $recaptchaConfigs->options->lang,
                                                'theme' => $recaptchaConfigs->options->theme,
                                                'secure' => $recaptchaConfigs->options->secure == '1'));
    }

    return $this->_recaptchaWidget;
  }

}
?>
