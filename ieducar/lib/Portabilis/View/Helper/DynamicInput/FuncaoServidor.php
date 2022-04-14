<?php

class Portabilis_View_Helper_DynamicInput_FuncaoServidor extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'funcao_servidor';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $instituicaoId = $this->getInstituicaoId($options['instituicaoId'] ?? null);
        $escolaId = $this->getEscolaId($options['escolaId'] ?? null);
        $cursoId = $this->getCursoId($options['cursoId'] ?? null);
        
        $obj = new clsPmieducarFuncao();
        $lista = $obj->lista();

        foreach ($lista as $key => $funcao) {
            $resources[$funcao["cod_funcao"]] = $funcao['nm_funcao'];
        }

        if (count($resources) == 0)
            return $this->insertOption(null, 'Sem opções', $resources);
        else
            return $this->insertOption(null, 'Selecione a função', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Função']];
    }

    public function funcaoServidor($options = [])
    {
        parent::select($options);
    }
}
