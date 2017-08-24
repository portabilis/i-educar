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
require_once 'lib/Portabilis/Utils/Database.php';

class ComponenteCurricularController extends ApiCoreController
{
  // search options

  protected function searchOptions() {
    return array('namespace' => 'modules', 'idAttr' => 'id');
  }

  // subescreve para pesquisar %query%, e nao query% como por padrão
  protected function sqlsForStringSearch() {
    return "select distinct id, nome as name from modules.componente_curricular
            where lower((nome)) like '%'||lower(($1))||'%' order by nome limit 15";
  }

  // subscreve formatResourceValue para não adicionar 'id -' a frente do resultado
  protected function formatResourceValue($resource) {
    return mb_strtoupper($resource['name'], 'UTF-8');
  }

  function getComponentesCurricularesSearch(){

    $sql = 'SELECT componente_curricular_id FROM modules.professor_turma_disciplina WHERE professor_turma_id = $1';

    $array = array();

    $resources = Portabilis_Utils_Database::fetchPreparedQuery($sql, array( 'params' => array($this->getRequest()->id) ));

    foreach ($resources as $reg) {
      $array[] = $reg['componente_curricular_id'];
    }

    return array('componentecurricular' => $array);
  }

  function canGetComponentesCurriculares(){
    return  $this->validatesPresenceOf('instituicao_id');
  }

  function getComponentesCurriculares(){
    if($this->canGetComponentesCurriculares()){

      $instituicaoId = $this->getRequest()->instituicao_id;
      $areaConhecimentoId = $this->getRequest()->area_conhecimento_id;

      $areaConhecimentoId ? $where = 'AND area_conhecimento_id = '. $areaConhecimentoId : '';
      
      $sql = 'SELECT componente_curricular.id, componente_curricular.nome, area_conhecimento_id, area_conhecimento.nome AS nome_area
                FROM modules.componente_curricular
               INNER JOIN modules.area_conhecimento ON (area_conhecimento.id = componente_curricular.area_conhecimento_id)
                WHERE componente_curricular.instituicao_id = $1
                ' . $where . '
                ORDER BY nome ';
      $disciplinas = $this->fetchPreparedQuery($sql, array($instituicaoId));

      $attrs = array('id', 'nome', 'area_conhecimento_id', 'nome_area');
      $disciplinas = Portabilis_Array_Utils::filterSet($disciplinas, $attrs);

      foreach ($disciplinas as &$disciplina){
        $disciplina['nome'] = Portabilis_String_Utils::toUtf8($disciplina['nome']);
      }

      return array('disciplinas' => $disciplinas);
    }
  }

function getComponentesCurricularesPorSerie(){
    if($this->canGetComponentesCurriculares()){

      $instituicaoId = $this->getRequest()->instituicao_id;
      $serieId       = $this->getRequest()->serie_id;
      
      $sql = 'SELECT componente_curricular.id, componente_curricular.nome, carga_horaria::int, area_conhecimento_id, area_conhecimento.nome AS nome_area
                FROM modules.componente_curricular
               INNER JOIN modules.componente_curricular_ano_escolar ON (componente_curricular_ano_escolar.componente_curricular_id = componente_curricular.id)
               INNER JOIN modules.area_conhecimento ON (area_conhecimento.id = componente_curricular.area_conhecimento_id)
                WHERE componente_curricular.instituicao_id = $1
                  AND ano_escolar_id = ' . $serieId . '
                ORDER BY nome ';
      $disciplinas = $this->fetchPreparedQuery($sql, array($instituicaoId));

      $attrs = array('id', 'nome', 'carga_horaria', 'area_conhecimento_id', 'nome_area');
      $disciplinas = Portabilis_Array_Utils::filterSet($disciplinas, $attrs);

      return array('disciplinas' => $disciplinas);
    }
  }

    protected function getComponentesCurricularesForMultipleSearch() {
    if ($this->canGetComponentesCurriculares()) {
      $turmaId       = $this->getRequest()->turma_id;
      $ano           = $this->getRequest()->ano;

      $sql = "select cc.id,
                     (cc.nome) as nome
                from pmieducar.turma,
                     modules.componente_curricular_turma as cct,
                     modules.componente_curricular as cc,
                     modules.area_conhecimento as ac,
                     pmieducar.escola_ano_letivo as al
               where turma.cod_turma = $1 and
                     cct.turma_id = turma.cod_turma and
                     cct.escola_id = turma.ref_ref_cod_escola and
                     cct.componente_curricular_id = cc.id and al.ano = $2 and
                     turma.ref_ref_cod_escola = al.ref_cod_escola and
                     cc.area_conhecimento_id = ac.id
               order by ac.secao, ac.nome, cc.ordenamento, cc.nome";


      $componentesCurriculares = $this->fetchPreparedQuery($sql, array($turmaId, $ano));

      if(count($componentesCurriculares) < 1){
        $sql = "select cc.id,
                       (cc.nome) as nome
                  from pmieducar.turma as t,
                       pmieducar.escola_serie_disciplina as esd,
                       modules.componente_curricular as cc,
                       modules.area_conhecimento as ac,
                       pmieducar.escola_ano_letivo as al
                 where t.cod_turma = $1 and
                       esd.ref_ref_cod_escola = t.ref_ref_cod_escola and
                       esd.ref_ref_cod_serie = t.ref_ref_cod_serie and
                       esd.ref_cod_disciplina = cc.id and al.ano = $2 and
                       esd.ref_ref_cod_escola = al.ref_cod_escola and t.ativo = 1 and
                       esd.ativo = 1 and
                       al.ativo = 1 and
                       cc.area_conhecimento_id = ac.id
                 order by ac.secao, ac.nome, cc.ordenamento, cc.nome";

        $componentesCurriculares = $this->fetchPreparedQuery($sql, array($turmaId, $ano));
      }

      $componentesCurriculares = Portabilis_Array_Utils::setAsIdValue($componentesCurriculares, 'id', 'nome');

      return array('options' => $componentesCurriculares);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'componente_curricular-search'))
      $this->appendResponse($this->search());
    elseif ($this->isRequestFor('get', 'componentecurricular-search'))
      $this->appendResponse($this->getComponentesCurricularesSearch());
    elseif ($this->isRequestFor('get', 'componentes-curriculares'))
      $this->appendResponse($this->getComponentesCurriculares());
    elseif ($this->isRequestFor('get', 'componentes-curriculares-serie'))
      $this->appendResponse($this->getComponentesCurricularesPorSerie());
    elseif($this->isRequestFor('get', 'componentes-curriculares-for-multiple-search'))
      $this->appendResponse($this->getComponentesCurricularesForMultipleSearch());
    else
      $this->notImplementedOperationError();
  }
}
