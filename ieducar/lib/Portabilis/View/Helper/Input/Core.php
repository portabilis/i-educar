<?php

class Portabilis_View_Helper_Input_Core
{
    public function __construct($viewInstance, $inputsHelper)
    {
        $this->viewInstance = $viewInstance;
        $this->_inputsHelper = $inputsHelper;

        $this->loadCoreAssets();
        $this->loadAssets();
    }

    protected function inputsHelper()
    {
        return $this->_inputsHelper;
    }

    protected function helperName()
    {
        $arrayClassName = explode('_', get_class($this));

        return end($arrayClassName);
    }

    protected function inputName()
    {
        return Portabilis_String_Utils::underscore($this->helperName());
    }

    protected function inputValue($value = null)
    {
        if (!is_null($value)) {
            return $value;
        }

        if (isset($this->viewInstance->{$this->inputName()})) {
            return $this->viewInstance->{$this->inputName()};
        }

        return null;
    }

    protected function inputPlaceholder($inputOptions)
    {
        return $inputOptions['placeholder'] ?? $inputOptions['label'] ?? null;
    }

    protected function fixupPlaceholder($inputOptions)
    {
        $id = $inputOptions['id'];
        $placeholder = $this->inputPlaceholder($inputOptions);

        $script = '
            var $input = $j(\'#' . $id . '\');
            if ($input.is(\':enabled\')) {
                $input.attr(\'placeholder\', \'' . $placeholder . '\');
            }
        ';

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $script, $afterReady = true);
    }

    protected function loadCoreAssets()
    {
        // carrega estilo para feedback messages, devido algumas validações de inuts
        // adicionarem mensagens

        $style = '/modules/Portabilis/Assets/Stylesheets/Frontend.css';

        Portabilis_View_Helper_Application::loadStylesheet($this->viewInstance, $style);

        $dependencies = [
            '/modules/Portabilis/Assets/Javascripts/Utils.js',
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
            '/modules/Portabilis/Assets/Javascripts/Validator.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $dependencies);
    }

    protected function loadAssets()
    {
        $rootPath = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        $style = "/modules/DynamicInput/Assets/Stylesheets/{$this->helperName()}.css";
        $script = "/modules/DynamicInput/Assets/Javascripts/{$this->helperName()}.js";

        if (file_exists($rootPath . $style)) {
            Portabilis_View_Helper_Application::loadStylesheet($this->viewInstance, $style);
        }

        if (file_exists($rootPath . $script)) {
            Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $script);
        }
    }

    // wrappers

    protected function getCurrentUserId()
    {
        return Portabilis_Utils_User::currentUserId();
    }

    protected function getPermissoes()
    {
        return Portabilis_Utils_User::getClsPermissoes();
    }

    protected function hasNivelAcesso($nivelAcessoType)
    {
        return Portabilis_Utils_User::hasNivelAcesso($nivelAcessoType);
    }

    protected function getDataMapperFor($packageName, $modelName)
    {
        return Portabilis_DataMapper_Utils::getDataMapperFor($packageName, $modelName);
    }

    protected static function mergeOptions($options, $defaultOptions)
    {
        return Portabilis_Array_Utils::merge($options, $defaultOptions);
    }

    protected static function insertOption($key, $value, $array)
    {
        return Portabilis_Array_Utils::insertIn($key, $value, $array);
    }

    // ieducar helpers

    protected function getInstituicaoId($instituicaoId = null)
    {
        if (!is_null($instituicaoId) && is_numeric($instituicaoId)) {
            return $instituicaoId;
        }

        if (isset($this->viewInstance->ref_cod_instituicao) && is_numeric($this->viewInstance->ref_cod_instituicao)) {
            return $this->viewInstance->ref_cod_instituicao;
        }

        if (isset($this->viewInstance->ref_cod_escola) && is_numeric($this->viewInstance->ref_cod_escola)) {
            $escola = App_Model_IedFinder::getEscola($this->viewInstance->ref_cod_escola);

            return $escola['ref_cod_instituicao'];
        }

        if (isset($this->viewInstance->ref_cod_biblioteca) && is_numeric($this->viewInstance->ref_cod_biblioteca)) {
            $biblioteca = App_Model_IedFinder::getBiblioteca($this->viewInstance->ref_cod_biblioteca);

            return $biblioteca['ref_cod_instituicao'];
        }

        return $this->getPermissoes()->getInstituicao($this->getCurrentUserId());
    }

    protected function getEscolaId($escolaId = null)
    {
        if (!is_null($escolaId) && is_numeric($escolaId)) {
            return $escolaId;
        }

        if (isset($this->viewInstance->ref_cod_escola) && is_numeric($this->viewInstance->ref_cod_escola)) {
            return $this->viewInstance->ref_cod_escola;
        }

        if (isset($this->viewInstance->ref_cod_biblioteca) && is_numeric($this->viewInstance->ref_cod_biblioteca)) {
            $biblioteca = App_Model_IedFinder::getBiblioteca($this->viewInstance->ref_cod_biblioteca);

            return $biblioteca['ref_cod_escola'];
        }

        return $this->getPermissoes()->getEscola($this->getCurrentUserId());
    }

    protected function getBibliotecaId($bibliotecaId = null)
    {
        if (!is_null($bibliotecaId) && is_numeric($bibliotecaId)) {
            return $bibliotecaId;
        }

        if (isset($this->viewInstance->ref_cod_biblioteca) && is_numeric($this->viewInstance->ref_cod_biblioteca)) {
            return $this->viewInstance->ref_cod_biblioteca;
        }

        $biblioteca = $this->getPermissoes()->getBiblioteca($this->getCurrentUserId());

        if (is_array($biblioteca) && count($biblioteca) > 0) {
            return $biblioteca[0]['ref_cod_biblioteca'];
        }

        return null;
    }

    protected function getCursoId($cursoId = null)
    {
        if (!is_null($cursoId) && is_numeric($cursoId)) {
            return $cursoId;
        }

        if (isset($this->viewInstance->ref_cod_curso) && is_numeric($this->viewInstance->ref_cod_curso)) {
            return $this->viewInstance->ref_cod_curso;
        }

        return null;
    }

    protected function getSerieId($serieId = null)
    {
        if (!is_null($serieId) && is_numeric($serieId)) {
            return $serieId;
        }

        if (isset($this->viewInstance->ref_cod_serie) && is_numeric($this->viewInstance->ref_cod_serie)) {
            return $this->viewInstance->ref_cod_serie;
        }

        return null;
    }

    protected function getTurmaId($turmaId = null)
    {
        if (!is_null($turmaId) && is_numeric($turmaId)) {
            return $turmaId;
        }

        if (isset($this->viewInstance->ref_cod_turma) && is_numeric($this->viewInstance->ref_cod_turma)) {
            return $this->viewInstance->ref_cod_turma;
        }

        return null;
    }

    
    protected function getComponenteCurricularId($componenteCurricularId = null)
    {
        if (!is_null($componenteCurricularId) && is_numeric($componenteCurricularId)) {
            return $componenteCurricularId;
        }

        if (isset($this->viewInstance->ref_cod_componente_curricular) && is_numeric($this->viewInstance->ref_cod_componente_curricular)) {
            return $this->viewInstance->ref_cod_componente_curricular;
        }

        return null;
    }

    protected function getEscolaridadesId($escolaridadeId = null)
    {
        if (!is_null($escolaridadeId) && is_numeric($escolaridadeId)) {
            return $escolaridadeId;
        }

        if (isset($this->viewInstance->idesco) && is_numeric($this->viewInstance->idesco)) {
            return $this->viewInstance->idesco;
        }

        return null;
    }

    protected function getAno($ano = null){
        if (!is_null($ano) && is_numeric($ano)) {
            return $ano;
        }

        if (isset($this->viewInstance->ano) && is_numeric($this->viewInstance->ano)) {
            return $this->viewInstance->ano;
        }
    }
}
