<?php

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $ref_cod_escola;
    public $tipo_acao;
    public $ano;

    public function Inicializar()
    {
        $retorno = 'Novo';

        /**
         * verifica permissao para realizar operacao
         */
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7, 'educar_escola_lst.php');
        /**
         * Somente inicia ano por POST
         */
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

        $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo($this->ref_cod_escola, $this->ano, null, null, null, null, null, null);
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

        $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo($this->ref_cod_escola, $this->ano, $this->pessoa_logada, $this->pessoa_logada, 1, null, null, 1);
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
        $existe_matricula_andamento_com_curso = $obj_matriculas->lista(null, null, $this->ref_cod_escola, null, null, null, null, 3, null, null, null, null, 1, $this->ano, null, null, 1, null, null, null, null, null, null, null, null, false);

        if ($existe_matricula_andamento_com_curso) {
            echo "<script>
                    alert('Não foi possível finalizar o ano letivo existem matrículas em andamento!');
                    window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}';
                  </script>";
        }

        $obj_matriculas = new clsPmieducarMatricula(null, null, $this->ref_cod_escola, null, $this->pessoa_logada, null, null, null, null, null, 1, $this->ano);
        $existe_matricula_andamento = $obj_matriculas->lista(null, null, $this->ref_cod_escola, null, null, null, null, 3, null, null, null, null, 1, $this->ano, null, null, 1, null, null, null, null, null, null, null, null, true);
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

        $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo($this->ref_cod_escola, $this->ano, $this->pessoa_logada, $this->pessoa_logada, 2, null, null, 1);
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
