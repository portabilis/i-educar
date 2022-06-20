<?php

class PlanejamentoAulaAeeController extends ApiCoreController
{
    public function verificarPlanoAulaSendoUsado ()
    {
        $planejamento_aula_aee_id = $this->getRequest()->planejamento_aula_aee_id;

        if (is_numeric($planejamento_aula_aee_id)) {
            $obj = new clsModulesPlanejamentoAulaAee($planejamento_aula_aee_id);
            $conteudos_ids = $obj->existeLigacaoRegistroAula();

            return ['conteudos_ids' => $conteudos_ids];
        }

        return [];
    }

    public function verificarPlanoAulaSendoUsado2 ()
    {
        $conteudos_ids = [];

        $planejamento_aula_aee_id = $this->getRequest()->planejamento_aula_aee_id;
        $conteudos_novos = $this->getRequest()->conteudos;

        if (is_numeric($planejamento_aula_aee_id) && is_array($conteudos_novos) && count($conteudos_novos) > 0) {
            $obj = new clsModulesPlanejamentoAulaConteudoAee();
            $conteudos_atuais = $obj->lista($planejamento_aula_aee_id);
            $conteudos = $obj->retornaDiferencaEntreConjuntosConteudos($conteudos_atuais, $conteudos_novos);

            $obj = new clsModulesComponenteMinistradoConteudoAee();
            $conteudos_ids = $obj->existeLigacaoRegistroAula(array_column($conteudos['remover'], 'id'));

            return ['conteudos_ids' => $conteudos_ids];
        }

        return [];
    }

    public function excluirPlanoAula ()
    {
        $planejamento_aula_aee_id = $this->getRequest()->planejamento_aula_aee_id;

        if (is_numeric($planejamento_aula_aee_id)) {
            $obj = new clsModulesPlanejamentoAulaAee($planejamento_aula_aee_id);
            return ['result' => $obj->excluir()];
        }

        return [];
    }

    public function editarPlanoAula ()
    {
        $planejamento_aula_aee_id = $this->getRequest()->planejamento_aula_aee_id;
        $ddp = $this->getRequest()->ddp;
        $necessidade_aprendizagem = $this->getRequest()->necessidade_aprendizagem;
        $bncc = $this->getRequest()->bncc;
        $conteudos = $this->getRequest()->conteudos_novos;
        $caracterizacao_pedagogica = $this->getRequest()->caracterizacao_pedagogica;

        if (is_numeric($planejamento_aula_aee_id)) {
            $obj = new clsModulesPlanejamentoAulaAee(
                $planejamento_aula_aee_id,
                null,
                null,
                null,
                null,
                null,
                $ddp,
                $necessidade_aprendizagem,
                $bncc,
                $conteudos,
                $caracterizacao_pedagogica
            );

            $editou = $obj->edita();

            if ($editou)
                return ['result' => 'Edição efetuada com sucesso.'];
        }

        return ['result' => "Edição não realizada."];
    }

    public function criarPlanoAula ()
    {
       

        $data_agora = new DateTime('now');
        $data_agora = new \DateTime($data_agora->format('Y-m-d'));

        $data_inicial = $this->getRequest()->data_inicial;
        $data_final = $this->getRequest()->data_final;        
        $turma = $this->getRequest()->turma;
        $matricula = $this->getRequest()->matricula;
        $faseEtapa = $this->getRequest()->faseEtapa;
        $ddp = $this->getRequest()->ddp;
        $necessidade_aprendizagem = $this->getRequest()->necessidade_aprendizagem;
        $caracterizacao_pedagogica = $this->getRequest()->caracterizacao_pedagogica;
        $conteudos = $this->getRequest()->conteudos;
        $componentesCurriculares = $this->getRequest()->componentesCurriculares;
        $bnccs = $this->getRequest()->bnccs;
        $bnccEspecificacoes = $this->getRequest()->bnccEspecificacoes;
        $recursos_didaticos = $this->getRequest()->recursos_didaticos;
        $outros = $this->getRequest()->outros;

        die(var_dump($conteudos));

        // $sequencia = $faseEtapa;
        // $obj = new clsPmieducarTurmaModulo();

        // $data = $obj->pegaPeriodoLancamentoNotasFaltas($turma, $sequencia);
        // if ($data['inicio'] != null && $data['fim'] != null) {
        //     $data['inicio_periodo_lancamentos'] = explode(',', $data['inicio']);
        //     $data['fim_periodo_lancamentos'] = explode(',', $data['fim']);

        //     array_walk($data['inicio_periodo_lancamentos'], function(&$data_inicio, $key) {
        //         $data_inicio = new \DateTime($data_inicio);
        //     });

        //     array_walk($data['fim_periodo_lancamentos'], function(&$data_fim, $key) {
        //         $data_fim = new \DateTime($data_fim);
        //     });
        // }

        // $data['inicio'] = new \DateTime($obj->pegaEtapaSequenciaDataInicio($turma, $sequencia));
        // $data['fim'] = new \DateTime($obj->pegaEtapaSequenciaDataFim($turma, $sequencia));

        // $podeRegistrar = false;
        // if (is_array($data['inicio_periodo_lancamentos']) && is_array($data['fim_periodo_lancamentos'])) {
        //     for ($i=0; $i < count($data['inicio_periodo_lancamentos']); $i++) {
        //         $data_inicio = $data['inicio_periodo_lancamentos'][$i];
        //         $data_fim = $data['fim_periodo_lancamentos'][$i];

        //         $podeRegistrar = $data_agora >= $data_inicio && $data_agora <= $data_fim;

        //         if ($podeRegistrar) break;
        //     }
        //     $podeRegistrar = $podeRegistrar && new DateTime($data_inicial) >= $data['inicio'] && new DateTime($data_final) <= $data['fim'];
        // } else {
        //     $podeRegistrar = new DateTime($data_inicial) >= $data['inicio'] && new DateTime($data_final) <= $data['fim'];
        //     $podeRegistrar = $podeRegistrar && $data_agora >= $data['inicio'] && $data_agora <= $data['fim'];
        // }

        // if (!$podeRegistrar) {
        //     return [ "result" => "Cadastro não realizado, pois o intervalo de datas não se adequa as etapas da turma." ];
        //     $this->simpleRedirect('educar_professores_planejamento_de_aula_cad.php');
        // }

        $obj = new clsModulesPlanejamentoAulaAee(           
           $turma,
           $matricula,
           $componentesCurriculares,
           $faseEtapa,
           $data_inicial,
           $data_final,
           $ddp,
           $necessidade_aprendizagem,
           $bnccs,
           $conteudos,
           $caracterizacao_pedagogica,
           $bnccEspecificacoes,
           $recursos_didaticos,
           $outros
        );

        $existe = $obj->existe(); 

        if ($existe){
            return [ "result" => "Cadastro não realizado, pois já há um planejamento para esse componente nesse período." ];
            $this->simpleRedirect('educar_professores_planejamento_de_aula_aee_cad.php');
        }

        $cadastrou = $obj->cadastra();
        if (!$cadastrou) {
            return [ "result" => "Cadastro não realizado." ];
            $this->simpleRedirect('educar_professores_planejamento_de_aula_aee_cad.php');
        } else {
            return [ "result" => "Cadastro efetuado com sucesso." ];
            $this->simpleRedirect('educar_professores_planejamento_de_aula_aee_lst.php');
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
        } else if ($this->isRequestFor('post', 'novo-plano-aula-aee')) {
            $this->appendResponse($this->criarPlanoAula());
        }
    }
}
