<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';
require_once 'Portabilis/Business/Professor.php';
require_once 'App/Model/NivelTipoUsuario.php';

class Portabilis_View_Helper_DynamicInput_Escola extends Portabilis_View_Helper_DynamicInput_CoreSelect
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
        $resources = $options['resources'];
        $instituicaoId = $this->getInstituicaoId($options['instituicaoId'] ?? null);
        $userId = $this->getCurrentUserId();

        if ($instituicaoId && empty($resources)) {
            $permissao = new clsPermissoes();
            $nivel = $permissao->nivel_acesso($userId);

            if (
                $nivel == App_Model_NivelTipoUsuario::ESCOLA
                || $nivel == App_Model_NivelTipoUsuario::BIBLIOTECA
            ) {
                $escolas_usuario = [];
                $escolasUser = App_Model_IedFinder::getEscolasUser($userId);

                foreach ($escolasUser as $e) {
                    $escolas_usuario[$e['ref_cod_escola']] = $e['nome'];
                }

                return $this->insertOption(null, 'Selecione uma escola', $escolas_usuario);
            }

            $resources = App_Model_IedFinder::getEscolas($instituicaoId);
        }

        return $this->insertOption(null, 'Selecione uma escola', $resources);
    }

    public function escola($options = [])
    {
        $this->select($options);

        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, '/modules/DynamicInput/Assets/Javascripts/Escola.js');
    }
}
