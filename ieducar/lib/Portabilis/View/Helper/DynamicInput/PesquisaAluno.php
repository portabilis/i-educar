<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/Core.php';

class Portabilis_View_Helper_DynamicInput_PesquisaAluno extends Portabilis_View_Helper_DynamicInput_Core
{
    protected function inputValue($id = null)
    {
        if (!$id && $this->viewInstance->ref_cod_aluno) {
            $id = $this->viewInstance->ref_cod_aluno;
        }

        return $id;
    }

    protected function getResource($id)
    {
        if (!$id) {
            $id = $this->inputValue($id);
        }

        // chama finder somente se possuir id, senão ocorrerá exception
        $resource = empty($id) ? null : App_Model_IedFinder::getAluno($this->getEscolaId(), $id);

        return $resource;
    }

    public function pesquisaAluno($options = [])
    {
        $defaultOptions = ['id' => null, 'options' => [], 'filterByEscola' => false];
        $options = $this->mergeOptions($options, $defaultOptions);

        $inputHint = '<img border=\'0\' onclick=\'pesquisaAluno();\' id=\'lupa_pesquisa_aluno\' name=\'lupa_pesquisa_aluno\' src=\'imagens/lupa.png\' />';

        // se não recuperar recurso, deixa resourceLabel em branco
        $resource = $this->getResource($options['id']);
        $resourceLabel = $resource ? $resource['nome_aluno'] : '';

        $defaultInputOptions = [
            'id' => 'nm_aluno',
            'label' => 'Aluno',
            'value' => $resourceLabel,
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

        call_user_func_array([$this->viewInstance, 'campoTexto'], $inputOptions);

        $this->viewInstance->campoOculto('ref_cod_aluno', $this->inputValue($options['id']));

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, '
            var resetAluno = function(){
                $("#ref_cod_aluno").val("");
                $("#nm_aluno").val("");
            }
            
            $("#ref_cod_escola").change(resetAluno);
        ', true);

        if ($options['filterByEscola']) {
            $js = '
                function pesquisaAluno() {
                    var additionalFields = [document.getElementById("ref_cod_escola")];
                    var exceptFields     = [document.getElementById("nm_aluno")];
                    
                    if (validatesPresenseOfValueInRequiredFields(additionalFields, exceptFields)) {
                        var escolaId = document.getElementById("ref_cod_escola").value;
                        pesquisa_valores_popless("/intranet/educar_pesquisa_aluno.php?ref_cod_escola="+escolaId);
                    }
                }'
            ;
        } else {
            $js = '
                function pesquisaAluno() {
                    var exceptFields     = [document.getElementById("nm_aluno")];
                    
                    if (validatesPresenseOfValueInRequiredFields([], exceptFields)) {
                        pesquisa_valores_popless("/intranet/educar_pesquisa_aluno.php");
                    }
                }
            ';
        }

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js);
    }
}
