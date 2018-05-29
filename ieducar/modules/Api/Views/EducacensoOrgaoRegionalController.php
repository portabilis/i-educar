<?php

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';

/**
 * Class EducacensoOrgaoRegionalController
 * @deprecated Essa versão da API pública será descontinuada
 */
class EducacensoOrgaoRegionalController extends ApiCoreController
{

  protected function getOrgaosRegionais() {
    $consulta = 'SELECT codigo
                    FROM modules.educacenso_orgao_regional
                    WHERE sigla_uf = $1';
    $orgaos = $this->fetchPreparedQuery($consulta, array($this->getRequest()->sigla_uf));
    $attrs = array('codigo');
    $orgaos = Portabilis_Array_Utils::filterSet($orgaos, $attrs);
    return array('orgaos' => $orgaos);
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'orgaos_regionais'))
      $this->appendResponse($this->getOrgaosRegionais());
    else
      $this->notImplementedOperationError();
  }
}
