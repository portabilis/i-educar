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

/**
 * Portabilis_Utils_User class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_Utils_User {

  static $_currentUserId;
  static $_nivelAcesso;
  static $_permissoes;

  static function currentUserId() {
    if (! isset(self::$_currentUserId)) {
      @session_start();
      self::$_currentUserId = $_SESSION['id_pessoa'];
      session_write_close();
    }

    return self::$_currentUserId;
  }

  static function redirectToLogoff() {
    header('Location: /intranet/logof.php');
  }


  static function loggedIn(){
    return is_numeric(self::currentUserId());
  }

  // database helpers

  static function loadUsingCredentials($username, $password) {
    $sql     = "SELECT ref_cod_pessoa_fj FROM portal.funcionario WHERE matricula = $1 and senha = $2";
    $options = array('params' => array($username, $password), 'show_errors' => false, 'return_only' => 'first-field');
    $userId  = self::fetchPreparedQuery($sql, $options);

    if (is_numeric($userId))
      return self::load($userId);

    return null;
  }

  // TODO usar modules/Usuario/Model/Funcionario ao invés de executar select
  static function load($id) {
    if ($id == 'current_user')
      $id = self::currentUserId();
    elseif (! is_numeric($id))
      throw new Exception("'$id' is not a valid id, please send a numeric value or a string 'current_user'");

    $sql     = "SELECT ref_cod_pessoa_fj as id, opcao_menu, ref_cod_setor_new, tipo_menu, matricula, email, status_token,
                ativo, proibido, tempo_expira_conta, data_reativa_conta, tempo_expira_senha, data_troca_senha,
                ip_logado as ip_ultimo_acesso, data_login FROM portal.funcionario WHERE ref_cod_pessoa_fj = $1";

    $options       = array('params' => array($id), 'show_errors' => false, 'return_only' => 'first-line');
    $user          = self::fetchPreparedQuery($sql, $options);
    $user['super'] = $GLOBALS['coreExt']['Config']->app->superuser == $user['matricula'];


    /* considera como expirado caso usuario não admin e data_reativa_conta + tempo_expira_conta <= now
       obs: ao salvar drh > cadastro funcionario, seta data_reativa_conta = now */
    $user['expired_account'] = ! $user['super'] && ! empty($user['tempo_expira_conta']) &&
                               ! empty($user['data_reativa_conta']) &&
                               time() - strtotime($user['data_reativa_conta']) > $user['tempo_expira_conta'] * 60 * 60 * 24;


    // considera o periodo para expiração de senha definido nas configs, caso o tenha sido feito.
    $tempoExpiraSenha = $GLOBALS['coreExt']['Config']->app->user_accounts->default_password_expiration_period;

    if (! is_numeric($tempoExpiraSenha))
      $tempoExpiraSenha = $user['tempo_expira_senha'];

    /* considera como expirado caso data_troca_senha + tempo_expira_senha <= now */
    $user['expired_password'] = ! empty($user['data_troca_senha']) && ! empty($tempoExpiraSenha) &&
                                time() - strtotime($user['data_troca_senha']) > $tempoExpiraSenha * 60 * 60 * 24;

    return $user;
  }


  static function disableAccount($userId) {
    $sql     = "UPDATE portal.funcionario SET ativo = 0 WHERE ref_cod_pessoa_fj = $1";
    $options = array('params' => array($userId), 'show_errors' => false);

    self::fetchPreparedQuery($sql, $options);
  }


  /*
    Destroi determinado tipo de status_token de um usuário, como ocorre por exemplo após fazer login,
    onde solicitações de redefinição de senha em aberto são destruidas.
  */
  static function destroyStatusTokenFor($userId, $withType) {
    $sql     = "UPDATE portal.funcionario set status_token = '' WHERE ref_cod_pessoa_fj = $1 and status_token like $2";
    $options = array('params' => array($userId, "$withType-%"), 'show_errors' => false);

    self::fetchPreparedQuery($sql, $options);
  }


  static function logAccessFor($userId, $clientIP) {
    $sql     = "UPDATE portal.funcionario SET ip_logado = '$clientIP', data_login = NOW() WHERE ref_cod_pessoa_fj = $1";
    $options = array('params' => array($userId), 'show_errors' => false);

    self::fetchPreparedQuery($sql, $options);
  }



  // permissões

  static function getClsPermissoes() {
    if (! isset(self::$_permissoes))
      self::$_permissoes = new clsPermissoes();

    return self::$_permissoes;
  }


  static function getNivelAcesso() {
    if (! isset(self::$_nivelAcesso))
      self::$_nivelAcesso = self::getClsPermissoes()->nivel_acesso(self::currentUserId());

    return self::$_nivelAcesso;
  }


  # TODO verificar se é possivel usar a logica de App_Model_NivelAcesso
  static function hasNivelAcesso($nivelAcessoType) {
    $niveisAcesso = array('POLI_INSTITUCIONAL' => 1,
                          'INSTITUCIONAL'      => 2,
                          'SOMENTE_ESCOLA'     => 4,
                          'SOMENTE_BIBLIOTECA' => 8);

    if (! isset($niveisAcesso[$nivelAcessoType]))
      throw new CoreExt_Exception("Nivel acesso '$nivelAcessoType' não definido.");

    return self::getNivelAcesso() == $niveisAcesso[$nivelAcessoType];
  }


  static function canAccessEscola($id) {
    return (self::hasNivelAcesso('POLI_INSTITUCIONAL') ||
            self::hasNivelAcesso('INSTITUCIONAL')      ||
            self::getClsPermissoes()->getEscola(self::currentUserId()) == $id);
  }


  // wrappers for Portabilis*Utils*

  protected static function fetchPreparedQuery($sql, $options = array()) {
    return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
  }
}