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
require_once 'include/portabilis/libs/XML/RPC2/Client.php';

/**
 * CoreExt_Session class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */

class Portabilis_Report_ReportFactoryRemote extends Portabilis_Report_ReportFactory
{
	function setSettings($config) {
	  $this->settings['url'] 								 = $this->config->remote_factory->url;
	  $this->settings['app_name']					 	 = $this->config->remote_factory->this_app_name;
		$this->settings['username'] 					 = $this->config->remote_factory->username;
	  $this->settings['password'] 					 = $this->config->remote_factory->password;
	  $this->settings['show_exceptions_msg'] = $this->config->remote_factory->show_exceptions_msg;
	  $this->settings['logo_name']           = $this->config->remote_factory->logo_name;
	}

  function dumps($report, $options = array()) {
    $defaultOptions = array('add_arg_logo_name' => true, 'encoding' => 'uncoded');
    $options        = self::mergeOptions($options, $defaultOptions);

    // logo helper

    if ($options['add_arg_logo_name'] and ! $this->settings['logo_name'])
    	throw new Exception("The option 'add_arg_logo_name' is true, but no logo_name defined in configurations");
		elseif ($options['add_arg_logo_name'])
      $report->addArg('logo_name', $this->settings['logo_name']);


    // api call

    $client = XML_RPC2_Client::create($this->settings['url']);

    $result = $client->build_report_jasper($app_name      = $this->settings['app_name'],
                                           $template_name = $report->templateName,
                                           $username      = $this->settings['username'],
                                           $password      = $this->settings['password'],
                                           $args          = $report->args);


    // report encoding

    // the remote report factory returns report encoded to base64.
    if ($options['encoding'] == 'base64')
      $report = $result['report'];

    elseif($options['encoding'] == 'uncoded')
      $report = base64_decode($result['report']);

    else
      throw new Exception("Encoding {$options['encoding']} not implemented!");

    return $report;
  }
}

?>