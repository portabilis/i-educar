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
 * @author Lucas D'Avila <lucas@lucasdavi.la>
 * @author Marcelo Cajueiro <marcelocajueiro@gmail.com>
 * @author Everton Muniz <munizeverton@gmail.com>
 * @author Eder Soares <edersoares@me.com>
 * @category  i-Educar
 * @license   GPL-2.0+
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'XML/RPC2/Client.php';
require_once 'lib/Portabilis/Report/ReportFactory.php';

class Portabilis_Report_ReportFactoryRemote extends Portabilis_Report_ReportFactory
{
    /**
     * Define as configurações dos relatórios.
     *
     * @param object $config
     *
     * @return void
     */
    public function setSettings($config)
    {
        $this->settings['url'] = $config->report->remote_factory->url;
        $this->settings['app_name'] = $config->report->remote_factory->this_app_name;
        $this->settings['username'] = $config->report->remote_factory->username;
        $this->settings['password'] = $config->report->remote_factory->password;
        $this->settings['logo_name'] = $config->report->remote_factory->logo_name;
    }

    /**
     * Renderiza o relatório.
     *
     * @param Portabilis_Report_ReportCore $report
     * @param array $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function dumps($report, $options = [])
    {
        $options = self::mergeOptions($options, [
            'add_logo_name_arg' => true,
            'encoding' => 'uncoded'
        ]);

        if ($options['add_logo_name_arg'] and !$this->settings['logo_name']) {
            throw new Exception('The option \'add_logo_name_arg\' is true, but no logo_name defined in configurations!');
        } elseif ($options['add_logo_name_arg']) {
            $report->addArg('logo_name', $this->settings['logo_name']);
        }

        $client = XML_RPC2_Client::create($this->settings['url']);

        $result = $client->build_report_jasper(
            $app_name = $this->settings['app_name'],
            $template_name = $report->templateName(),
            $username = $this->settings['username'],
            $password = $this->settings['password'],
            $args = $report->args
        );

        // Esta fábrica retorna o relatório encodado em base64.

        if ($options['encoding'] == 'base64') {
            $report = $result['report'];
        } elseif ($options['encoding'] == 'uncoded') {
            $report = base64_decode($result['report']);
        } else {
            throw new Exception("Encoding {$options['encoding']} not supported!");
        }

        return $report;
    }
}
