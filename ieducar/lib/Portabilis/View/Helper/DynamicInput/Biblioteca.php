<?php

class Portabilis_View_Helper_DynamicInput_Biblioteca extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputValue($value = null)
    {
        return $this->getBibliotecaId($value);
    }

    protected function inputName()
    {
        return 'ref_cod_biblioteca';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $instituicaoId = $this->getInstituicaoId();
        $escolaId = $this->getEscolaId();

        if ($instituicaoId and $escolaId and empty($resources)) {
            // se possui id escola então filtra bibliotecas pelo id desta escola
            $resources = App_Model_IedFinder::getBibliotecas($instituicaoId, $escolaId);
        }

        return $this->insertOption(null, 'Selecione uma biblioteca', $resources);
    }

    public function selectInput($options = [])
    {
        parent::select($options);
    }

    public function stringInput($options = [])
    {
        $defaultOptions = ['options' => []];
        $options = $this->mergeOptions($options, $defaultOptions);

        // subescreve $options['options']['value'] com nome escola
        if (isset($options['options']['value']) && $options['options']['value']) {
            $bibliotecaId = $options['options']['value'];
        } else {
            $bibliotecaId = $this->getBibliotecaId($options['id']);
        }

        $biblioteca = App_Model_IedFinder::getBiblioteca($bibliotecaId);
        $options['options']['value'] = $biblioteca['nm_biblioteca'];

        $defaultInputOptions = [
            'id' => 'ref_cod_biblioteca',
            'label' => 'Biblioteca',
            'value' => '',
            'inline' => false,
            'descricao' => '',
            'separador' => ':'
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        $this->viewInstance->campoOculto($inputOptions['id'], $bibliotecaId);

        $inputOptions['id'] = 'biblioteca_nome';

        $this->viewInstance->campoRotulo(...array_values($inputOptions));
    }

    public function biblioteca($options = [])
    {
        if ($this->hasNivelAcesso('POLI_INSTITUCIONAL') || $this->hasNivelAcesso('INSTITUCIONAL')) {
            $this->selectInput($options);
        } elseif ($this->hasNivelAcesso('SOMENTE_ESCOLA') || $this->hasNivelAcesso('SOMENTE_BIBLIOTECA')) {
            $this->stringInput($options);
        }

        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, '/modules/DynamicInput/Assets/Javascripts/Biblioteca.js');
    }
}
