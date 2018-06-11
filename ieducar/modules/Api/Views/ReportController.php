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
require_once "Reports/Reports/BoletimReport.php";
require_once "Reports/Reports/BoletimProfessorReport.php";

/**
 * Class ReportController
 * @deprecated Essa versão da API pública será descontinuada
 */
class ReportController extends ApiCoreController
{

  // validations

  protected function canGetBoletim() {
    return $this->validatesId('escola') &&
           $this->validatesId('matricula');
  }

  protected function canGetBoletimProfessor() {
    return $this->validatesId('instituicao') &&
           $this->validatesPresenceOf('ano') &&
           $this->validatesId('escola') &&
           $this->validatesId('serie') &&
           $this->validatesId('turma') &&
           $this->validatesPresenceOf('componente_curricular_id');
  }


  // load

  protected function loadDadosForMatricula($matriculaId){
    $sql            = "select cod_matricula as id, ref_cod_aluno as aluno_id, matricula.ano,
                       escola.ref_cod_instituicao as instituicao_id, matricula.ref_ref_cod_escola
                       as escola_id, matricula.ref_cod_curso as curso_id, matricula.ref_ref_cod_serie
                       as serie_id, matricula_turma.ref_cod_turma as turma_id from
                       pmieducar.matricula_turma, pmieducar.matricula, pmieducar.escola where escola.cod_escola =
                       matricula.ref_ref_cod_escola and ref_cod_matricula = cod_matricula and ref_cod_matricula =
                       $1 and matricula.ativo = matricula_turma.ativo and matricula_turma.ativo = 1 order by
                       matricula_turma.sequencial limit 1";

    $dadosMatricula = $this->fetchPreparedQuery($sql, $matriculaId, false, 'first-row');

    $attrs          = array('id', 'aluno_id', 'ano', 'instituicao_id', 'escola_id',
                            'curso_id', 'serie_id', 'turma_id');

    return Portabilis_Array_Utils::filter($dadosMatricula, $attrs);
  }

  // api

  protected function getBoletim() {
   if ($this->canGetBoletim()) {
      $dadosMatricula = $this->loadDadosForMatricula($this->getRequest()->matricula_id);

      $boletimReport = new BoletimReport();

      $boletimReport->addArg('matricula',   (int)$dadosMatricula['id']);
      $boletimReport->addArg('ano',         (int)$dadosMatricula['ano']);
      $boletimReport->addArg('instituicao', (int)$dadosMatricula['instituicao_id']);
      $boletimReport->addArg('escola',      (int)$dadosMatricula['escola_id']);
      $boletimReport->addArg('curso',       (int)$dadosMatricula['curso_id']);
      $boletimReport->addArg('serie',       (int)$dadosMatricula['serie_id']);
      $boletimReport->addArg('turma',       (int)$dadosMatricula['turma_id']);
      $boletimReport->addArg('situacao_matricula', 10);

      if (CORE_EXT_CONFIGURATION_ENV == "production") {
        $boletimReport->addArg('SUBREPORT_DIR', "/sites_media_root/services/reports/jasper/");
      } else if ($GLOBALS['coreExt']['Config']->app->database->dbname == 'test' || $GLOBALS['coreExt']['Config']->app->database->dbname == 'desenvolvimento') {
        $boletimReport->addArg('SUBREPORT_DIR', "/sites_media_root/services-test/reports/jasper/");
      } else {
        $boletimReport->addArg('SUBREPORT_DIR', "modules/Reports/ReportSources/Portabilis/");
      }

      $encoding     = 'base64';

      $dumpsOptions = array('options' => array('encoding' => $encoding));
      $encoded      = $boletimReport->dumps($dumpsOptions);

      return array('matricula_id' => $this->getRequest()->matricula_id,
                   'encoding'     => $encoding,
                   'encoded'      => $encoded);
    }
  }

  protected function getBoletimProfessor() {
   if ($this->canGetBoletimProfessor()) {
      $boletimProfessorReport = new BoletimProfessorReport();

      $boletimProfessorReport->addArg('ano',   (int)$this->getRequest()->ano);
      $boletimProfessorReport->addArg('instituicao',   (int)$this->getRequest()->instituicao_id);
      $boletimProfessorReport->addArg('escola',   (int)$this->getRequest()->escola_id);
      $boletimProfessorReport->addArg('curso',   (int)$this->getRequest()->curso_id);
      $boletimProfessorReport->addArg('serie',   (int)$this->getRequest()->serie_id);
      $boletimProfessorReport->addArg('turma',   (int)$this->getRequest()->turma_id);
      $boletimProfessorReport->addArg('professor',   Portabilis_String_Utils::toLatin1($this->getRequest()->professor));
      $boletimProfessorReport->addArg('disciplina',   (int)$this->getRequest()->componente_curricular_id);
      $boletimProfessorReport->addArg('orientacao', 2);
      $boletimProfessorReport->addArg('situacao', 0);

      $configuracoes = new clsPmieducarConfiguracoesGerais();
      $configuracoes = $configuracoes->detalhe();

      $modelo = $configuracoes["modelo_boletim_professor"];

      $boletimProfessorReport->addArg('modelo', $modelo);
      $boletimProfessorReport->addArg('linha', 0);

      if (CORE_EXT_CONFIGURATION_ENV == "production") {
        $boletimProfessorReport->addArg('SUBREPORT_DIR', "/sites_media_root/services/reports/jasper/");
      } else if ($GLOBALS['coreExt']['Config']->app->database->dbname == 'test' || $GLOBALS['coreExt']['Config']->app->database->dbname == 'desenvolvimento') {
        $boletimProfessorReport->addArg('SUBREPORT_DIR', "/sites_media_root/services-test/reports/jasper/");
      } else {
        $boletimProfessorReport->addArg('SUBREPORT_DIR', "modules/Reports/ReportSources/Portabilis/");
      }

      $encoding     = 'base64';

      $dumpsOptions = array('options' => array('encoding' => $encoding));
      $encoded      = $boletimProfessorReport->dumps($dumpsOptions);

      return array('encoding'     => $encoding,
                   'encoded'      => $encoded);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'boletim'))
      $this->appendResponse($this->getBoletim());
    elseif ($this->isRequestFor('get', 'boletim-professor'))
      $this->appendResponse($this->getBoletimProfessor());
    else
      $this->notImplementedOperationError();
  }
}
