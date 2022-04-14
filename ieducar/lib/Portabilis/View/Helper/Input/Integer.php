<?php

class Portabilis_View_Helper_Input_Integer extends Portabilis_View_Helper_Input_Numeric
{
    protected function fixupValidation($inputOptions)
    {
        // fixup para remover caracteres não numericos
        // inclusive pontos '.', não removidos pela super classe
        $js = '
            $j(\'#' . $inputOptions['id'] . "').keyup(function(){
                var oldValue = this.value;

                this.value = this.value.replace(/[^0-9\.]/g, '');
                this.value = this.value.replace('.', '');

                if (oldValue != this.value)
                    messageUtils.error('Informe apenas números.', this);
            });
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = false);
    }

    public function integer($attrName, $options = [])
    {
        parent::numeric($attrName, $options);
    }
}
