<?php

class ComponenteMinistradoAeeController extends ApiCoreController
{
    // public function verificarPlanoAulaSendoUsado()
    // {
    //     $componente_ministrado_aee_id = $this->getRequest()->componente_ministrado_aee_id;

    //     if (is_numeric($componente_ministrado_aee_id)) {
    //         $obj = new clsModulesComponenteMinistradoAee($componente_ministrado_aee_id);
    //         $conteudos_ids = $obj->existeLigacaoRegistroAula();

    //         return ['conteudos_ids' => $conteudos_ids];
    //     }

    //     return [];
    // }

    // public function verificarPlanoAulaSendoUsado2()
    // {
    //     $conteudos_ids = [];

    //     $componente_ministrado_aee_id = $this->getRequest()->componente_ministrado_aee_id;
    //     $conteudos_novos = $this->getRequest()->conteudos;

    //     if (is_numeric($componente_ministrado_aee_id) && is_array($conteudos_novos) && count($conteudos_novos) > 0) {
    //         $obj = new clsModulesPlanejamentoAulaConteudo();
    //         $conteudos_atuais = $obj->lista($componente_ministrado_aee_id);
    //         $conteudos = $obj->retornaDiferencaEntreConjuntosConteudos($conteudos_atuais, $conteudos_novos);

    //         $obj = new clsModulesComponenteMinistradoConteudo();
    //         $conteudos_ids = $obj->existeLigacaoRegistroAula(array_column($conteudos['remover'], 'id'));

    //         return ['conteudos_ids' => $conteudos_ids];
    //     }

    //     return [];
    // }

    public function excluirPlanoAula()
    {
        $componente_ministrado_aee_id = $this->getRequest()->componente_ministrado_aee_id;

        if (is_numeric($componente_ministrado_aee_id)) {
            $obj = new clsModulesComponenteMinistradoAee($componente_ministrado_aee_id);
            return ['result' => $obj->excluir()];
        }

        return [];
    }

    public function verificarPlanoAulaSendoByConteudo()
    {
        // $componente_ministrado_aee_id = $this->getRequest()->componente_ministrado_aee_id;
        // $conteudos = $this->getRequest()->conteudos;

        // if (is_numeric($componente_ministrado_aee_id) && is_array($conteudos) && count($conteudos) > 0) {
        //     $frequencia_ids = [];
        //     $conteudosVerificar = [];

        //     foreach ($conteudos as $conteudo) {
        //         $conteudosVerificar[] = $conteudo[0];
        //     }

        //     $obj = new clsModulesComponenteMinistradoConteudoAee();
        //     $frequenciaUtilizadas = $obj->existeLigacaoRegistroAula($conteudosVerificar);

        //     foreach ($frequenciaUtilizadas as $frequencia) {
        //         $frequencia_ids[] = $frequencia['frequencia_id'];
        //     }

        //     return ['frequencia_ids' => $frequencia_ids];
        // }

        // return [];
    }

    public function editarPlanoAula()
    {
        $componente_ministrado_aee_id = $this->getRequest()->planejamento_aula_id;
        $data_inicial = $this->getRequest()->data_inicial;
        $data_final = $this->getRequest()->data_final;
        $turma = $this->getRequest()->turma;
        $matricula = $this->getRequest()->matricula;
        $faseEtapa = $this->getRequest()->faseEtapa;
        $ddp = $this->getRequest()->ddp;
        $conteudos = $this->getRequest()->conteudos;
        $componentesCurriculares = $this->getRequest()->componentesCurriculares;
        $bnccs = $this->getRequest()->bnccs;
        $bnccEspecificacoes = $this->getRequest()->bnccEspecificacoes;
        $recursos_didaticos = $this->getRequest()->recursos_didaticos;
        $outros = $this->getRequest()->outros;

        $podeEditar = $this->verificarDatasTurma($faseEtapa, $turma, $data_inicial, $data_final);

        if (!$podeEditar) {
            return ["result" => "Edição não realizada, pois o intervalo de datas não se adequa as etapas da turma."];
            $this->simpleRedirect('educar_professores_planejamento_de_aula_aee_cad2.php');
        }

        if (is_numeric($componente_ministrado_aee_id)) {
            $obj = new clsModulesComponenteMinistradoAee(
                $componente_ministrado_aee_id,
                $data_inicial,
                $data_final,
                null,
                null,
                null,
                $ddp,
                $conteudos,
                $componentesCurriculares,
                $bnccs,
                $bnccEspecificacoes,
                $recursos_didaticos,
                $outros
            );

            //die(var_dump(//$obj));

            $editou = $obj->edita();

            if ($editou)
                return ['result' => 'Edição efetuada com sucesso.'];
        }

        return ['result' => "Edição não realizada."];
    }

