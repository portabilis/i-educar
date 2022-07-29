<?php

class FichaAeeController extends ApiCoreController
{

    public function excluirFichaAee()
    {
        $ficha_aee_id = $this->getRequest()->ficha_aee_id;

        if (is_numeric($ficha_aee_id)) {
            $obj = new clsModulesFichaAee($ficha_aee_id);
            return ['result' => $obj->excluir()];
        }

        return [];
    }

    public function editarFichaAee()
    {
        $ficha_aee_id = (int) $this->getRequest()->ficha_aee_id;        
        $necessidades_aprendizagem = $this->getRequest()->necessidades_aprendizagem;
        $caracterizacao_pedagogica = $this->getRequest()->caracterizacao_pedagogica;

        if (is_numeric($ficha_aee_id)) {
            $obj = new clsModulesFichaAee(
                $ficha_aee_id,
                null,
                null,
                null,
                $necessidades_aprendizagem,
                $caracterizacao_pedagogica
            );

            $editou = $obj->edita();

            if ($editou)
                return ['result' => 'Edição efetuada com sucesso.'];
        }

        return ['result' => "Edição não realizada."];
    }

    public function criarFichaAee()
    {
        $data = $this->getRequest()->data;
        $ref_cod_turma = $this->getRequest()->turma;
        $ref_cod_matricula = $this->getRequest()->matricula;        
        $necessidades_aprendizagem = $this->getRequest()->necessidades_aprendizagem;
        $caracterizacao_pedagogica = $this->getRequest()->caracterizacao_pedagogica;

        $obj = new clsModulesFichaAee(
            null,
            $data,
            $ref_cod_turma,
            $ref_cod_matricula,            
            $necessidades_aprendizagem,
            $caracterizacao_pedagogica
        );

        $existe = $obj->existe();

        if ($existe){
            return [ "result" => "Cadastro não realizado, pois já há uma Ficha cadastrada para este Aluno nesta Turma." ];
            $this->simpleRedirect('educar_professores_ficha_aee_cad.php');
        }

        $cadastrou = $obj->cadastra();
        if (!$cadastrou) {
            return ["result" => "Cadastro não realizado."];
            $this->simpleRedirect('educar_professores_ficha_aee_cad.php');
        } else {
            return ["result" => "Cadastro efetuado com sucesso."];
            $this->simpleRedirect('educar_professores_ficha_aee_lst.php');
        }

        return ["result" => "Cadastro não realizado."];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('post', 'excluir-ficha-aee')) {
            $this->appendResponse($this->excluirFichaAee());
        } else if ($this->isRequestFor('post', 'editar-ficha-aee')) {
            $this->appendResponse($this->editarFichaAee());
        } else if ($this->isRequestFor('post', 'nova-ficha-aee')) {
            $this->appendResponse($this->criarFichaAee());
        } 
    }
}
