<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
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
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'Portabilis/Model/Report/TipoBoletim.php';
require_once "App/Model/IedFinder.php";

class TurmaController extends ApiCoreController
{
  // validators

  protected function validatesTurmaId() {
    return  $this->validatesPresenceOf('id') &&
            $this->validatesExistenceOf('turma', $this->getRequest()->id);
  }

  // validations

  protected function canGet() {
    return $this->canAcceptRequest() &&
           $this->validatesTurmaId();
  }

  // api

  protected function getTipoBoletim() {
  	$tipo = App_Model_IedFinder::getTurma($codTurma = $this->getRequest()->id);
  	$tipo = $tipo['tipo_boletim'];

    $tiposBoletim = Portabilis_Model_Report_TipoBoletim;

    $tipos = array(null                                         => 'indefinido',
                   $tiposBoletim::BIMESTRAL                     => 'portabilis_boletim',
                   $tiposBoletim::TRIMESTRAL                    => 'portabilis_boletim_trimestral',
                   $tiposBoletim::TRIMESTRAL_CONCEITUAL         => 'portabilis_boletim_primeiro_ano_trimestral',
                   $tiposBoletim::SEMESTRAL                     => 'portabilis_boletim_semestral',
                   $tiposBoletim::SEMESTRAL_CONCEITUAL          => 'portabilis_boletim_conceitual_semestral',
                   $tiposBoletim::SEMESTRAL_EDUCACAO_INFANTIL   => 'portabilis_boletim_educ_infantil_semestral',
                   $tiposBoletim::PARECER_DESCRITIVO_COMPONENTE => 'portabilis_boletim_parecer',
                   $tiposBoletim::PARECER_DESCRITIVO_GERAL      => 'portabilis_boletim_parecer_geral');

    return array('tipo-boletim' => $tipos[$tipo]);
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'tipo-boletim'))
      $this->appendResponse($this->getTipoBoletim());
    else
      $this->notImplementedOperationError();
  }
}
