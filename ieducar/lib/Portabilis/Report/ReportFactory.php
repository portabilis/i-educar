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
 * @author    Lucas D'Avila <lucassdvl@gmail.com>
 * @author    Marcelo Cajueiro <marcelocajueiro@gmail.com>
 * @author    Lucas Schmoeller da Silva <lucasschmoellerdasilva@gmail.com>
 * @author    Eder Soares <edersoares@me.com>
 *
 * @category  i-Educar
 * @package   Portabilis
 *
 * @since     Arquivo disponível desde a versão 1.1.0
 *
 * @version   $Id$
 */

require_once 'lib/Portabilis/Array/Utils.php';

abstract class Portabilis_Report_ReportFactory
{
    /**
     * @var object
     */
    public $config;

    /**
     * @var array
     */
    public $settings;

    /**
     * Portabilis_Report_ReportFactory constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->config = $GLOBALS['coreExt']['Config'];
        $this->settings = [];

        $this->setSettings($this->config);
    }

    /**
     * Wrapper para Portabilis_Array_Utils::merge.
     *
     * @see Portabilis_Array_Utils::merge()
     *
     * @param array $options
     * @param array $defaultOptions
     *
     * @return array
     */
    protected static function mergeOptions($options, $defaultOptions)
    {
        return Portabilis_Array_Utils::merge($options, $defaultOptions);
    }

    /**
     * Define as configurações dos relatórios.
     *
     * @param object $config
     *
     * @return void
     */
    abstract public function setSettings($config);

    /**
     * Renderiza o relatório.
     *
     * @param Portabilis_Report_ReportCore $report
     * @param array                        $options
     *
     * @return mixed
     */
    abstract public function dumps($report, $options = []);
}
