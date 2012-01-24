<?php

/*
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
 */

require_once 'include/clsBanco.inc.php';

/* requer Services_ReCaptcha:
 $ pear install Services_ReCaptcha */
require_once 'Services/ReCaptcha.php';

/**
 * clsControlador class.
 *
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Classe disponível desde a versão 1.0.0
 * @version  $Id: /ieducar/branches/1.1.0-avaliacao/ieducar/intranet/include/clsControlador.inc.php 662 2009-11-17T18:28:48.404882Z eriksen  $
 */
class clsControlador
{

  /**
   * @var boolean
   */
  public $logado;

  /**
   * @var string
   */
  public $erroMsg;


  /**
   * Construtor.
   */
  public function clsControlador()
  {

    /*
      Desabilitado esta linha para usar o valor setado no php.ini > session.cookie_lifetime  
      @session_set_cookie_params(200);
    */

    @session_start();

    if ('logado' == $_SESSION['itj_controle']) {
      $this->logado = TRUE;
    }
    else {
      $this->logado = FALSE;
    }

    // Controle dos menus
    if (isset($_GET['mudamenu']) && isset($_GET['categoria']) && isset($_GET['acao']))
    {
      if ($_GET['acao']) {
        $_SESSION['menu_opt'][$_GET['categoria']] = 1;
        $_SESSION['menu_atual'] = $_GET['categoria'];
      }
      else {
        // Está apagando variável session com o índice dado por $_GET
        unset($_SESSION['menu_opt'][$_GET['categoria']]);
        if ($_SESSION['menu_atual'] == $_GET['categoria']) {
          unset($_SESSION['menu_atual']);
        }
      }

      $db = new clsBanco();
      if (isset($_SESSION['id_pessoa'])) {
        $db->Consulta("UPDATE funcionario SET opcao_menu = '" . serialize( $_SESSION['menu_opt'] ) . "' WHERE ref_cod_pessoa_fj = '" . $_SESSION['id_pessoa'] . "'");
      }
    }

    session_write_close();
  }

  /**
   * Retorna TRUE para usuário logado
   * @return  boolean
   */
  public function Logado()
  {
    return $this->logado;
  }

  
  /**
   * Executa o login do usuário.
   */
  public function obriga_Login()
  {
    if ($_POST['login'] && $_POST['senha']) {
      $this->logar(TRUE);
    }
    if (!$this->logado) {
      $this->logar(FALSE);
    }
  }

  //novo metodo login
  public function Logar($validateCredentials) {
    $this->_loginMsgs = array();
    $this->_maximoTentativasFalhas = 6;

    if ($validateCredentials) {
      $username = @$_POST['login'];
      $password = md5(@$_POST['senha']);
      $userId = $this->validateUser($username, $password);

      if ($this->canStartLoginSession($userId))
        $this->startLoginSession($userId);
      else {
        $this->renderLoginPage();
      }
    }
    else
      $this->renderLoginPage();
  }


  //metodos usados pelo novo metodo de login
  protected function validateUser($username, $password) {
    $result = false;

    if (! $this->validateHumanAccess()) {
      $this->appendLoginMsg("Parece que você errou a senha muitas vezes, por favor, preencha o " .
                            "campo de confirmação visual ou <a class='light decorated' href='/module/Usuario/RedefinirSenha'>tente redefinir sua senha</a>.", "error", false, "error");
    }
    else {
      $sql = "SELECT ref_cod_pessoa_fj FROM portal.funcionario WHERE matricula = $1 and senha = $2";
      $userId = $this->fetchPreparedQuery($sql, array($username, $password), true, 'first-field');

      if (! is_numeric($userId)) {
        $this->appendLoginMsg("Usuário ou senha incorreta.", "error");
        $this->incrementTentativasLogin();
      }
      else {
        $this->unsetTentativasLogin();
        $result = $userId;
      }
    }
    return $result;
  }


