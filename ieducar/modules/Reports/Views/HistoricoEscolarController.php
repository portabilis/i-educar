<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * @author      Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Reports
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once "lib/Portabilis/Controller/ReportCoreController.php";
require_once "Reports/Reports/HistoricoEscolarReport.php";

/**
 * HistoricoEscolarController class.
 *
 * @author      Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Reports
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class HistoricoEscolarController extends Portabilis_Controller_ReportCoreController
{

  protected $_titulo = 'Hist&oacute;rico Escolar';

	function form() {
    $this->inputsHelper()->dynamic(array('instituicao', 'escola'));
    $this->inputsHelper()->simpleSearchAluno();

    $resources = array(1 => '9 anos',
                       2 => '8 anos',
                       3 => 'S&eacute;rie/Anos');

    $options = array('label' => 'Modelo', 'resources' => $resources, 'value' => 1);
		$this->inputsHelper()->select('modelo', $options);

    // carrega javascript HistoricoEscolar.js para listar alunos de todas as escolas
    $this->loadResourceAssets($this->getDispatcher());
  }

	function report() {
	  return new HistoricoEscolarReport();
	}

  function beforeValidation() {
    $this->report->addArg('instituicao',  (int)$this->getRequest()->ref_cod_instituicao);
    $this->report->addArg('escola',       (int)$this->getRequest()->ref_cod_escola);
    $this->report->addArg('aluno',        (int)$this->getRequest()->aluno_id);
    $this->report->addArg('aluno',        (int)$this->getRequest()->aluno_id);
    $this->report->addArg('modelo',       (int)$this->getRequest()->modelo);
  }
}

?>
