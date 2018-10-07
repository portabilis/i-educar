<?php
namespace Ieducar\Portabilis\Utils;

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
 * @author    Éber Freitas Dias <eber.freitas@gmail.com>
 * @category  i-Educar
 * @license   GPL-2.0+
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'include/pmieducar/clsPmieducarConfiguracoesGerais.inc.php';

class CustomLabel
{
    protected static $instance;

    protected $defaults;
    protected $custom;

    public function __construct($defaultsPath)
    {
        $raw = @file_get_contents($defaultsPath);

        if ($raw === false) {
            throw new Exception('Não foi possível encontrar o arquivo de chaves padrão no caminho "' . $defaultsPath . '"');
        }

        $this->defaults = json_decode($raw, true);
        $this->custom = $this->queryCustom();
    }

    public function customize($key)
    {
        if (!empty($this->custom[$key])) {
            return $this->custom[$key];
        }

        if (!empty($this->defaults[$key])) {
            return $this->defaults[$key];
        }

        return $key;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function getCustom()
    {
        return $this->custom;
    }

    protected function queryCustom()
    {
        $configs = new clsPmieducarConfiguracoesGerais();
        $detalhe = $configs->detalhe();

        return $detalhe['custom_labels'];
    }

    public static function getInstance($path)
    {
        if (is_null(self::$instance)) {
            self::$instance = new CustomLabel($path);
        }

        return self::$instance;
    }
}

function _cl($key)
{
    $path = PROJECT_ROOT . DS . 'configuration' . DS . 'custom_labels.json';

    return CustomLabel::getInstance($path)->customize($key);
}
