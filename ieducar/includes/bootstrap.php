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
 * @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  CoreExt
 * @since    Arquivo disponível desde a versão 1.0.0
 * @version  $Id$
 */


/*
 * Verifica se o PHP instalado é maior ou igual a 5.2.0
 */
if (! version_compare('5.2.0', PHP_VERSION, '<=')) {
  die('O i-Educar requer o PHP na versão 5.2. A versão instalada de seu PHP (' . PHP_VERSION . ') não é suportada.');
}


/*
 * Altera o include_path, adicionando o caminho a CoreExt, tornando mais
 * simples o uso de require e include para as novas classes.
 */
$coreExt = realpath(dirname(__FILE__) . '/../') . '/lib';
set_include_path($coreExt . PATH_SEPARATOR . get_include_path());

/*
 * Define o ambiente de configuração desejado. Verifica se existe uma variável
 * de ambiente configurada ou define 'production' como padrão.
 */
defined('CORE_EXT_CONFIGURATION_ENV') ||
  define('CORE_EXT_CONFIGURATION_ENV', 'development');

// Arquivo de configuração INI
$configFile = realpath(dirname(__FILE__) . '/../') . '/configuration/ieducar.ini';

// Classe de configuração
require_once 'CoreExt/Config.class.php';
require_once 'CoreExt/Config/Ini.class.php';


// Array global de objetos de classes do pacote CoreExt
global $coreExt;
$coreExt = array();

// Instancia objeto CoreExt_Configuration
$coreExt['Config'] = new CoreExt_Config_Ini($configFile, CORE_EXT_CONFIGURATION_ENV);