    public function criarComponenteMinistradoAee()
    {

        $data_cadastro = $this->getRequest()->data;
        $hora_inicio = $this->getRequest()->hora_inicio;
        $hora_fim = $this->getRequest()->hora_fim;
        $ref_cod_matricula = $this->getRequest()->matricula;
        $atividades = $this->getRequest()->atividades;
        $conteudos = $this->getRequest()->conteudos;
        $observacao = $this->getRequest()->observacao;

        // $podeRegistrar = $this->verificarDatasTurma($faseEtapa, $turma, $data_inicial, $data_final);

        // if (!$podeRegistrar) {
        //     return ["result" => "Cadastro não realizado, pois o intervalo de datas não se adequa as etapas da turma."];
        //     $this->simpleRedirect('educar_professores_planejamento_de_aula_aee_cad.php');
        // }

        $obj = new clsModulesComponenteMinistradoAee(
            null,
            $data_cadastro,
            $hora_inicio,
            $hora_fim,
            $ref_cod_matricula,
            $atividades,
            $conteudos,
            $observacao
        );

        // $existe = $obj->existe();

        // if ($existe) {
        //     return ["result" => "Cadastro não realizado, pois já há um planejamento para esse componente nesse período."];
        //     $this->simpleRedirect('educar_professores_planejamento_de_aula_aee_cad.php');
        // }

        $cadastrou = $obj->cadastra();
        if (!$cadastrou) {
            return ["result" => "Cadastro não realizado."];
            $this->simpleRedirect('educar_professores_conteudo_ministrado_aee_cad.php');
        } else {
            return ["result" => "Cadastro efetuado com sucesso."];
            $this->simpleRedirect('educar_professores_conteudo_ministrado_aee_lst.php');
        }

        return ["result" => "Cadastro não realizado."];
    }

    public function getObjetivosAprendizagem ()
    {
        $componente_ministrado_aee_id = $this->getRequest()->planejamento_aula_id;
        $turma_id = $this->getRequest()->turma_id;
        $ano = $this->getRequest()->ano;

        if (is_numeric($componente_ministrado_aee_id)) {
            $obj = new clsModulesComponenteMinistradoAee($componente_ministrado_aee_id);
            $detalhePA = $obj->detalhe();

            $row = [];

            foreach ($detalhePA['componentesCurriculares'] as $key => $componenteCurricular) {
                $habilidadesPaCC = [];
                $especificacoesGeralBNCC = [];
                $especificacoesPABNCC = [];
                $planejamento_aula_bncc_ids = [];

                $habilidadesGeralCC = $this->getBNCCTurma($turma_id, $componenteCurricular['id']);

                foreach ($detalhePA['bnccs'] as $key => $bnccPA) {
                    if (array_key_exists($bnccPA["id"], $habilidadesGeralCC['bncc'])) {
                        $especificacoesGeralBNCC[$bnccPA["id"]] = $this->getEspecificacaoBNCC($bnccPA['id'])['bncc_especificacao'];
                        $habilidadesPaCC[] = $bnccPA["id"];
                        $planejamento_aula_bncc_ids[] = $bnccPA["planejamento_aula_bncc_id"];
                    }
                }

                if (!empty($planejamento_aula_bncc_ids)) {
                    $objTemp = new clsModulesPlanejamentoAulaBNCCEspecificacaoAee();
                    $especificacoesPABNCC[] = $objTemp->listaEspecificacoesByBNCCArray($planejamento_aula_bncc_ids);
                }

                $row[] = [
                    'componente_curricular_id' => $componenteCurricular['id'],
                    'habilidades' => [
                        'habilidades_planejamento_aula_cc' => $habilidadesPaCC,
                        'habilidades_geral_cc' => $habilidadesGeralCC['bncc']
                    ],
                    'especificacoes' => [
                        'especificacoes_pa_bncc' => $especificacoesPABNCC,
                        'especificacoes_geral_bncc' => $especificacoesGeralBNCC
                    ]
                ];

            }

            $row['count_objetivos'] = count($row);

            return $row;

        }

        return [];
    }

