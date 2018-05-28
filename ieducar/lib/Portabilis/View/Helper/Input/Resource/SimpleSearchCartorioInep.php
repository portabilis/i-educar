<?php

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_SimpleSearchCartorioInep extends Portabilis_View_Helper_Input_SimpleSearch {

    protected function resourceValue($id)
    {
        if ($id) {
            $sql = "SELECT id_cartorio || ' - ' || descricao AS nome
                    FROM cadastro.codigo_cartorio_inep
                    WHERE id = $1";

            $options = array('params' => $id, 'return_only' => 'first-row');
            $curso_superior = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
            $nome = $curso_superior['nome'];

            return Portabilis_String_Utils::toLatin1($nome, array('transform' => true, 'escape' => false));
        }
    }

    public function simpleSearchCartorioInep($attrName, $options = array())
    {
        $defaultOptions = array(
            'objectName' => 'cartorioinep',
            'apiController' => 'CartorioInep',
            'apiResource'   => 'cartorioinep-search',
            'showIdOnValue' => false
        );
            
        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou nome do cartório';
    }

    protected function loadAssets() {
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/SimpleSearchCartorioInep.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
      }
}