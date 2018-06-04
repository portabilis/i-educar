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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
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
require_once 'include/modules/clsModulesComponenteCurricularAnoEscolar.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaSerieDisciplina.inc.php';
require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';

/**
 * Class ComponentesSerieController
 * @deprecated Essa versão da API pública será descontinuada
 */
class ComponentesSerieController extends ApiCoreController
{

    function atualizaComponentesDaSerie(){
        $serieId     = $this->getRequest()->serie_id;
        $componentes = json_decode($this->getRequest()->componentes);
        $arrayComponentes = array();

        foreach ($componentes as $key => $componente) {
            $arrayComponentes[$key]['id'] = $componente->id;
            $arrayComponentes[$key]['carga_horaria'] = $componente->carga_horaria;
            $arrayComponentes[$key]['tipo_nota'] = $componente->tipo_nota;
        }

        $obj = new clsModulesComponenteCurricularAnoEscolar(NULL, $serieId, NULL, NULL,  $arrayComponentes);

        $updateInfo = $obj->updateInfo();            
        $componentesAtualizados = $updateInfo['update'];
        $componentesInseridos   = $updateInfo['insert'];
        $componentesExcluidos   = $updateInfo['delete'];
        
        if ($obj->atualizaComponentesDaSerie()) {
        
            if ($componentesExcluidos) {
              $this->atualizaExclusoesDeComponentes($serieId, $componentesExcluidos);
            }

            return array('update' => $componentesAtualizados,
                         'insert' => $componentesInseridos,
                         'delete' => $componentesExcluidos);
        }
        return array('msgErro' => 'Erro ao alterar componentes da série.');
    }

    function atualizaEscolasSerieDisciplina(){
        $serieId     = $this->getRequest()->serie_id;
        $componentes = json_decode($this->getRequest()->componentes);
        $arrayComponentes = array();
        
        foreach ($componentes as $key => $componente) {
            $arrayComponentes[$key]['id'] = $componente->id;
            $arrayComponentes[$key]['carga_horaria'] = $componente->carga_horaria;
        }

        $this->replicaComponentesAdicionadosNasEscolas($serieId, $arrayComponentes);
    }

    function replicaComponentesAdicionadosNasEscolas($serieId, $componentes){
        $escolas = $this->getEscolasDaSerie($serieId);
        $turmas  = $this->getTurmasDaSerieNoAnoLetivoAtual($serieId);
        if($escolas && $componentes){
            foreach ($escolas as $escola) {
                foreach ($componentes as $componente){
                    $objEscolaSerieDisciplina = new clsPmieducarEscolaSerieDisciplina($serieId, $escola['ref_cod_escola'], $componente['id']);
                    if(!$objEscolaSerieDisciplina->cadastra()){
                        return false;
                    }
                }
            }
        }
    }

    function getUltimoAnoLetivoAberto(){
        $objEscolaAnoLetivo = new clsPmieducarEscolaAnoLetivo();
        $ultimoAnoLetivoAberto = $objEscolaAnoLetivo->getUltimoAnoLetivoAberto();
        return $ultimoAnoLetivoAberto;
    }

    function getEscolasDaSerie($serieId){
        $objEscolaSerie = new clsPmieducarEscolaSerie();
        $escolasDaSerie = $objEscolaSerie->lista(NULL, $serieId);
        if($escolasDaSerie){
            return $escolasDaSerie;
        }
        return false;
    }

    function getTurmasDaSerieNoAnoLetivoAtual($serieId){
        $objTurmas     = new clsPmieducarTurma();
        $turmasDaSerie = $objTurmas->lista(NULL, NULL, NULL, $serieId, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $this->getUltimoAnoLetivoAberto());
        if($turmasDaSerie){
            return $turmasDaSerie;
        }
        return false;
    }

    function excluiEscolaSerieDisciplina($escolaId, $serieId, $disciplinaId){
        $objEscolaSerieDisiciplina = new clsPmieducarEscolaSerieDisciplina($serieId, $escolaId, $disciplinaId);
        if($objEscolaSerieDisiciplina->excluir()){
            return true;
        }
        return false;
    }