    private function getBNCCTurma($turma = null, $ref_cod_componente_curricular = null)
    {
        if (is_numeric($turma)) {
            // $obj = new clsPmieducarTurma($turma);
            // $resultado = $obj->getGrau();

            $bncc = [];
            $bncc_temp = [];
            $obj = new clsModulesBNCC();

            if ($bncc_temp = $obj->listaTurmaAee($turma, $ref_cod_componente_curricular)) {
                foreach ($bncc_temp as $bncc_item) {
                    $id = $bncc_item['id'];
                    $codigo = $bncc_item['codigo'];
                    $habilidade = $bncc_item['habilidade'];

                    $bncc[$id] = $codigo . ' - ' . $habilidade;
                }
            }

            return ['bncc' => $bncc];
        }

        return [];
    }

    private function getEspecificacaoBNCC($bncc_id = null)
    {
        if (is_numeric($bncc_id)) {
            $bncc_especificacao = [];
            $obj = new clsModulesBNCCEspecificacao();

            if ($bncc_especificacao_temp = $obj->lista($bncc_id)) {
                foreach ($bncc_especificacao_temp as $bncc_especificacao_item) {
                    $id = $bncc_especificacao_item['id'];
                    $especificacao = $bncc_especificacao_item['especificacao'];

                    $bncc_especificacao[$id] = $id . ' - ' . $especificacao;
                }
            }

            return ['bncc_especificacao' => $bncc_especificacao];
        }

        return [];
    }

    private function verificarDatasTurma($faseEtapa, $turma, $data_inicial, $data_final)
    {
        $podeRegistrar = false;
        $data_agora = new DateTime('now');
        $data_agora = new \DateTime($data_agora->format('Y-m-d'));

        $sequencia = $faseEtapa;
        $obj = new clsPmieducarTurmaModulo();

        $data = $obj->pegaPeriodoLancamentoNotasFaltas($turma, $sequencia);
        if ($data['inicio'] != null && $data['fim'] != null) {
            $data['inicio_periodo_lancamentos'] = explode(',', $data['inicio']);
            $data['fim_periodo_lancamentos'] = explode(',', $data['fim']);

            array_walk($data['inicio_periodo_lancamentos'], function (&$data_inicio, $key) {
                $data_inicio = new \DateTime($data_inicio);
            });

            array_walk($data['fim_periodo_lancamentos'], function (&$data_fim, $key) {
                $data_fim = new \DateTime($data_fim);
            });
        }

        $data['inicio'] = new \DateTime($obj->pegaEtapaSequenciaDataInicio($turma, $sequencia));
        $data['fim'] = new \DateTime($obj->pegaEtapaSequenciaDataFim($turma, $sequencia));

        if (is_array($data['inicio_periodo_lancamentos']) && is_array($data['fim_periodo_lancamentos'])) {
            for ($i = 0; $i < count($data['inicio_periodo_lancamentos']); $i++) {
                $data_inicio = $data['inicio_periodo_lancamentos'][$i];
                $data_fim = $data['fim_periodo_lancamentos'][$i];

                $podeRegistrar = $data_agora >= $data_inicio && $data_agora <= $data_fim;

                if ($podeRegistrar) break;
            }
            $podeRegistrar = $podeRegistrar && new DateTime($data_inicial) >= $data['inicio'] && new DateTime($data_final) <= $data['fim'];
        } else {
            $podeRegistrar = new DateTime($data_inicial) >= $data['inicio'] && new DateTime($data_final) <= $data['fim'];
            $podeRegistrar = $podeRegistrar && $data_agora >= $data['inicio'] && $data_agora <= $data['fim'];
        }

        return $podeRegistrar;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('post', 'verificar-plano-aula-aee-sendo-usado')) {
            $this->appendResponse($this->verificarPlanoAulaSendoUsado());
        } else if ($this->isRequestFor('post', 'verificar-plano-aula-aee-sendo-usado2')) {
            $this->appendResponse($this->verificarPlanoAulaSendoUsado2());
        } else if ($this->isRequestFor('post', 'excluir-plano-aula-aee')) {
            $this->appendResponse($this->excluirPlanoAula());
        } else if ($this->isRequestFor('post', 'editar-plano-aula-aee')) {
            $this->appendResponse($this->editarPlanoAula());
        } else if ($this->isRequestFor('post', 'novo-componente-ministrado-aee')) {
            $this->appendResponse($this->criarComponenteMinistradoAee());
        } else if ($this->isRequestFor('get', 'get-objetivos-aprendizagem')) {
            $this->appendResponse($this->getObjetivosAprendizagem());
        } else if ($this->isRequestFor('post', 'verificar-plano-aula-sendo-usado-conteudo')) {
            $this->appendResponse($this->verificarPlanoAulaSendoByConteudo());
        }
    }
}