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
 * @author     Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category   i-Educar
 * @license    @@license@@
 * @package    Reports
 * @subpackage Modules
 * @since      Arquivo disponível desde a versão 1.1.0
 * @version    $Id$
 */


/**
 * BoletimReport class.
 *
 * @author     Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category   i-Educar
 * @license    @@license@@
 * @package    Reports
 * @subpackage Modules
 * @since      Classe disponível desde a versão 1.1.0
 * @version    @@package_version@@
 */

require_once "lib/Portabilis/Report/ReportCore.php";
require_once 'Portabilis/Model/Report/TipoBoletim.php';
require_once "App/Model/IedFinder.php";

class BoletimReport extends Portabilis_Report_ReportCore
{

  function templateName() {
  	$flagTipoBoletimTurma = App_Model_IedFinder::getTurma($codTurma = $this->args['turma']);
  	$flagTipoBoletimTurma = $flagTipoBoletimTurma['tipo_boletim'];

  	if (empty($flagTipoBoletimTurma)) {
  		throw new Exception(
        Portabilis_String_Utils::toLatin1("Não foi definido o tipo de boletim no cadastro de turmas.")
      );
    }

    $tiposBoletim = Portabilis_Model_Report_TipoBoletim;

    $templates = array($tiposBoletim::BIMESTRAL                     => 'portabilis_boletim',
                       $tiposBoletim::TRIMESTRAL                    => 'portabilis_boletim_trimestral',
                       $tiposBoletim::TRIMESTRAL_CONCEITUAL         => 'portabilis_boletim_primeiro_ano_trimestral',
                       $tiposBoletim::SEMESTRAL                     => 'portabilis_boletim_semestral',
                       $tiposBoletim::SEMESTRAL_CONCEITUAL          => 'portabilis_boletim_conceitual_semestral',
                       $tiposBoletim::SEMESTRAL_EDUCACAO_INFANTIL   => 'portabilis_boletim_educ_infantil_semestral',
                       $tiposBoletim::PARECER_DESCRITIVO_COMPONENTE => 'portabilis_boletim_parecer',
                       $tiposBoletim::PARECER_DESCRITIVO_GERAL      => 'portabilis_boletim_parecer_geral');

    $template = is_null($flagTipoBoletimTurma) ? '' : $templates[$flagTipoBoletimTurma];

  	if (empty($template)) {
  		throw new Exception(
        Portabilis_String_Utils::toLatin1("Não foi possivel recuperar nome do template para o boletim.")
      );
    }

    return $template;
  }

	function requiredArgs() {
		$this->addRequiredArg('ano');
		$this->addRequiredArg('instituicao');
		$this->addRequiredArg('escola');
		$this->addRequiredArg('curso');
		$this->addRequiredArg('serie');
		$this->addRequiredArg('turma');
	}
}

?>
