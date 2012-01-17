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
   * Faz o login do usuário.
   * @param  mixed  $acao
   */
  /*public function _Logar($acao)
  {
    if ($acao)
    {
      $login = @$_POST['login'];
      $senha = md5(@$_POST['senha']);
      $db    = new clsBanco();

      $db->Consulta("SELECT ref_cod_pessoa_fj FROM funcionario WHERE matricula = '{$login}'");
      if ($db->ProximoRegistro())
      {
        list($idpes) = $db->Tupla();

        // Padrão: meia hora atrás
        $intervalo = date("Y-m-d H:i", time() - (60 * 1 ));

        // Se o último login bem sucedido foi em menos de meia hora, conta somente dali para a frente
        $db->consulta("SELECT data_hora FROM acesso WHERE cod_pessoa = '{$idpes}' AND data_hora > '{$intervalo}' AND sucesso = 't' ORDER BY data_hora DESC LIMIT 1" );
        if ($db->Num_Linhas()) {
          $db->ProximoRegistro();
          list($intervalo) = $db->Tupla();
        }

        // Trava usuário se tentar login mais de 5 vezes
        $tentativas = $db->CampoUnico("SELECT COUNT(0) FROM acesso WHERE cod_pessoa = '{$idpes}' AND data_hora > '{$intervalo}' AND sucesso = 'f'" );
        if ($tentativas > 5)
        {
          $hora_ultima_tentativa = $db->CampoUnico("SELECT data_hora FROM acesso WHERE cod_pessoa = '{$idpes}' ORDER BY data_hora DESC LIMIT 1 OFFSET 4" );
          $hora_ultima_tentativa = explode(".",$hora_ultima_tentativa);
          $hora_ultima_tentativa = $hora_ultima_tentativa[0];

          $data_libera = date("d/m/Y H:i",
            strtotime($hora_ultima_tentativa) + (60 * 30));

          die("<html><body></body><script>alert('Houveram mais de 5 tentativas frustradas de acessar a sua conta na última meia hora.\\nPor segurança, sua conta ficará interditada até: {$data_libera}');document.location.href='/intranet';</script></html>");
        }

        $db->Consulta( "SELECT ref_cod_pessoa_fj, opcao_menu, ativo, tempo_expira_senha, tempo_expira_conta, data_troca_senha, data_reativa_conta, proibido, ref_cod_setor_new, tipo_menu FROM funcionario WHERE ref_cod_pessoa_fj = '{$idpes}' AND senha = '{$senha}'" );
        if ($db->ProximoRegistro())
        {
          list($id_pessoa, $opcaomenu, $ativo, $tempo_senha,
            $tempo_conta, $data_senha, $data_conta, $proibido,
            $setor_new, $tipo_menu) = $db->Tupla();

          if (!$proibido)
          {
            if ($ativo)
            {
              // Usuário ativo, verifica se a conta expirou
              $expirada = FALSE;
              if (!empty($tempo_conta) && !empty($data_conta))
              {
                if (time() - strtotime($data_conta) > $tempo_conta * 60 * 60 * 24) {
                  // Conta expirada
                  $db->Consulta("UPDATE funcionario SET ativo='0' WHERE ref_cod_pessoa_fj = '$id_pessoa'");
                  die("<html><body></body><script>alert( 'Sua conta na intranet expirou.\nContacte um administrador para reativa-la.' );document.location.href='/intranet';</script></html>");
                }
              }

              // Vendo se a senha não expirou
              if (!empty($tempo_senha) && ! empty($data_senha)) {
                if (time() - strtotime($data_senha) > $tempo_senha * 60 * 60 * 24) {
                  // Senha expirada, pede que mude a senha
                  //die("<html><body><form id='reenvio' name='reenvio' action='usuario_trocasenha.php' method='POST'><input type='hidden' name='cod_pessoa' value='{$id_pessoa}'></form></body><script>document.getElementById('reenvio').submit();</script></html>");
               // echo("<script>showExpansivelIframe(800, 270, 'troca_senha_pop.php', 1);</script>");

                echo("<script type='text/javascript'>alert('Sua senha expirou, por favor atualize sua senha para continuar utilizando o sistema.')</script>");              

                }
              }

              // Pega o endereço IP do host, primeiro com HTTP_X_FORWARDED_FOR (para pegar o IP real
              // caso o host esteja atrás de um proxy)
              if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
                // No caso de múltiplos IPs, pega o último da lista
                $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $ip_maquina = trim(array_pop($ip));
              }
              else {
                $ip_maquina = $_SERVER['REMOTE_ADDR'];
              }

              $sql = "SELECT ip_logado, data_login FROM funcionario WHERE ref_cod_pessoa_fj = {$id_pessoa}";
              $db2 = new clsBanco();
              $db2->Consulta($sql);
              while ($db2->ProximoRegistro())
              {
                list($ip_banco, $data_login) = $db2->Tupla();
                if ($ip_banco)
                {

                  if (abs(time() - strftime("now") - strtotime($data_login)) <= 10 * 60
                    && $ip_banco != $ip_maquina) {
                    echo("<html><body></body><script>alert('Sua conta de usuário foi acessada recentemente de outro computador.\\n\nCaso não tenha sido você, altere sua senha.');</script></html>");
                  }

                  $sql = "UPDATE funcionario SET data_login = NOW() WHERE ref_cod_pessoa_fj = {$id_pessoa}";
                  $db2->Consulta($sql);
                }
                else {
                  $sql = "UPDATE funcionario SET ip_logado = '{$ip_maquina}', data_login = NOW() WHERE ref_cod_pessoa_fj = {$id_pessoa}";
                  $db2->Consulta($sql);
                }
              }

              // Login do usuário, grava dados na sessão
              @session_start();
              $_SESSION = array();
              $_SESSION['itj_controle'] = 'logado';
              $_SESSION['id_pessoa']    = $id_pessoa;
              $_SESSION['pessoa_setor'] = $setor_new;
              $_SESSION['menu_opt']     = unserialize( $opcaomenu );
              $_SESSION['tipo_menu']    = $tipo_menu;
              @session_write_close();

              $this->logado = TRUE;
            }
            else
            {
              if (!empty($tempo_conta) && !empty($data_conta))
              {
                if (time() - strtotime( $data_conta ) > $tempo_conta * 60 * 60 * 24) {
                  $this->erroMsg = "Sua conta expirou. Contate o administrador para reativá-la.";
                  $expirada = 1;
                }
                else {
                  $this->erroMsg = "Sua conta n&atilde;o est&aacute; ativa. Use a op&ccedil;&atilde;o 'Nunca usei a intranet'.";
                  $expirada = 0;
                }
              }
            }
          }
          else
          {
            $this->erroMsg = "Imposs&iacute;vel realizar login.";
            $this->logado  = FALSE;
          }
        }
        else
        {
          if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
            // No caso de múltiplos IPs, pega o último da lista
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip_de_rede = trim(array_pop($ip));
          }

          $ip = empty($_SERVER['REMOTE_ADDR']) ? 'NULL' : $_SERVER['REMOTE_ADDR'];
          $ip_de_rede = empty($ip_de_rede) ? 'NULL' : $ip_de_rede;

          $db->Consulta("INSERT INTO acesso (data_hora, ip_externo, ip_interno, cod_pessoa, sucesso) VALUES (now(), '{$ip}', '{$ip_de_rede}',  {$idpes}, 'f')");

          $this->erroMsg = '<p class="erro">Matrícula ou Senha incorretos.</p>';
          $this->logado  = FALSE;
        }
      }
      else {
        $this->erroMsg = '<p class="erro">Matrícula ou Senha incorretos.</p>';
        $this->logado  = FALSE;
      }
    }
    else
    {
      $arquivo = 'templates/nvp_htmlloginintranet.tpl';
      $ptrTpl  = fopen($arquivo, "r");
      $strArquivo = fread($ptrTpl, filesize($arquivo));

      if ($this->erroMsg) {
        $strArquivo = str_replace( "<!-- #&ERROLOGIN&# -->", $this->erroMsg, $strArquivo );
      }

      fclose($ptrTpl);
      die($strArquivo);
      // @todo
      #throw new Exception($strArquivo);
    }
  }*/

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

    if ($validateCredentials) {
      $username = @$_POST['login'];
      $password = md5(@$_POST['senha']);
      $userId = $this->validateUser($username, $password);

      if ($this->canStartLoginSession($userId))
        $this->startLoginSession($userId);
      else {
        $this->validateHumanAccess();
        $this->renderLoginPage();
      }
    }
    else
      $this->renderLoginPage();
  }


  //metodos usados pelo novo metodo de login
  protected function validateUser($username, $password) {
    $sql = "SELECT ref_cod_pessoa_fj FROM portal.funcionario WHERE matricula = $1 and senha = $2";
    $userId = $this->fetchPreparedQuery($sql, array($username, $password), true, 'first-field');

    if (! is_numeric($userId))
      $this->appendLoginMsg("Usuário ou senha incorreta.", "error");

    return $userId;
  }


  protected function canStartLoginSession($userId) {

    if (! $this->hasLoginMsgWithType("error")) {
      if ($this->fetchPreparedQuery("SELECT ativo FROM portal.funcionario WHERE ref_cod_pessoa_fj = $1",
                                    $userId, true, 'first-field') != '1') {
        $this->appendLoginMsg("Aparentemente sua conta de usuário esta inativa (expirada), por favor, " .
                              "entre em contato com o administrador do sistema.", "error");
      }

      elseif ($this->fetchPreparedQuery("SELECT proibido FROM portal.funcionario WHERE ref_cod_pessoa_fj = $1",
                                    $userId, true, 'first-field') != '0') {
        $this->appendLoginMsg("Aparentemente sua conta não pode acessar o sistema, " .
                              "por favor, entre em contato com o administrador do sistema.", "error");
      }
    }

    #TODO verificar se conta expirou (se sim, inativar conta)
    #TODO verificar se senha expirou
    #TODO verifica se usuario acessou de outro ip em memos de 10 minutos (eliminar esta verificação ?), se bloquear setar $sql = "UPDATE funcionario SET data_login = NOW() WHERE ref_cod_pessoa_fj = {$id_pessoa}" ?;
    #TODO verificar se conta nunca usada (exibir mensagem ?)

    return ! $this->hasLoginMsgWithType("error");
  }


  protected function startLoginSession($userId) {
    $sql = "SELECT ref_cod_pessoa_fj, opcao_menu, ref_cod_setor_new, tipo_menu, email FROM funcionario WHERE ref_cod_pessoa_fj = $1";
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

    #TODO setar data_login, ip_logado ? "UPDATE funcionario SET ip_logado = '{$ip_maquina}', data_login = NOW() WHERE ref_cod_pessoa_fj = {$id_pessoa}";

    //redireciona para usuário informar email, caso este seja inválido
    if (! filter_var($record['email'], FILTER_VALIDATE_EMAIL))
       header("Location: /module/Usuario/AlterarEmail");
  }


  protected function destroyLoginSession($addMsg = false) {
    @session_start();
    $_SESSION = array();
    @session_destroy();

    if ($addMsg)
      $this->appendLoginMsg("Usuário deslogado com sucesso.", "success");
  }


  protected function validateHumanAccess() {
    /* #TODO se ocorreram mais de 5 tentativas erradas nos ultimos minutos,
             confirmar se usuário que esta acessando é humano, like http://www.google.com/recaptcha */
    return true;
  }


  protected function renderLoginPage() {
    $this->destroyLoginSession();

    $templateName = 'templates/nvp_htmlloginintranet.tpl';
    $templateFile  = fopen($templateName, "r");
    $templateText = fread($templateFile, filesize($templateName));
    $templateText = str_replace( "<!-- #&ERROLOGIN&# -->", $this->getLoginMsgs(), $templateText);

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


  protected function appendLoginMsg($msg, $type="error", $encodeToUtf8 = false){
    if ($encodeToUtf8)
      $msg = utf8_encode($msg);

    //error_log("$type msg: '$msg'");
    $this->_loginMsgs[] = array('msg' => $msg, 'type' => $type);
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
