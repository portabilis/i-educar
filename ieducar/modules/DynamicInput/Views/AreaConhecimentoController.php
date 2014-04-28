<?php

// error_reporting(E_ALL);
// ini_set("display_errors", 1);

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'App/Model/IedFinder.php';

/**
 * AreaConhecimento class.
 *
 * @author      Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão ??
 * @version     @@package_version@@
 */
class AreaConhecimentoController extends ApiCoreController{

  protected function getAreasConhecimento() {

     $instituicaoId = $this->getRequest()->instituicao_id;
     $turmaId       = $this->getRequest()->turma_id;
     $sql    = 'SELECT DISTINCT
                       ac.id as id,
                       to_ascii(lower(ac.nome)) as nome
                  FROM pmieducar.turma AS t, 
                       pmieducar.escola_serie_disciplina AS esd, 
                       modules.componente_curricular AS cc, 
                       modules.area_conhecimento AS ac
                 WHERE t.cod_turma = $2
                   AND esd.ref_ref_cod_escola = t.ref_ref_cod_escola 
                   AND esd.ref_ref_cod_serie = t.ref_ref_cod_serie 
                   AND esd.ref_cod_disciplina = cc.id
                   AND t.ativo = 1 
                   AND esd.ativo = 1 
                   AND cc.area_conhecimento_id = ac.id
                   AND ac.instituicao_id = $1
                 ORDER BY to_ascii(lower(ac.nome)), ac.id';

    $areasConhecimento = array();
    $paramsSql = array($instituicaoId,$turmaId);
    $areasConhecimento = $this->fetchPreparedQuery($sql, $paramsSql);
    $options = array();

    foreach ($areasConhecimento as $areaConhecimento){
      $options['__' . $areaConhecimento['id']] = $this->toUtf8($areaConhecimento['nome']);
    }
    return array('options' => $options);

  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'area_conhecimento'))
      $this->appendResponse($this->getAreasConhecimento());
    else
      $this->notImplementedOperationError();
  }
}