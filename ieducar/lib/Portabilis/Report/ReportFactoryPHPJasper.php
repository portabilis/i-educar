<?php
 // error_reporting(E_ERROR);
 // ini_set("display_errors", 1);
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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Report/ReportFactory.php';
require_once 'vendor/autoload.php';
use JasperPHP\JasperPHP;

/**
 * Portabilis_Report_ReportFactoryPHPJasper class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     ?
 * @version   @@package_version@@
 */

class Portabilis_Report_ReportFactoryPHPJasper extends Portabilis_Report_ReportFactory
{
  function setSettings($config) {
    $this->settings['db']             = $config->app->database;
    $this->settings['logo_file_name'] = $config->report->logo_file_name;
  }


  function getReportsPath() {
    $rootPath   = dirname(dirname(dirname(dirname(__FILE__))));
    $reportsPath = $rootPath . "/modules/Reports/ReportSources/";

    return $reportsPath;
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


    // Generate a random file name
    $outputFile = $this->getReportsPath() . time().'-'.mt_rand();;

    // Corrige parametros boleanos
    foreach ($report->args as $key => $value) {
      if (is_bool($value)) {
        $report->args[$key] = ($value ? 'true' : 'false');
      }
    }

    $builder = new JasperPHP();
    $return = $builder->process(
        $this->getReportsPath() . $report->templateName() . '.jasper',
        $outputFile,
        array("pdf"),
        $report->args,
        array(
          'driver' => 'postgres',
          'username' => $this->settings['db']->username,
          'host' => $this->settings['db']->hostname,
          'database' => $this->settings['db']->dbname,
          'port' => $this->settings['db']->port,
          'password' => $this->settings['db']->password,
        ),
        FALSE
    )->execute();

    $outputFile .= '.pdf';

    $this->showPDF($outputFile);
    $this->destroyPDF($outputFile);
  }

  function showPDF($file){
    $filename = 'relatorio.pdf';

    header('Content-type: application/pdf; charset=utf-8');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($file));
    // header('Accept-Ranges: bytes');

    readfile($file);
  }

  function destroyPDF($file){
    unlink($file);
  }
}
