<?php

class PlanejamentoAulaController extends ApiCoreController
{
    public function verificarPlanoAulaSendoUsado ()
    {
        $planejamento_aula_id = $this->getRequest()->planejamento_aula_id;

        if (is_numeric($planejamento_aula_id)) {
            $obj = new clsModulesPlanejamentoAula($planejamento_aula_id);
            $conteudos_ids = $obj->existeLigacaoRegistroAula();

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
            return ['result' => $obj->excluir()];
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

    public function criarPlanoAula ()
    {
        $data_inicial = $this->getRequest()->data_inicial;
        $data_final = $this->getRequest()->data_final;
        $turma = $this->getRequest()->turma;
        $faseEtapa = $this->getRequest()->faseEtapa;
        $ddp = $this->getRequest()->ddp;
        $atividades = $this->getRequest()->atividades;
        $referencias = $this->getRequest()->referencias;
        $conteudos = $this->getRequest()->conteudos;
        $componentesCurriculares = $this->getRequest()->componentesCurriculares;
        $bnccs = $this->getRequest()->bnccs;
        $bnccEspecificacoes = $this->getRequest()->bnccEspecificacoes;

        $sequencia = $faseEtapa;
        $obj = new clsPmieducarTurmaModulo();

        $data = $obj->pegaPeriodoLancamentoNotasFaltas($turma, $sequencia);
        if ($data['inicio'] != null && $data['fim'] != null) {
            $data['inicio'] = explode(',', $data['inicio']);
            $data['fim'] = explode(',', $data['fim']);

            array_walk($data['inicio'], function(&$data_inicio, $key) {
                $data_inicio = new \DateTime($data_inicio);
            });

            array_walk($data['fim'], function(&$data_fim, $key) {
                $data_fim = new \DateTime($data_fim);
            });
        } else {
            $data['inicio'] = new \DateTime($obj->pegaEtapaSequenciaDataInicio($turma, $sequencia));
            $data['fim'] = new \DateTime($obj->pegaEtapaSequenciaDataFim($turma, $sequencia));
        }

        $podeRegistrar = false;
        if (is_array($data['inicio']) && is_array($data['fim'])) {
            for ($i=0; $i < count($data['inicio']); $i++) {
                $data_inicio = $data['inicio'][$i];
                $data_fim = $data['fim'][$i];

                $podeRegistrar = $data_inicial >= $data_inicio && $data_final <= $data_fim;

                if ($podeRegistrar) break;
            }     
        } else {
            $podeRegistrar = new DateTime($data_inicial) >= $data['inicio'] && new DateTime($data_final) <= $data['fim'];
        }

        if (!$podeRegistrar) {
            return [ "result" => "Cadastro não realizado, pois o intervalo de datas não se adequa as etapas da turma." ];
            $this->simpleRedirect('educar_professores_planejamento_de_aula_cad.php');
        }

        $obj = new clsModulesPlanejamentoAula(
           null,
           $turma,
           $componentesCurriculares,
           $faseEtapa,
           $data_inicial,
           $data_final,
           $ddp, 
           $atividades,
           $bnccs,
           $conteudos,
           $referencias,
           $bnccEspecificacoes,
        );

        $existe = $obj->existe();
        if ($existe){
            return [ "result" => "Cadastro não realizado, pois este plano de aula já existe." ];
            $this->simpleRedirect('educar_professores_planejamento_de_aula_cad.php');
        }

        $cadastrou = $obj->cadastra();
        if (!$cadastrou) {   
            return [ "result" => "Cadastro não realizado." ];
            $this->simpleRedirect('educar_professores_planejamento_de_aula_cad.php');
        } else {
            return [ "result" => "Cadastro efetuado com sucesso." ];
            $this->simpleRedirect('educar_professores_planejamento_de_aula_lst.php');
        }

        return [ "result" => "Cadastro não realizado." ];
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
        } else if ($this->isRequestFor('post', 'novo-plano-aula')) {
            $this->appendResponse($this->criarPlanoAula());
        }
    }
}
