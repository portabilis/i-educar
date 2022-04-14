<?php

class PlanejamentoAulaController extends ApiCoreController
{
    public function verificarPlanoAulaSendoUsado ()
    {
        $planejamento_aula_id = $this->getRequest()->planejamento_aula_id;

        if (is_numeric($planejamento_aula_id)) {
            $obj = new clsModulesPlanejamentoAula($planejamento_aula_id);
            $conteudos_ids = $obj->existeLigacaoRegistroAula($id);

            return ['conteudos_ids' => $conteudos_ids];
        }

        return [];
    }

    public function verificarPlanoAulaSendoUsado2 ()
    {
        $conteudos_ids = [];

        $planejamento_aula_id = $this->getRequest()->planejamento_aula_id;
        $conteudos_novos = $this->getRequest()->conteudos;

        if (is_numeric($planejamento_aula_id) && is_array($conteudos_novos) && count($conteudos_novos) > 0) {
            $obj = new clsModulesPlanejamentoAulaConteudo();
            $conteudos_atuais = $obj->lista($planejamento_aula_id);
            $conteudos = $obj->retornaDiferencaEntreConjuntosConteudos($conteudos_atuais, $conteudos_novos);

            $obj = new clsModulesComponenteMinistradoConteudo();
            $conteudos_ids = $obj->existeLigacaoRegistroAula(array_column($conteudos['remover'], 'id'));

            return ['conteudos_ids' => $conteudos_ids];
        }

        return [];
    }

    public function excluirPlanoAula ()
    {
        $planejamento_aula_id = $this->getRequest()->planejamento_aula_id;

        if (is_numeric($planejamento_aula_id)) {
            $obj = new clsModulesPlanejamentoAula($planejamento_aula_id);
            return ['result' => $obj->excluir($id)];
        }

        return [];
    }

    public function editarPlanoAula ()
    {
        $planejamento_aula_id = $this->getRequest()->planejamento_aula_id;
        $ddp = $this->getRequest()->ddp;
        $atividades = $this->getRequest()->atividades;
        $bncc = $this->getRequest()->bncc;
        $conteudos = $this->getRequest()->conteudos_novos;
        $referencias = $this->getRequest()->referencias;

        if (is_numeric($planejamento_aula_id)) {
            $obj = new clsModulesPlanejamentoAula(
                $planejamento_aula_id,
                null,
                null,
                null,
                null,
                null,
                $ddp,
                $atividades,
                $bncc,
                $conteudos,
                $referencias
            );
    
            $editou = $obj->edita();
    
            if ($editou)
                return ['result' => 'Edição efetuada com sucesso.'];
        }

        return ['result' => "Edição não realizada."];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('post', 'verificar-plano-aula-sendo-usado')) {
            $this->appendResponse($this->verificarPlanoAulaSendoUsado());
        } else if ($this->isRequestFor('post', 'verificar-plano-aula-sendo-usado2')) {
            $this->appendResponse($this->verificarPlanoAulaSendoUsado2());
        } else if ($this->isRequestFor('post', 'excluir-plano-aula')) {
            $this->appendResponse($this->excluirPlanoAula());
        } else if ($this->isRequestFor('post', 'editar-plano-aula')) {
            $this->appendResponse($this->editarPlanoAula());
        }
    }
}