    function excluiComponenteDaTurma($componenteId, $turmaId){
        $mapper = new ComponenteCurricular_Model_TurmaDataMapper();
        $where = array('componente_curricular_id' => $componenteId, 'turma_id' => $turmaId);
        $componente = $mapper->findAll(array('componente_curricular_id', 'turma_id'), $where, array(), false);
        
        if($componente && $mapper->delete($componente[0])){
            return true;
        }
        return false;
    }

    function atualizaExclusoesDeComponentes($serieId, $componentes){
        $escolas = $this->getEscolasDaSerie($serieId);
        $turmas  = $this->getTurmasDaSerieNoAnoLetivoAtual($serieId);
        if($escolas && $componentes){
            foreach ($escolas as $escola) {
                foreach ($componentes as $componente){
                    $this->excluiEscolaSerieDisciplina($escola['ref_cod_escola'], $serieId, $componente);
                }
            }
        }

        if($turmas && $componentes){
            foreach ($turmas as $turma) {
                foreach ($componentes as $componente){
                $this->excluiComponenteDaTurma($componente, $turma['cod_turma']);
                }
            }
        }
    }

    function excluiComponentesSerie(){
        $serieId = $this->getRequest()->serie_id;
        $obj     = new clsModulesComponenteCurricularAnoEscolar(NULL, $serieId);
        if ($obj->exclui()) {
            $this->excluiTodosComponenteDaTurma($serieId);
            $this->excluiTodasDisciplinasEscolaSerie($serieId);
        }
    }

    function excluiTodasDisciplinasEscolaSerie($serieId){
        $escolas = $this->getEscolasDaSerie($serieId);
        if($escolas){
            foreach ($escolas as $escola) {
                $objEscolaSerieDisciplina = new clsPmieducarEscolaSerieDisciplina($serieId, $escola['ref_cod_escola']);
                $objEscolaSerieDisciplina->excluirTodos();
            }
        }
    }

    function excluiTodosComponenteDaTurma($serieId){
        $turmas  = $this->getTurmasDaSerieNoAnoLetivoAtual($serieId);
        $mapper  = new ComponenteCurricular_Model_TurmaDataMapper();
        if($turmas){
            foreach ($turmas as $turma) {
                $where = array('turma_id' => $turma['cod_turma']);
                $componentes = $mapper->findAll(array('componente_curricular_id', 'turma_id'), $where, array(), false);
            }
        }
        if($componentes){
            foreach ($componentes as $componente) {
                $mapper->delete($componente);
            }
        }
    }

    function existeDependencia(){
        $serie = $this->getRequest()->serie_id;
        $escola = $this->getRequest()->escola_id;
        $disciplinas = $this->getRequest()->disciplinas;
        $disciplinas = explode(',', $disciplinas);

        $obj = new clsPmieducarEscolaSerieDisciplina($serie, $escola, NULL, 1);

        return array('existe_dependencia' => $obj->existeDependencia($disciplinas));
    }

    function existeDispensa(){
        $serie = $this->getRequest()->serie_id;
        $escola = $this->getRequest()->escola_id;
        $disciplinas = $this->getRequest()->disciplinas;
        $disciplinas = explode(',', $disciplinas);

        $obj = new clsPmieducarEscolaSerieDisciplina($serie, $escola, NULL, 1);

        return array('existe_dispensa' => $obj->existeDispensa($disciplinas));
    }

  public function Gerar() {
    if ($this->isRequestFor('post', 'atualiza-componentes-serie'))
      $this->appendResponse($this->atualizaComponentesDaSerie());
    elseif($this->isRequestFor('post', 'replica-componentes-adicionados-escolas'))
      $this->appendResponse($this->atualizaEscolasSerieDisciplina());
    elseif($this->isRequestFor('post', 'exclui-componentes-serie'))
        $this->appendResponse($this->excluiComponentesSerie());
    elseif($this->isRequestFor('get', 'existe-dispensa'))
        $this->appendResponse($this->existeDispensa());
    elseif($this->isRequestFor('get', 'existe-dependencia'))
        $this->appendResponse($this->existeDependencia());
    else
      $this->notImplementedOperationError();
  }
}
