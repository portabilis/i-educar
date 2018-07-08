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
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

/*
 * Verifica se o PHP instalado é maior ou igual a 5.2.0
 */
if (! version_compare('5.2.0', PHP_VERSION, '<=')) {
  die('O i-Educar requer o PHP na versão 5.2. A versão instalada de seu PHP (' . PHP_VERSION . ') não é suportada.');
}

/**
 * Alias para DIRECTORY_SEPARATOR
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Diretório raiz do projeto.
 */
$root = realpath(dirname(__FILE__) . '/../');
define('PROJECT_ROOT', $root);

/**
 * Diretório raiz da aplicação (intranet/).
 */
define('APP_ROOT', $root . DS . 'intranet');

/*
 * Altera o include_path, adicionando o caminho a CoreExt, tornando mais
 * simples o uso de require e include para as novas classes.
 */
$paths = array();
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'intranet'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'lib'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'modules'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, '.'));

// Configura o include_path.
set_include_path(join(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'CoreExt/Config.class.php';
require_once 'CoreExt/Config/Env.php';
require_once 'CoreExt/Locale.php';

// Array global de objetos de classes do pacote CoreExt
global $coreExt;
$coreExt = array();

// Localização para pt_BR
$locale = CoreExt_Locale::getInstance();
$locale->setCulture('pt_BR')->setLocale();

$tenantEnvFile = $_SERVER['HTTP_HOST'] . '.env';

$ambientesDesenvolvimento = [
    'local',
    'staging',
];

$tenants = [];
if (file_exists(__DIR__ . '/../configuration/' . $tenantEnvFile)) {
    $tenants[] = new Dotenv\Dotenv(__DIR__ . '/../configuration/', $tenantEnvFile);
}

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../configuration/');

$config = new \iEducar\Config\Env($dotenv, $tenants);

$coreExt['Config'] = $config;
$coreExt['Locale'] = $locale;

if (!in_array(getenv('ambiente'), $ambientesDesenvolvimento) && !file_exists(__DIR__ . '/../configuration/' . $tenantEnvFile)){
    header("Location: /404.html");
}

// Timezone
date_default_timezone_set($coreExt['Config']->app->locale->timezone);

/**
 * Altera o diretório da aplicação. chamadas a fopen() na aplicação não
 * verificam em que diretório está, assumindo sempre uma requisição a
 * intranet/.
 */
chdir($root . DS . 'intranet');
unset($root, $paths);

// função pra ajudar no debug
function debug($var) {
    $backtrace = debug_backtrace();
    $template = '<div><strong>%s</strong> linha <strong>%d</strong></div>';
    echo sprintf($template, $backtrace[0]['file'], $backtrace[0]['line']);
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
