<?php

return new class extends clsCadastro {
    public $pessoa_logada;
    public $ref_cod_escola;
    public $tipo_acao;
    public $ano;

    public function Inicializar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: 'educar_escola_lst.php');

        if (!$_POST) {
            $this->simpleRedirect('educar_escola_lst.php');
        }

        foreach ($_POST as $key => $value) {
            $this->$key = $value;
        }

        /**
         *  Os 3 campos devem estar preenchidos para poder realizar acao
         */
        if (!$this->ref_cod_escola || !$this->tipo_acao || !$this->ano) {
            $this->simpleRedirect('educar_escola_lst.php');
        }

        if (strtolower($this->tipo_acao) == 'editar') {
            $this->simpleRedirect("educar_ano_letivo_modulo_cad.php?ref_cod_escola={$this->ref_cod_escola}&ano={$this->ano}&referrer=educar_escola_det.php");
        }

        /**
         * verifica se existe ano letivo
         */

        $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo(ref_cod_escola: $this->ref_cod_escola, ano: $this->ano);
        $det_ano = $obj_ano_letivo->detalhe();

        if (!$obj_ano_letivo->detalhe()) {
            $this->simpleRedirect('educar_escola_lst.php');
        }

        /**
         * verifica se ano letivo da escola nao possui nenhuma matricula
         */

        if ($this->tipo_acao == 'iniciar' && $det_ano['andamento'] == 0) {
            $this->iniciarAnoLetivo();
        } elseif ($this->tipo_acao == 'reabrir') {
            $this->iniciarAnoLetivo();
        } elseif ($this->tipo_acao == 'finalizar'  && $det_ano['andamento'] == 1) {
            $this->finalizarAnoLetivo();
        } else {
            $this->simpleRedirect("educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo'");
        }
    }

    public function iniciarAnoLetivo()
    {
        /**
         *  INICIALIZA ano letivo
         */

        $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo(ref_cod_escola: $this->ref_cod_escola, ano: $this->ano, ref_usuario_cad: $this->pessoa_logada, ref_usuario_exc: $this->pessoa_logada, andamento: 1, data_cadastro: null, data_exclusao: null, ativo: 1);
        if (!$obj_ano_letivo->edita()) {
            echo "<script>
                    alert('Erro ao finalizar o ano letivo!');
                    window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
                  </script>";
        } else {
            echo "<script>
                    alert('Ano letivo inicializado com sucesso!');
                    window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
                  </script>";
        }
    }

    public function finalizarAnoLetivo()
    {
        /**
         * VERIFICA se nï¿½o existem matriculas em andamento
         */

        $obj_matriculas = new clsPmieducarMatricula();
        $existe_matricula_andamento_com_curso = $obj_matriculas->lista(int_cod_matricula: null, int_ref_cod_reserva_vaga: null, int_ref_ref_cod_escola: $this->ref_cod_escola, int_ref_ref_cod_serie: null, int_ref_usuario_exc: null, int_ref_usuario_cad: null, ref_cod_aluno: null, int_aprovado: 3, date_data_cadastro_ini: null, date_data_cadastro_fim: null, date_data_exclusao_ini: null, date_data_exclusao_fim: null, int_ativo: 1, int_ano: $this->ano, int_ref_cod_curso2: null, int_ref_cod_instituicao: null, int_ultima_matricula: 1, int_modulo: null, int_padrao_ano_escolar: null, int_analfabeto: null, int_formando: null, str_descricao_reclassificacao: null, int_matricula_reclassificacao: null, boo_com_deficiencia: null, int_ref_cod_curso: null, bool_curso_sem_avaliacao: false);

        if ($existe_matricula_andamento_com_curso) {
            echo "<script>
                    alert('Não foi possível finalizar o ano letivo existem matrículas em andamento!');
                    window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}';
                  </script>";
        }

        $obj_matriculas = new clsPmieducarMatricula(cod_matricula: null, ref_cod_reserva_vaga: null, ref_ref_cod_escola: $this->ref_cod_escola, ref_ref_cod_serie: null, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, ref_cod_aluno: null, aprovado: null, data_cadastro: null, data_exclusao: null, ativo: 1, ano: $this->ano);
        $existe_matricula_andamento = $obj_matriculas->lista(int_cod_matricula: null, int_ref_cod_reserva_vaga: null, int_ref_ref_cod_escola: $this->ref_cod_escola, int_ref_ref_cod_serie: null, int_ref_usuario_exc: null, int_ref_usuario_cad: null, ref_cod_aluno: null, int_aprovado: 3, date_data_cadastro_ini: null, date_data_cadastro_fim: null, date_data_exclusao_ini: null, date_data_exclusao_fim: null, int_ativo: 1, int_ano: $this->ano, int_ref_cod_curso2: null, int_ref_cod_instituicao: null, int_ultima_matricula: 1, int_modulo: null, int_padrao_ano_escolar: null, int_analfabeto: null, int_formando: null, str_descricao_reclassificacao: null, int_matricula_reclassificacao: null, boo_com_deficiencia: null, int_ref_cod_curso: null, bool_curso_sem_avaliacao: true);
        if ($existe_matricula_andamento) {
            // REVER CHAMADA DE MÉTODO, NÃO FAZ SENTIDO, ESTÁ COLOCANDO TODOS ALUNOS COMO APROVADOS SEM NENHUM FILTRO
            //$editou = $obj_matriculas->aprova_matricula_andamento_curso_sem_avaliacao();
            if (!editou) {
                echo "<script>
                        alert('Não foi possível finalizar o ano letivo.\\nErro ao editar matriculas de curso sem avaliação!');
                        window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}';
                      </script>";
            }
        }

        /**
         *  FINALIZA ano letivo
         */

        $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo(ref_cod_escola: $this->ref_cod_escola, ano: $this->ano, ref_usuario_cad: $this->pessoa_logada, ref_usuario_exc: $this->pessoa_logada, andamento: 2, data_cadastro: null, data_exclusao: null, ativo: 1);
        if (!$obj_ano_letivo->edita()) {
            echo "<script>
                    alert('Erro ao finalizar o ano letivo!');
                    window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
                  </script>";
        } else {
            echo "<script>
                    alert('Ano letivo finalizado com sucesso!');
                    window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
                  </script>";
        }
    }

    public function Formular()
    {
        $this->title = 'Iniciar/Finalizar Ano Letivo';
        $this->processoAp = '561';
    }
};
