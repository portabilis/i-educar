<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';

/**
 * Portabilis_View_Helper_DynamicInput_AreaConhecimento class.
 *
 * @author    Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão ?
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicInput_AreaConhecimento extends Portabilis_View_Helper_DynamicInput_CoreSelect {

  protected function inputName() {
    return 'area_conhecimento_id';
  }

  protected function inputOptions($options) {

    $resources     = $options['resources'];
    
    // echo "<pre>";var_dump($resources);echo"</pre>";
    // $instituicaoId = $this->getInstituicaoId($options['instituicaoId']);
    
    // $resources = App_Model_IedFinder::getAreasConhecimento($instituicaoId);

    return $this->insertOption(null, "Todas", $resources);
  }

  public function areaConhecimento($options = array()) {
    parent::select($options);
  }
}