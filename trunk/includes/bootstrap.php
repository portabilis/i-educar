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

/*
 * Define o ambiente de configuração desejado. Verifica se existe uma variável
 * de ambiente configurada ou define 'production' como padrão.
 */
if (getenv('CORE_EXT_CONFIGURATION_ENV')) {
  define('CORE_EXT_CONFIGURATION_ENV', getenv('CORE_EXT_CONFIGURATION_ENV'));
}
else {
  define('CORE_EXT_CONFIGURATION_ENV', 'production');
}

// por padrão busca uma configuração para o ambiente atual definido em CORE_EXT_CONFIGURATION_ENV
$configFile = realpath(dirname(__FILE__) . '/../') . '/configuration/' . CORE_EXT_CONFIGURATION_ENV . '.ini';

// caso não exista o ini para o ambiente atual, usa o arquivo padrão ieducar.ini
if (! file_exists($configFile))
  $configFile = realpath(dirname(__FILE__) . '/../') . '/configuration/ieducar.ini';

// Classe de configuração
require_once 'CoreExt/Config.class.php';
require_once 'CoreExt/Config/Ini.class.php';
require_once 'CoreExt/Locale.php';

// Array global de objetos de classes do pacote CoreExt
global $coreExt;
$coreExt = array();

// Localização para pt_BR
$locale = CoreExt_Locale::getInstance();
$locale->setCulture('pt_BR')->setLocale();

// Instancia objeto CoreExt_Configuration
$coreExt['Config'] = new CoreExt_Config_Ini($configFile, CORE_EXT_CONFIGURATION_ENV);
$coreExt['Locale'] = $locale;

// Timezone
date_default_timezone_set($coreExt['Config']->app->locale->timezone);

$tenantEnv = $_SERVER['HTTP_HOST'];

// tenta carregar as configurações da seção especifica do tenant,
// ex: ao acessar http://tenant.ieducar.com.br será carregado a seção tenant.ieducar.com.br caso exista
if ($coreExt['Config']->hasEnviromentSection($tenantEnv))
  $coreExt['Config']->changeEnviroment($tenantEnv);

/**
 * Altera o diretório da aplicação. chamadas a fopen() na aplicação não
 * verificam em que diretório está, assumindo sempre uma requisição a
 * intranet/.
 */
chdir($root . DS . 'intranet');
unset($root, $paths);
