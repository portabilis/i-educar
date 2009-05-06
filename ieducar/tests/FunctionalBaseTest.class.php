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

/**
 * FunctionBaseTest class.
 *
 * Contém as configurações de acesso ao servidor Selenium RC, a conta de usuário
 * a ser utilizada no teste e alguns métodos auxiliares.
 *
 * Muda o diretório atual para que os testes possam ser facilmente invocados
 * em qualquer subdiretório do sistema.
 *
 * Abstrai o PHPUnit, diminuindo a dependência de seu uso. Inclui a classe
 * de banco de dados para facilitar no tearDown de dados de teste.
 *
 * @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Test
 * @since    Classe disponível desde a versão 1.0.1
 * @version  $Id$
 */

chdir(realpath(dirname(__FILE__) . '/../') . '/intranet');
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once 'include/clsBanco.inc.php';

abstract class FunctionalBaseTest extends PHPUnit_Extensions_SeleniumTestCase {

  // Configurações do Selenium RC
  static protected
    $slBrowserUrl = 'http://ieducar.local',
    $slBrowser    = '*firefox',
    $slPort       = 4444,
    $slHost       = 'localhost';

  // Conta de usuário para testes funcionais
  protected
    $slUsuarioLogin = 'ieducar',
    $slUsuarioSenha = '12345678';

  protected function setUp() {
    $this->setBrowser(self::$slBrowser);
    $this->setHost(self::$slHost);
    $this->setPort(self::$slPort);
    $this->setBrowserUrl(self::$slBrowserUrl);
  }

  protected function doLogin() {
    $this->open('/intranet');
    $this->type('login', $this->slUsuarioLogin);
    $this->type('senha', $this->slUsuarioSenha);
    $this->clickAndWait("//input[@value='Entrar']");
  }

  protected function doLogout() {
    $this->click("//img[@alt='Logout']");
  }

}