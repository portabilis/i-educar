<?php

class Portabilis_View_Helper_Input_CoreSelect extends Portabilis_View_Helper_Input_Core
{
    protected function inputName()
    {
        return parent::inputName() . '_id';
    }

    public function select($options = [])
    {
        // this helper options
        $defaultOptions = [
            'id' => null,
            'objectName' => '',
            'attrName' => $this->inputName(),
            'resources' => [],
            'options' => []
        ];

        $defaultOptions = $this->mergeOptions($this->defaultOptions(), $defaultOptions);
        $this->options = $this->mergeOptions($options, $defaultOptions);
        $this->options['options'] = $this->mergeOptions($this->options['options'], $defaultOptions['options']);

        // select options

        $defaultInputOptions = [
            'label' => Portabilis_String_Utils::humanize($this->inputName()),
            'value' => $this->inputValue($this->options['id']),
            'resources' => $this->inputOptions($this->options)
        ];

        $inputOptions = $this->mergeOptions($this->options['options'], $defaultInputOptions);
        $helperOptions = ['objectName' => $this->options['objectName']];

        // input
        $this->inputsHelper()->select($this->options['attrName'], $inputOptions, $helperOptions);
    }

    /**
     * subscrever no child caso deseje carregar mais opções do banco de dados
     * antes de carregar a página, ou deixar apenas com a opção padrão e
     * carregar via ajax
     */
    protected function inputOptions($options)
    {
        return $this->insertOption(
            null,
            'Selecione um(a) ' . Portabilis_String_Utils::humanize($this->inputName()),
            $resources
        );
    }

    /**
     * overwrite this method in childrens to set additional default options, to
     * be merged with received options, and pass to select helper
     */
    protected function defaultOptions()
    {
        return [];
    }
}
