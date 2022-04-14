<?php

class Portabilis_View_Helper_DynamicInput_BibliotecaPesquisaObra extends Portabilis_View_Helper_DynamicInput_Core
{
    protected function getAcervoId($id = null)
    {
        if (!$id && $this->viewInstance->ref_cod_acervo) {
            $id = $this->viewInstance->ref_cod_acervo;
        }

        return $id;
    }

    protected function getObra($id)
    {
        if (!$id) {
            $id = $this->getAcervoId($id);
        }

        // chama finder somente se possuir id, senão ocorrerá exception
        $obra = empty($id) ? null : App_Model_IedFinder::getBibliotecaObra($this->getBibliotecaId(), $id);

        return $obra;
    }

    public function bibliotecaPesquisaObra($options = [])
    {
        $defaultOptions = ['id' => null, 'options' => [], 'hiddenInputOptions' => []];
        $options = $this->mergeOptions($options, $defaultOptions);

        $inputHint = '<img border=\'0\' onclick=\'pesquisaObra();\' id=\'lupa_pesquisa_obra\' name=\'lupa_pesquisa_obra\' src=\'imagens/lupaT.png\' />';

        // se não recuperar obra, deixa titulo em branco
        $obra = $this->getObra($options['id']);
        $tituloObra = $obra ? $obra['titulo'] : '';

        $defaultInputOptions = [
            'id' => 'titulo_obra',
            'label' => 'Obra',
            'value' => $tituloObra,
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
            'id' => 'ref_cod_acervo',
            'value' => $this->getAcervoId($options['id'])
        ];

        $hiddenInputOptions = $this->mergeOptions($options['hiddenInputOptions'], $defaultHiddenInputOptions);

        $this->viewInstance->campoOculto(...array_values($hiddenInputOptions));

        // Ao selecionar obra, na pesquisa de obra é setado o value deste elemento
        $this->viewInstance->campoOculto('cod_biblioteca', '');

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, '
            var resetObra = function(){
                $("#ref_cod_acervo").val("");
                $("#titulo_obra").val("");
            }

            $("#ref_cod_biblioteca").change(resetObra);
        ', true);

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, '
            function pesquisaObra() {

                var additionalFields = getElementFor("biblioteca");
                var exceptFields     = getElementFor("titulo_obra");

                if (validatesPresenseOfValueInRequiredFields(additionalFields, exceptFields)) {
                    var bibliotecaId = getElementFor("biblioteca").val();

                    pesquisa_valores_popless("educar_pesquisa_obra_lst.php?campo1=ref_cod_acervo&campo2=titulo_obra&campo3="+bibliotecaId)
                }
            }
        ');
    }
}