  public function canStartLoginSession($userId) {

    if (! $this->hasLoginMsgWithType("error")) {
      $sql = "SELECT ativo, proibido, tempo_expira_conta, data_reativa_conta, ip_logado as ip_ultimo_acesso, data_login " .
             "FROM portal.funcionario WHERE ref_cod_pessoa_fj = $1";
      $user = $this->fetchPreparedQuery($sql, $userId, true, 'first-line');

      if ($user['ativo'] != '1') {
        $this->appendLoginMsg("Aparentemente sua conta de usuário esta inativa (expirada), por favor, " .
                              "entre em contato com o administrador do sistema.", "error", false, "error");
      }

      if ($user['proibido'] != '0') {
        $this->appendLoginMsg("Aparentemente sua conta não pode acessar o sistema, " .
                              "por favor, entre em contato com o administrador do sistema.", "error", false, "error");
      }

      /* considera como expirado caso data_reativa_conta + tempo_expira_conta <= now
         obs: ao salvar drh > cadastro funcionario, seta data_reativa_conta = now */
      $contaExpirou = ! empty($user['tempo_expira_conta']) && ! empty($user['data_reativa_conta']) &&
                      time() - strtotime($user['data_reativa_conta']) > $user['tempo_expira_conta'] * 60 * 60 * 24;

      if($contaExpirou) {
        $sql = "UPDATE funcionario SET ativo = 0 WHERE ref_cod_pessoa_fj = $1";
        $this->fetchPreparedQuery($sql, $userId, true);

        $this->appendLoginMsg("Aparentemente a conta de usuário expirou, por favor, " .
                              "entre em contato com o administrador do sistema.", "error", false, "error");
      }

      // considera como acesso multiplo, acesso em diferentes IPs em menos de $tempoMultiploAcesso minutos
      $tempoMultiploAcesso = 10;
      $tempoEmEspera = abs(time() - strftime("now") - strtotime($user['data_login'])) / 60;

      $multiploAcesso = $tempoEmEspera <= $tempoMultiploAcesso &&
                        $user['ip_ultimo_acesso'] != $this->getClientIP();
    
      if ($multiploAcesso) {
        $minutosEmEspera = round($tempoMultiploAcesso - $tempoEmEspera) + 1;
        $this->appendLoginMsg("Aparentemente sua conta foi acessada em outro computador nos últimos " .
                              "$tempoMultiploAcesso minutos, caso não tenha sido você, " . 
                              "por favor, altere sua senha ou tente novamente em $minutosEmEspera minutos",
                              "error", false, "error");
      }
      #TODO verificar se conta nunca usada (exibir "Sua conta n&atilde;o est&aacute; ativa. Use a op&ccedil;&atilde;o 'Nunca usei a intrenet'." ?)
    }
    return ! $this->hasLoginMsgWithType("error");
  }


  public function startLoginSession($userId, $redirectTo = '') {
    $sql = "SELECT ref_cod_pessoa_fj, opcao_menu, ref_cod_setor_new, tipo_menu, email, status_token FROM funcionario WHERE ref_cod_pessoa_fj = $1";
    $record = $this->fetchPreparedQuery($sql, $userId, true, 'first-line');

    @session_start();
    $_SESSION = array();
    $_SESSION['itj_controle'] = 'logado';
    $_SESSION['id_pessoa']    = $record['ref_cod_pessoa_fj'];
    $_SESSION['pessoa_setor'] = $record['ref_cod_setor_new'];
    $_SESSION['menu_opt']     = unserialize($record['opcao_menu']);
    $_SESSION['tipo_menu']    = $record['tipo_menu'];
    @session_write_close();

    $this->logado = true;
    $this->appendLoginMsg("Usuário logado com sucesso.", "success");

    $this->logAccess($userId);
    $this->destroyUserStatusToken($userId);

    //redireciona para usuário informar email, caso este seja inválido
    if (! filter_var($record['email'], FILTER_VALIDATE_EMAIL))
       header("Location: /module/Usuario/AlterarEmail");
    elseif(! empty($redirectTo))
       header("Location: $redirectTo");
  }


  protected function destroyLoginSession($addMsg = false) {
    $tentativasLoginFalhas = $_SESSION['tentativas_login_falhas'];
    @session_start();
    $_SESSION = array();
    @session_destroy();

    //mantem tentativas_login_falhas, até que senha senha informada corretamente
    @session_start();
    $_SESSION['tentativas_login_falhas'] = $tentativasLoginFalhas;
    @session_write_close();

    if ($addMsg)
      $this->appendLoginMsg("Usuário deslogado com sucesso.", "success");
  }


  /* Ao fazer login destroy solicitações em aberto, como redefinição de senha.
  */
  protected function destroyUserStatusToken($userId) {

    $statusTokensToDestoyOnLogin = array('redefinir_senha');

    $sql = "SELECT status_token FROM funcionario WHERE ref_cod_pessoa_fj = $1";
    $record = $this->fetchPreparedQuery($sql, $userId, true, 'first-line');

    $statusToken = explode('-', $record['status_token']);
    $statusToken = $statusToken[0];

    if(in_array($statusToken, $statusTokensToDestoyOnLogin)) {
      $sql = "UPDATE funcionario set status_token = '' WHERE ref_cod_pessoa_fj = $1";
      $record = $this->fetchPreparedQuery($sql, $userId, true);
    }    
  }


