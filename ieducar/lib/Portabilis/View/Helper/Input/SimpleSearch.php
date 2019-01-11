<?php

require_once 'lib/Portabilis/View/Helper/Input/Core.php';

class Portabilis_View_Helper_Input_SimpleSearch extends Portabilis_View_Helper_Input_Core
{
    protected function resourceValue($id)
    {
        throw new Exception(
            'You are trying to get the resource value, but this is a generic class, ' .
            'please, define the method resourceValue in a resource subclass.'
        );
    }

    public function simpleSearch($objectName, $attrName, $options = [])
    {
        $defaultOptions = [
            'options' => [],
            'apiModule' => 'Api',
            'apiController' => ucwords($objectName),
            'apiResource' => $objectName . '-search',
            'searchPath' => '',
            'addHiddenInput' => true,
            'hiddenInputOptions' => [],
            'showIdOnValue' => true
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        if (empty($options['searchPath'])) {
            $options['searchPath'] = '/module/' . $options['apiModule'] . '/' . $options['apiController'] .
                '?oper=get&resource=' . $options['apiResource'];
        }

        $resourceId = $options['hiddenInputOptions']['options']['value'];

        if ($resourceId && !$options['options']['value']) {
            if ($options['showIdOnValue']) {
                $options['options']['value'] = $resourceId . ' - ' . $this->resourceValue($resourceId);
            } else {
                $options['options']['value'] = $this->resourceValue($resourceId);
            }
        }

        $this->hiddenInput($objectName, $attrName, $options);
        $this->textInput($objectName, $attrName, $options);
        $this->js($objectName, $attrName, $options);
    }

    protected function hiddenInput($objectName, $attrName, $options)
    {
        if ($options['addHiddenInput']) {
            if ($attrName == 'id') {
                throw new CoreExt_Exception(
                    'When $addHiddenInput is true the $attrName (of the visible input) ' .
                    'must be different than \'id\', because the hidden input will use it.'
                );
            }

            $defaultHiddenInputOptions = ['options' => [], 'objectName' => $objectName];
            $hiddenInputOptions = $this->mergeOptions($options['hiddenInputOptions'], $defaultHiddenInputOptions);

            $this->inputsHelper()->hidden('id', [], $hiddenInputOptions);
        }
    }

    protected function textInput($objectName, $attrName, $options)
    {
        $textHelperOptions = ['objectName' => $objectName];

        $options['options']['placeholder'] = Portabilis_String_Utils::toLatin1(
            $this->inputPlaceholder([]),
            ['escape' => false]
        );

        $this->inputsHelper()->text($attrName, $options['options'], $textHelperOptions);
    }

    protected function js($objectName, $attrName, $options)
    {
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/SimpleSearch.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);

        $resourceOptions = 'simpleSearch' . Portabilis_String_Utils::camelize($objectName) . 'Options';

        $js = "
            $resourceOptions = typeof $resourceOptions == 'undefined' ? {} : $resourceOptions;
            simpleSearchHelper.setup('$objectName', '$attrName', '" . $options['searchPath'] . "', $resourceOptions);
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
    }
}
