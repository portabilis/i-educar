<?php

class Portabilis_View_Helper_DynamicInput_BibliotecaPesquisaCliente extends Portabilis_View_Helper_DynamicInput_Core
{
    protected function getResourceId($id = null)
    {
        if (!$id && $this->viewInstance->ref_cod_cliente) {
            $id = $this->viewInstance->ref_cod_cliente;
        }

        return $id;
    }

    public function bibliotecaPesquisaCliente($options = [])
    {
        $defaultOptions = ['id' => null, 'options' => [], 'hiddenInputOptions' => []];
        $options = $this->mergeOptions($options, $defaultOptions);

        $inputHint = '<img border=\'0\' onclick=\'pesquisaCliente();\' id=\'lupa_pesquisa_cliente\' name=\'lupa_pesquisa_cliente\' src=\'imagens/lupaT.png\' />';

        $defaultInputOptions = [
            'id' => 'nome_cliente',
            'label' => 'Cliente',
            'value' => '',
            'size' => '30',
            'max_length' => '255',
            'required' => true,
            'expressao' => false,
            'inline' => false,
            'label_hint' => '',
            'input_hint' => $inputHint,
            'callback' => '',
            'event' => 'onKeyUp',
            'disabled' => true
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        $this->viewInstance->campoTexto(...array_values($inputOptions));

        $defaultHiddenInputOptions = [
            'id' => 'ref_cod_cliente',
            'value' => $this->getResourceId($options['id'])
        ];

        $hiddenInputOptions = $this->mergeOptions($options['hiddenInputOptions'], $defaultHiddenInputOptions);

        $this->viewInstance->campoOculto(...array_values($hiddenInputOptions));

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, '
            var resetCliente = function(){
                $("#ref_cod_cliente").val("");
                $("#nome_cliente").val("");
            }

            $("#ref_cod_biblioteca").change(resetCliente);
        ', true);

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, '
            function pesquisaCliente() {
                var additionalFields = getElementFor(\'biblioteca\');
                var exceptFields     = getElementFor(\'nome_cliente\');

                if (validatesPresenseOfValueInRequiredFields(additionalFields, exceptFields)) {
                    var bibliotecaId   = getElementFor(\'biblioteca\').val();
                    var attrIdName     = getElementFor(\'cliente\').attr(\'id\');

                    pesquisa_valores_popless(\'educar_pesquisa_cliente_lst.php?campo1=\'+attrIdName+\'&campo2=nome_cliente&ref_cod_biblioteca=\'+bibliotecaId);
               }
          }
        ');
    }
}