  protected function getClientIP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
      // pega o (ultimo) IP real caso o host esteja atrás de um proxy
      $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
      $ip = trim(array_pop($ip));
    }
    else
      $ip = $_SERVER['REMOTE_ADDR'];
    return $ip;
  }


  protected function logAccess($userId) {
    $sql = "UPDATE funcionario SET ip_logado = '{$this->getClientIP()}', data_login = NOW() WHERE ref_cod_pessoa_fj = $1";
    $this->fetchPreparedQuery($sql, $userId, true);
  }

  // see http://www.google.com/recaptcha && http://pear.php.net/package/Services_ReCaptcha
  protected function getRecaptchaWidget() {
    $recaptchaConfigs = $GLOBALS['coreExt']['Config']->app->recaptcha;
    $recaptcha = new Services_ReCaptcha($recaptchaConfigs->public_key, 
                                        $recaptchaConfigs->private_key,
                                        array('lang' => $recaptchaConfigs->options->lang,
                                              'theme' => $recaptchaConfigs->options->theme,
                                              'secure' => $recaptchaConfigs->options->secure == '1'));
    return $recaptcha;
  }


  protected function validateHumanAccess() {
    $result = false;

    if (! $this->atingiuTentativasLogin())
      $result = true;

    elseif ($this->getRecaptchaWidget()->validate()) {
      $this->unsetTentativasLogin();
      $result = true;
    }
    return $result;
  }


  protected function atingiuTentativasLogin($value) {
    return isset($_SESSION['tentativas_login_falhas']) &&
                 is_numeric($_SESSION['tentativas_login_falhas']) &&
                 $_SESSION['tentativas_login_falhas'] >= $this->_maximoTentativasFalhas;
  }


  protected function incrementTentativasLogin($value) {
    @session_start();
    if (! isset($_SESSION['tentativas_login_falhas']) or ! is_numeric($_SESSION['tentativas_login_falhas'])) 
      $_SESSION['tentativas_login_falhas'] = 1;
    else
      $_SESSION['tentativas_login_falhas'] += 1;
    @session_write_close();
  }


  protected function unsetTentativasLogin() {
    @session_start();
    unset($_SESSION['tentativas_login_falhas']);
    @session_write_close();
  }


  protected function renderLoginPage() {
    $this->destroyLoginSession();

    $templateName = 'templates/nvp_htmlloginintranet.tpl';
    $templateFile  = fopen($templateName, "r");
    $templateText = fread($templateFile, filesize($templateName));
    $templateText = str_replace( "<!-- #&ERROLOGIN&# -->", $this->getLoginMsgs(), $templateText);

    $requiresHumanAccessValidation = isset($_SESSION['tentativas_login_falhas']) &&
                                     is_numeric($_SESSION['tentativas_login_falhas']) &&
                                     $_SESSION['tentativas_login_falhas'] >= $this->_maximoTentativasFalhas;

    if ($requiresHumanAccessValidation)
      $templateText = str_replace( "<!-- #&RECAPTCHA&# -->", $this->getRecaptchaWidget(), $templateText);

    fclose($templateFile);
    die($templateText);
  }


  protected function fetchPreparedQuery($sql, $params = array(), $hideExceptions = true, $returnOnly = '') {
    try{    
      $result = array();
      $db = new clsBanco();
      if ($db->execPreparedQuery($sql, $params) != false) {

        while ($db->ProximoRegistro())
          $result[] = $db->Tupla();

        if ($returnOnly == 'first-line' and isset($result[0]))
          $result = $result[0];
        elseif ($returnOnly == 'first-field' and isset($result[0]) and isset($result[0][0]))
          $result = $result[0][0];
      }
    }
    catch(Exception $e) 
    {
      if (! $hideExceptions)
        $this->appendLoginMsg($e->getMessage(), "error", true);
    }
    return $result;
  }


  protected function appendLoginMsg($msg, $type="error", $encodeToUtf8 = false, $ignoreIfHasMsgWithType = ''){
    if (empty($ignoreIfHasMsgWithType) || ! $this->hasLoginMsgWithType($ignoreIfHasMsgWithType)) {
      if ($encodeToUtf8)
        $msg = utf8_encode($msg);

      //error_log("$type msg: '$msg'");
      $this->_loginMsgs[] = array('msg' => $msg, 'type' => $type);
    }
  }


  protected function hasLoginMsgWithType($type) {
    $hasMsg = false;

    foreach ($this->_loginMsgs as $m){
      if ($m['type'] == $type) {
        $hasMsg = true;
        break;
      }
    }

    return $hasMsg;
  }


  protected function getLoginMsgs() {
    $msgs = '';
    foreach($this->_loginMsgs as $m)
      $msgs .= "<p class='{$m['type']}'>{$m['msg']}</p>";
    return $msgs;
  }
}
