<?PHP
$root = realpath(dirname(__FILE__) . '/../');
$paths = array();
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'intranet'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'lib'));
set_include_path(join(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());
chdir($root);
unset($root, $paths);


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
require_once 'include/clsBancoCustom.inc.php';

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

$urls = $argv;
unset($urls[0]);

$todayDate = date('Y-m-d');

foreach ($urls as $key => $clientUrl) {
  $tenantEnv = $clientUrl;

  if ($coreExt['Config']->hasEnviromentSection($tenantEnv))
    $coreExt['Config']->changeEnviroment($tenantEnv);

  $db = new clsBancoCustom(FALSE);
  $expirationDate = $db->CampoUnico(" SELECT data_expiracao_reserva_vaga FROM pmieducar.instituicao ");

  if($todayDate >= $expirationDate && !empty($expirationDate)){
    $db->consulta(" UPDATE candidato_reserva_vaga SET situacao = 'N' WHERE situacao = '' OR situacao IS NULL ");
  }
}