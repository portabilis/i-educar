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

require_once 'lib/Portabilis/Report/ReportFactory.php';

require_once 'relatorios/phpjasperxml/class/fpdf/fpdf.php';
require_once 'relatorios/phpjasperxml/class/PHPJasperXML.inc';

//set_include_path(get_include_path() . PATH_SEPARATOR . 'include/portabilis/libs');
//require_once 'include/portabilis/libs/XML/RPC2/Client.php';

/**
 * Portabilis_Report_ReportFactoryPHPJasperXML class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */

class Portabilis_Report_ReportFactoryPHPJasperXML extends Portabilis_Report_ReportFactory
{
  function setSettings($config) {
    $this->settings['db']             = $config->app->database;
    $this->settings['logo_file_name'] = $config->report->logo_file_name;
  }


  function loadReportSource($templateName) {
    $rootPath   = dirname(dirname(dirname(dirname(__FILE__))));
    $fileName = "$templateName.jrxml";
    $filePath = $rootPath . "/modules/Reports/ReportSources/$fileName";

    if (! file_exists($filePath))
      throw new CoreExt_Exception("Report source '$fileName' not found in path '$filePath'");

    return simplexml_load_file($filePath);
  }


  function logoPath() {
    if (! $this->settings['logo_file_name'])
      throw new Exception("No report.logo_file_name defined in configurations!");

    $rootPath = dirname(dirname(dirname(dirname(__FILE__))));
    $filePath = $rootPath . "/modules/Reports/ReportLogos/{$this->settings['logo_file_name']}";

    if (! file_exists($filePath))
      throw new CoreExt_Exception("Report logo '{$this->settings['logo_file_name']}' not found in path '$filePath'");

    return $filePath;
  }


  function dumps($report, $options = array()) {
    $defaultOptions          = array('add_logo_arg' => true);
    $options                 = self::mergeOptions($options, $defaultOptions);

    if ($options['add_logo_arg'])
      $report->addArg('logo', $this->logoPath());

    $xml                     = $this->loadReportSource($report->templateName());

    $builder                 = new PHPJasperXML();
    $builder->debugsql       = false;
    $builder->arrayParameter = $report->args;

    $builder->xml_dismantle($xml);

    $builder->transferDBtoArray($this->settings['db']->hostname,
                                $this->settings['db']->username,
                                $this->settings['db']->password,
                                $this->settings['db']->dbname,
                                $this->settings['db']->port);

    // I: standard output, D: Download file, F: file
    $builder->outpage('I');
  }
}