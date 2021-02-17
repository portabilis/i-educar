<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';
require_once 'Portabilis/Business/Professor.php';
require_once 'App/Model/NivelTipoUsuario.php';

class Portabilis_View_Helper_DynamicInput_EscolaSemFiltroPorUsuario extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputValue($value = null)
    {
        return $this->getEscolaId($value);
    }

    protected function inputName()
    {
        return 'ref_cod_escola';
    }

    protected function inputOptions($options)
    {
        $instituicaoId = $this->getInstituicaoId($options['instituicaoId'] ?? null);
        $resources = App_Model_IedFinder::getEscolas($instituicaoId);

        return $this->insertOption(null, 'Selecione uma escola', $resources);
    }

    public function escolaSemFiltroPorUsuario($options = [])
    {
        $this->attributeJs($options);
        $this->select($options);
        Portabilis_View_Helper_Application::loadChosenLib($this->viewInstance);
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, '/modules/DynamicInput/Assets/Javascripts/Escola.js');
    }

    protected function attributeJs($options)
    {
        $js = '$j("#ref_cod_escola").attr("escola_sem_filtro_por_usuario", true)';

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = false);
    }
}
