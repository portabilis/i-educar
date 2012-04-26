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
require_once 'lib/Portabilis/Message.php';
require_once 'lib/Portabilis/Mailer.php';

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

    $this->messages = new Message();
    $this->_maximoTentativasFalhas = 6;
    $this->mailer = new Mailer();
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


  // novo metodo login, logica quebrada em metodos menores
  public function Logar($validateCredentials) {
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


  // renderiza o template de login, com as mensagens adicionadas durante validações
  protected function renderLoginPage() {
    $this->destroyLoginSession();

    $templateName = 'templates/nvp_htmlloginintranet.tpl';
    $templateFile  = fopen($templateName, "r");
    $templateText = fread($templateFile, filesize($templateName));
    $templateText = str_replace( "<!-- #&ERROLOGIN&# -->", $this->messages->toHtml('p'), $templateText);

    $requiresHumanAccessValidation = isset($_SESSION['tentativas_login_falhas']) &&
                                     is_numeric($_SESSION['tentativas_login_falhas']) &&
                                     $_SESSION['tentativas_login_falhas'] >= $this->_maximoTentativasFalhas;

    if ($requiresHumanAccessValidation)
      $templateText = str_replace( "<!-- #&RECAPTCHA&# -->", $this->getRecaptchaWidget(), $templateText);

    fclose($templateFile);
    die($templateText);
  }


  // valida se o usuário e senha informados, existem no banco de dados.
  protected function validateUser($username, $password) {
    if (! $this->validateHumanAccess()) {
      $msg = "Parece que você errou a senha muitas vezes, por favor, preencha o campo de " .
             "confirmação visual ou <a class='light decorated' href='/module/Usuario/Rede" .
             "finirSenha'>tente redefinir sua senha</a>.";
      $this->messages->append($msg, "error", false, "error");
    }
    else {
      $sql = "SELECT ref_cod_pessoa_fj FROM portal.funcionario WHERE matricula = $1 and senha = $2";
      $userId = $this->fetchPreparedQuery($sql, array($username, $password), true, 'first-field');

      if (! is_numeric($userId)) {
        $this->messages->append("Usuário ou senha incorreta.", "error");
        $this->incrementTentativasLogin();
      }
      else {
        $this->unsetTentativasLogin();
        return $userId;
      }
    }

    return false;
  }


  // valida se o usuário, pode acessar o sistema.
  public function canStartLoginSession($userId) {

    if (! $this->messages->hasMsgWithType("error")) {
      $sql = "SELECT ativo, proibido, tempo_expira_conta, data_reativa_conta, ip_logado " .
             "as ip_ultimo_acesso, data_login, matricula, email FROM portal.funcionario WHERE ref_cod_pessoa_fj = $1";

      $user = $this->fetchPreparedQuery($sql, $userId, true, 'first-line');

      $isSuperUser = $GLOBALS['coreExt']['Config']->app->superuser == $user['matricula'];

      if ($user['ativo'] != '1') {
        $this->messages->append("Aparentemente sua conta de usuário esta inativa (expirada), por favor, " .
                                "entre em contato com o administrador do sistema.", "error", false, "error");
      }

      if ($user['proibido'] != '0') {
        $this->messages->append("Aparentemente sua conta não pode acessar o sistema, " .
                                "por favor, entre em contato com o administrador do sistema.",
                                "error", false, "error");
      }

      /* considera como expirado caso data_reativa_conta + tempo_expira_conta <= now
         obs: ao salvar drh > cadastro funcionario, seta data_reativa_conta = now */
      $contaExpirou = ! empty($user['tempo_expira_conta']) && ! empty($user['data_reativa_conta']) &&
                      time() - strtotime($user['data_reativa_conta']) > $user['tempo_expira_conta'] * 60 * 60 * 24;

      if($contaExpirou) {
        $sql = "UPDATE funcionario SET ativo = 0 WHERE ref_cod_pessoa_fj = $1";
        $this->fetchPreparedQuery($sql, $userId, true);

        $this->messages->append("Aparentemente a conta de usuário expirou, por favor, " .
                                "entre em contato com o administrador do sistema.", "error", false, "error");
      }

      // considera como acesso multiplo, acesso em diferentes IPs em menos de $tempoMultiploAcesso minutos
      $tempoMultiploAcesso = 10;
      $tempoEmEspera = abs(time() - strftime("now") - strtotime($user['data_login'])) / 60;

      $multiploAcesso = $tempoEmEspera <= $tempoMultiploAcesso &&
                        $user['ip_ultimo_acesso'] != $this->getClientIP();

      if ($multiploAcesso and $isSuperUser) {
        $subject = "Conta do super usuário {$_SERVER['HTTP_HOST']} acessada em mais de um local";

        $message = ("Aparentemente a conta do super usuário {$user['matricula']} foi acessada em " .
                    "outro computador nos últimos $tempoMultiploAcesso " .
                    "minutos, caso não tenha sido você, por favor, altere sua senha.\n\n" .
                    "Endereço IP último acesso: {$user['ip_ultimo_acesso']}\n".
                    "Endereço IP acesso atual: {$this->getClientIP()}");

        $this->mailer->sendMail($user['email'], $subject, $message);
      }
      elseif ($multiploAcesso) {
        $minutosEmEspera = round($tempoMultiploAcesso - $tempoEmEspera) + 1;
        $this->messages->append("Aparentemente sua conta foi acessada em outro computador nos últimos " .
                                "$tempoMultiploAcesso minutos, caso não tenha sido você, " .
                                "por favor, altere sua senha ou tente novamente em $minutosEmEspera minutos",
                                "error", false, "error");
      }
      #TODO verificar se conta nunca usada (exibir "Sua conta n&atilde;o est&aacute; ativa. Use a op&ccedil;&atilde;o 'Nunca usei a intrenet'." ?)
    }

    return ! $this->messages->hasMsgWithType("error");
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
    $this->messages->append("Usuário logado com sucesso.", "success");

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
      $this->messages->append("Usuário deslogado com sucesso.", "success");
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


  // wrapper para $db->execPreparedQuery($sql, $params)
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
        $this->messages->append($e->getMessage(), "error", true);
    }
    return $result;
  }


  // Ao fazer login destroy solicitações em aberto, como redefinição de senha.
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
}
