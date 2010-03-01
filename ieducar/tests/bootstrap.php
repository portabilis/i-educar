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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Tests
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

/**
 * Arquivo de bootstrap para os testes do projeto. Configura o include_path e
 * inclui alguns arquivos necessários nos testes unitários e funcionais.
 */

error_reporting(E_ALL ^ E_STRICT);

// Diretório raiz do projeto.
$root = realpath(dirname(__FILE__) . '/../');

// Adiciona os diretórios tests/unit, tests/functional, tests/, ./ e intranet/
// ao include_path.
$paths = array();
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'tests', 'unit'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'tests', 'functional'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'tests'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, '.'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'intranet'));

// Configura o include_path.
set_include_path(join(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());

// Altera as configurações de session para os testes. Não usa cookies para
// permitir o teste de CoreExt_Session, já que o test runner do PHPUnit faz
// saída de buffer.
ini_set('session.use_cookies', 0);

// Arquivos em intranet/ usam includes com caminho relativo, muda diretório
// atual para evitar warnings e errors.
chdir($root . '/intranet');
unset($root, $paths);

require_once 'include/clsBanco.inc.php';
require_once 'CustomPdo.php';
require_once 'TestCollector.php';
require_once 'UnitBaseTest.class.php';
require_once 'IntegrationBaseTest.php';
require_once 'FunctionalBaseTest.class.php';