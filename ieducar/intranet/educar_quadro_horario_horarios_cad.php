<?php

return new class extends clsCadastro {
    public $pessoa_logada;

    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $ref_ref_cod_escola;
    public $ref_cod_curso;
    public $ref_cod_curso_;
    public $ref_ref_cod_serie;
    public $ref_cod_turma;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_disciplina;
    public $dia_semana;
    public $quadro_horario;
    public $ref_cod_quadro_horario;
    public $hora_inicial;
    public $hora_final;
    public $ref_cod_instituicao_servidor;
    public $qtd_aulas;
    public $ref_cod_servidor;
    public $ref_cod_servidor_substituto_1;
    public $ref_cod_servidor_substituto_2;
    public $incluir_horario;
    public $excluir_horario;
    public $lst_matriculas;
    public $identificador;
    public $ano_alocacao;

    public $min_mat = 0;
    public $min_ves = 0;
    public $min_not = 0;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_turma          = $_GET['ref_cod_turma'];
        $this->ref_ref_cod_serie      = $_GET['ref_cod_serie'];
        $this->ref_cod_curso          = $_GET['ref_cod_curso'];
        $this->ref_cod_escola         = $_GET['ref_cod_escola'];
        $this->ref_cod_instituicao    = $_GET['ref_cod_instituicao'];
        $this->ref_cod_disciplina     = $_GET['ref_cod_disciplina'];
        $this->ref_ref_cod_serie_     = $_GET['ref_ref_cod_serie_'];
        $this->ref_cod_quadro_horario = $_GET['ref_cod_quadro_horario'];
        $this->dia_semana             = $_GET['dia_semana'];
        $this->identificador          = $_GET['identificador'];
        $this->ano_alocacao           = $_GET['ano'];

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            641,
            $this->pessoa_logada,
            7,
            "educar_quadro_horario_lst.php?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}"
        );

        if (!$_POST) {
            $obj_quadro_horarios_aux = new clsPmieducarQuadroHorarioHorariosAux();
            $obj_quadro_horarios_aux->excluirTodos($this->identificador);
        }

        if (is_numeric($this->ref_cod_turma) && is_numeric($this->ref_cod_quadro_horario)) {
            echo '<script>
              var quadro_horario = 0;
            </script>';

            $obj = new clsPmieducarQuadroHorarioHorarios();
            $lista = $obj->lista(
                $this->ref_cod_quadro_horario,
                $this->ref_ref_cod_serie,
                $this->ref_cod_escola,
                null,
                $this->ref_cod_turma,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->dia_semana
            );

            if ($lista) {
                $qtd_horario = 1;
                foreach ($lista as $campo) {
                    $this->quadro_horario[$qtd_horario]['ref_cod_quadro_horario_']       = $campo['ref_cod_quadro_horario'];
                    $this->quadro_horario[$qtd_horario]['ref_ref_cod_serie_']            = $campo['ref_cod_serie'];
                    $this->quadro_horario[$qtd_horario]['ref_ref_cod_escola_']           = $campo['ref_cod_escola'];
                    $this->quadro_horario[$qtd_horario]['ref_ref_cod_disciplina_']       = $campo['ref_cod_disciplina'];
                    $this->quadro_horario[$qtd_horario]['sequencial_']                   = $campo['sequencial'];
                    $this->quadro_horario[$qtd_horario]['ref_cod_instituicao_servidor_'] = $campo['ref_cod_instituicao_servidor'];
                    $this->quadro_horario[$qtd_horario]['ref_servidor_']                 = $campo['ref_servidor'];
                    $this->quadro_horario[$qtd_horario]['ref_servidor_substituto_']      = $campo['ref_servidor_substituto'];
                    $this->quadro_horario[$qtd_horario]['ref_cod_servidor_substituto_1_']      = $campo['ref_cod_servidor_substituto_1'];
                    $this->quadro_horario[$qtd_horario]['ref_cod_servidor_substituto_2_']      = $campo['ref_cod_servidor_substituto_2'];
                    $this->quadro_horario[$qtd_horario]['qtd_aulas_']                    = $campo['qtd_aulas'];
                    $this->quadro_horario[$qtd_horario]['hora_inicial_']                 = substr($campo['hora_inicial'], 0, 5);
                    $this->quadro_horario[$qtd_horario]['hora_final_']                   = substr($campo['hora_final'], 0, 5);
                    $this->quadro_horario[$qtd_horario]['ativo_']                        = $campo['ativo'];
                    $this->quadro_horario[$qtd_horario]['dia_semana_']                   = $campo['dia_semana'];
                    $this->quadro_horario[$qtd_horario]['qtd_horario_']                  = $qtd_horario;
                    $qtd_horario++;

                    /**
                     * salva os dados em uma tabela temporaria
                     * para realizar consulta na listagem
                     */
                    if (!$_POST['identificador']) {
                        $obj_quadro_horario = new clsPmieducarQuadroHorarioHorariosAux(
                            $campo['ref_cod_quadro_horario'],
                            null,
                            $campo['ref_cod_disciplina'],
                            $campo['ref_cod_escola'],
                            $campo['ref_cod_serie'],
                            $campo['ref_cod_instituicao_servidor'],
                            $campo['ref_servidor'],
                            $campo['dia_semana'],
                            substr($campo['hora_inicial'], 0, 5),
                            substr($campo['hora_final'], 0, 5),
                            $this->identificador,
                            $campo['ref_cod_servidor_substituto_1'],
                            $campo['ref_cod_servidor_substituto_2'],
                            $campo['qtd_aulas'],
                        );

                        $obj_quadro_horario->cadastra();
                    }
                }
            }

            if ($lista) {
                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(641, $this->pessoa_logada, 7)) {
                    if ($this->descricao) {
                        $this->fexcluir = true;
                    }
                }

                $retorno = 'Editar';
            }
        } else {
            $this->simpleRedirect('educar_quadro_horario_lst.php');
        }

        $this->url_cancelar = "educar_quadro_horario_lst.php?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' horário',
            [
                url('intranet/educar_servidores_index.php') => 'Servidores',
            ]
        );

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $desabilitado = 'disabled';

        $this->inputsHelper()->dynamic('instituicao', ['value' => $this->ref_cod_instituicao, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('escola', ['value' => $this->ref_cod_escola, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('curso', ['value' => $this->ref_cod_curso, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('serie', ['value' => $this->ref_ref_cod_serie, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic('anoLetivo', ['value' => $this->ano_alocacao, 'disabled' => $desabilitado]);

        $this->campoQuebra();

        /**
         * Campos a serem preenchidos com os dados necessários para a inclusão de horários
         */

        // foreign keys
        $opcoes_disc = ['' => 'Selecione uma disciplina'];

        // Componentes curriculares da série
        $componentesTurma = [];
        try {
            $componentesTurma = App_Model_IedFinder::getComponentesTurma(
                $this->ref_ref_cod_serie,
                $this->ref_cod_escola,
                $this->ref_cod_turma
            );
        } catch (Exception $e) {
        }

        if (0 == count($componentesTurma)) {
            $opcoes_disc = ['NULL' => 'A série dessa escola não possui componentes cadastrados'];
        } else {
            $opcoes_disc['todas_disciplinas'] = 'Todas as disciplinas';
            foreach ($componentesTurma as $componente) {
                $opcoes_disc[$componente->id] = $componente;
            }
        }

        $this->campoLista(
            'ref_cod_disciplina',
            'Componente curricular',
            $opcoes_disc,
            $this->ref_cod_disciplina,
            '',
            false,
            '',
            '',
            false,
            false
        );

        $this->campoOculto('identificador', $this->identificador);

        $opcoesDias = [
            '' => 'Selecione um dia da semana',
            1  => 'Domingo',
            2  => 'Segunda-Feira',
            3  => 'Terça-Feira',
            4  => 'Quarta-Feira',
            5  => 'Quinta-Feira',
            6  => 'Sexta-Feira',
            7  => 'Sábado'
        ];

        $this->campoOculto('dia_semana', $this->dia_semana);
        $this->campoLista(
            'dia_semana_',
            'Dia da Semana',
            $opcoesDias,
            $this->dia_semana,
            '',
            false,
            '',
            '',
            true,
            false
        );

        $this->campoHora('hora_inicial', 'Hora Inicial', $this->hora_inicial, false);
        $this->campoHora('hora_final', 'Hora Final', $this->hora_final, false);

        if (empty($this->qtd_aulas)) {
            $this->qtd_aulas = 1;
        }

        if (!empty($this->ref_ref_cod_serie)) {
            $obj = new clsPmieducarSerie();
            $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($this->ref_ref_cod_serie);

            if ($tipo_presenca == '2') {
                $this->campoNumero('qtd_aulas', 'Quantidade de Aulas', $this->qtd_aulas, 1, 1, true);
            }
        }

        $this->campoListaPesq(
            'ref_cod_servidor',
            'Servidor',
            ['' => 'Selecione um servidor'],
            $this->ref_cod_servidor,
            '',
            '',
            false,
            '',
            '',
            null,
            null,
            '',
            true,
            false,
            false
        );

        $this->campoListaPesq(
            'ref_cod_servidor_substituto_1',
            '1º Servidor Substituto',
            ['' => 'Selecione um servidor'],
            $this->ref_cod_servidor_substituto_1,
            '',
            '',
            false,
            '',
            '',
            null,
            null,
            '',
            true,
            false,
            false
        );

        $this->campoListaPesq(
            'ref_cod_servidor_substituto_2',
            '2º Servidor Substituto',
            ['' => 'Selecione um servidor'],
            $this->ref_cod_servidor_substituto_2,
            '',
            '',
            false,
            '',
            '',
            null,
            null,
            '',
            true,
            false,
            false
        );

        $this->campoRotulo(
            'bt_incluir_horario',
            'Hor&aacute;rio',
            '<a href=\'#\' id=\'btn_incluir_horario\' ><img src=\'imagens/nvp_bot_adiciona.gif\' title=\'Incluir\' border=0></a>'
        );

        $this->campoOculto('incluir_horario', '');

        /**
         * Inclui horários
         */
        if ($_POST['quadro_horario']) {
            $this->quadro_horario = unserialize(urldecode($_POST['quadro_horario']));
        }

        $qtd_horario = is_array($this->quadro_horario) ? (count($this->quadro_horario) == 0 ? 1 : count($this->quadro_horario) + 1) : 1;

        // primary keys
        if ($this->incluir_horario) {
            if (is_numeric($_POST['ref_cod_servidor']) &&
                is_string($_POST['hora_inicial']) &&
                is_string($_POST['hora_final']) &&
                is_numeric($_POST['dia_semana'])
                && is_numeric($_POST['ref_cod_disciplina'])
            ) {
                $this->quadro_horario[$qtd_horario]['ref_cod_quadro_horario_']       = $this->ref_cod_quadro_horario;
                $this->quadro_horario[$qtd_horario]['ref_ref_cod_serie_']            = $this->ref_ref_cod_serie;
                $this->quadro_horario[$qtd_horario]['ref_ref_cod_escola_']           = $this->ref_cod_escola;
                $this->quadro_horario[$qtd_horario]['ref_ref_cod_disciplina_']       = $_POST['ref_cod_disciplina'];
                $this->quadro_horario[$qtd_horario]['ref_cod_instituicao_servidor_'] = $this->ref_cod_instituicao;
                $this->quadro_horario[$qtd_horario]['ref_servidor_']                 = $_POST['ref_cod_servidor'];
                $this->quadro_horario[$qtd_horario]['ref_servidor_substituto_1_']    = $_POST['ref_cod_servidor_substituto_1'];
                $this->quadro_horario[$qtd_horario]['ref_servidor_substituto_2_']    = $_POST['ref_cod_servidor_substituto_2'];
                $this->quadro_horario[$qtd_horario]['qtd_aulas_']                    = $_POST['qtd_aulas'];
                $this->quadro_horario[$qtd_horario]['hora_inicial_']                 = $_POST['hora_inicial'];
                $this->quadro_horario[$qtd_horario]['hora_final_']                   = $_POST['hora_final'];
                $this->quadro_horario[$qtd_horario]['ativo_']                        = 1;
                $this->quadro_horario[$qtd_horario]['dia_semana_']                   = $_POST['dia_semana'];
                $this->quadro_horario[$qtd_horario]['qtd_horario_']                  = $qtd_horario;

                /**
                 * salva os dados em uma tabela temporaria
                 * para realizar consulta na listagem
                 */
                $obj_quadro_horario = new clsPmieducarQuadroHorarioHorariosAux(
                    $this->ref_cod_quadro_horario,
                    null,
                    $this->ref_cod_disciplina,
                    $this->ref_cod_escola,
                    $this->ref_ref_cod_serie,
                    $this->ref_cod_instituicao,
                    $this->ref_cod_servidor,
                    $this->dia_semana,
                    $this->hora_inicial,
                    $this->hora_final,
                    $this->identificador,
                    $this->ref_cod_servidor_substituto_1,
                    $this->ref_cod_servidor_substituto_2,
                    $this->qtd_aulas,
                );

                $obj_quadro_horario->cadastra();

                unset($this->ref_cod_servidor);
                unset($this->ref_cod_disciplina);
                unset($this->hora_inicial);
                unset($this->hora_final);

                echo '
          <script>
            window.onload = function() {
              document.getElementById(\'ref_cod_servidor\').value   = \'\';
              document.getElementById(\'ref_cod_disciplina\').value = \'\';
              document.getElementById(\'hora_inicial\').value       = \'\';
              document.getElementById(\'hora_final\').value         = \'\';
            }
          </script>';
            }
        }

        $count = is_array($this->quadro_horario) ? count($this->quadro_horario) : 0;
        echo "<script>
            quadro_horario = {$count};
        </script>";

        $this->campoOculto('excluir_horario', '');
        $qtd_horario = 1;

        $this->lst_matriculas = urldecode($this->lst_matriculas);

        $this->min_mat = $this->min_ves = $this->min_not = 0;

        if (is_array($this->quadro_horario)) {
            foreach ($this->quadro_horario as $campo) {
                if ($this->excluir_horario == $campo['qtd_horario_']) {
                    $obj_horario = new clsPmieducarQuadroHorarioHorarios();
                    $lst_horario = $obj_horario->lista(
                        $campo['ref_cod_quadro_horario_'],
                        $campo['ref_ref_cod_serie_'],
                        $campo['ref_ref_cod_escola_'],
                        $campo['ref_ref_cod_disciplina_'],
                        null,
                        null,
                        null,
                        $campo['ref_cod_instituicao_servidor_'],
                        null,
                        $campo['ref_servidor_'],
                        $campo['hora_inicial_'],
                        null,
                        $campo['hora_final_'],
                        null,
                        null,
                        null,
                        null,
                        null,
                        1,
                        $campo['dia_semana_'],
                        $campo['ref_servidor_substituto_1_'],
                        $campo['ref_servidor_substituto_2_'],
                        $campo['qtd_aulas_'],
                    );

                    if (is_array($lst_horario)) {
                        $campo['ativo_'] = 0;

                        if (isset($this->lst_matriculas)) {
                            $this->lst_matriculas .= '' . $campo['ref_servidor_'] . '';
                        } else {
                            $this->lst_matriculas .= ', ' . $campo['ref_servidor_'] . '';
                        }
                    } else {
                        $campo['ativo_'] = 2;

                        if (isset($this->lst_matriculas)) {
                            $this->lst_matriculas .= '' . $campo['ref_servidor_'] . '';
                        } else {
                            $this->lst_matriculas .= ', ' . $campo['ref_servidor_'] . '';
                        }
                    }

                    $this->excluir_horario = null;

                    $obj_horario = new clsPmieducarQuadroHorarioHorariosAux();
                    $lst_horario = $obj_horario->excluiRegistro(
                        $campo['ref_cod_quadro_horario_'],
                        $campo['ref_ref_cod_serie_'],
                        $campo['ref_ref_cod_escola_'],
                        $campo['ref_ref_cod_disciplina_'],
                        $campo['ref_cod_instituicao_servidor_'],
                        $campo['ref_servidor_'],
                        $this->identificador
                    );
                } else {
                    switch ($campo['dia_semana_']) {
                        case 1:
                            $campo['nm_dia_semana_'] = 'Domingo';
                            break;

                        case 2:
                            $campo['nm_dia_semana_'] = 'Segunda-Feira';
                            break;

                        case 3:
                            $campo['nm_dia_semana_'] = 'Terça-Feira';
                            break;

                        case 4:
                            $campo['nm_dia_semana_'] = 'Quarta-Feira';
                            break;

                        case 5:
                            $campo['nm_dia_semana_'] = 'Quinta-Feira';
                            break;

                        case 6:
                            $campo['nm_dia_semana_'] = 'Sexta-Feira';
                            break;

                        case 7:
                            $campo['nm_dia_semana_'] = 'S&aacute;bado';
                            break;
                    }
                }

                if ($campo['ativo_'] == 1) {
                    $this->campoTextoInv(
                        $campo['qtd_horario_'] . '_nm_dia_semana',
                        '',
                        $campo['nm_dia_semana_'],
                        13,
                        255,
                        false,
                        false,
                        true
                    );

                    $this->campoOculto(
                        $campo['qtd_horario_'] . '_dia_semana',
                        $campo['dia_semana_']
                    );

                    $this->campoTextoInv(
                        $campo['qtd_horario_'] . '_hora_inicial',
                        '',
                        $campo['hora_inicial_'],
                        5,
                        255,
                        false,
                        false,
                        true
                    );

                    $this->campoTextoInv(
                        $campo['qtd_horario_'] . '_hora_final',
                        '',
                        $campo['hora_final_'],
                        5,
                        255,
                        false,
                        false,
                        true
                    );

                    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
                    $componente = $componenteMapper->find($campo['ref_ref_cod_disciplina_']);

                    $this->campoTextoInv(
                        $campo['qtd_horario_'] . '_ref_cod_disciplina',
                        '',
                        $componente->nome,
                        30,
                        255,
                        false,
                        false,
                        true
                    );

                    $obj_pes = new clsPessoa_($campo['ref_servidor_']);
                    $det_pes = $obj_pes->detalhe();

                    if (is_numeric($campo['ref_servidor_substituto_'])) {
                        $this->campoTextoInv(
                            $campo['qtd_horario_'] . '_ref_cod_servidor',
                            '',
                            $det_pes['nome'],
                            30,
                            255,
                            false,
                            false,
                            false,
                            '',
                            ''
                        );
                    } else {
                        $this->campoTextoInv(
                            $campo['qtd_horario_'] . '_ref_cod_servidor',
                            '',
                            $det_pes['nome'],
                            30,
                            255,
                            false,
                            false,
                            false,
                            '',
                            "<a href='#' onclick=\"getElementById('excluir_horario').value = '{$campo['qtd_horario_']}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>"
                        );
                    }
                }

                if ($campo['ativo_'] != 2) {
                    $horarios_incluidos[$qtd_horario]['ref_cod_quadro_horario_']       = $campo['ref_cod_quadro_horario_'];
                    $horarios_incluidos[$qtd_horario]['ref_ref_cod_serie_']            = $campo['ref_ref_cod_serie_'];
                    $horarios_incluidos[$qtd_horario]['ref_ref_cod_escola_']           = $campo['ref_ref_cod_escola_'];
                    $horarios_incluidos[$qtd_horario]['ref_ref_cod_disciplina_']       = $campo['ref_ref_cod_disciplina_'];
                    $horarios_incluidos[$qtd_horario]['sequencial_']                   = $campo['sequencial_'];
                    $horarios_incluidos[$qtd_horario]['ref_cod_instituicao_servidor_'] = $campo['ref_cod_instituicao_servidor_'];
                    $horarios_incluidos[$qtd_horario]['ref_servidor_']                 = $campo['ref_servidor_'];
                    $horarios_incluidos[$qtd_horario]['ref_servidor_substituto_']      = $campo['ref_servidor_substituto_'];
                    $horarios_incluidos[$qtd_horario]['ref_servidor_substituto_1_']    = $campo['ref_servidor_substituto_1_'];
                    $horarios_incluidos[$qtd_horario]['ref_servidor_substituto_2_']    = $campo['ref_servidor_substituto_2_'];
                    $horarios_incluidos[$qtd_horario]['qtd_aulas_']                    = $campo['qtd_aulas_'];
                    $horarios_incluidos[$qtd_horario]['hora_inicial_']                 = $campo['hora_inicial_'];
                    $horarios_incluidos[$qtd_horario]['hora_final_']                   = $campo['hora_final_'];
                    $horarios_incluidos[$qtd_horario]['ativo_']                        = $campo['ativo_'];
                    $horarios_incluidos[$qtd_horario]['dia_semana_']                   = $campo['dia_semana_'];
                    $horarios_incluidos[$qtd_horario]['qtd_horario_']                  = $qtd_horario;
                    $qtd_horario++;
                }
            }

            unset($this->quadro_horario);
            $this->quadro_horario = $horarios_incluidos;
        }

        $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);
        $this->campoOculto('quadro_horario', serialize($this->quadro_horario));
        $this->campoOculto('ref_cod_curso_', $this->ref_cod_curso);
        $this->campoOculto('ano_alocacao', $this->ano_alocacao);
        $this->campoOculto('lst_matriculas', urlencode($this->lst_matriculas));
        $this->campoOculto('min_mat', $this->min_mat);
        $this->campoOculto('min_ves', $this->min_ves);
        $this->campoOculto('min_not', $this->min_not);

        $this->campoQuebra();
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            641,
            $this->pessoa_logada,
            7,
            'educar_quadro_horario_lst.php'
        );

        $this->quadro_horario = unserialize(urldecode($this->quadro_horario));

        $verifica = true;
        $parametros = '';
        if ($this->ref_cod_disciplina == 'todas_disciplinas') {
            $this->ref_cod_turma          = $_GET['ref_cod_turma'];
            $this->ref_ref_cod_serie      = $_GET['ref_cod_serie'];
            $this->ref_cod_curso          = $_GET['ref_cod_curso'];
            $this->ref_cod_escola         = $_GET['ref_cod_escola'];
            $this->ref_cod_instituicao    = $_GET['ref_cod_instituicao'];
            $this->ref_cod_quadro_horario = $_GET['ref_cod_quadro_horario'];
            $this->dia_semana             = $_GET['dia_semana'];
            $this->identificador          = $_GET['identificador'];
            $this->ref_servidor           = $_POST['ref_cod_servidor'];
            $this->ref_cod_servidor_substituto_1           = $_POST['ref_servidor_substituto_1'];
            $this->ref_cod_servidor_substituto_2           = $_POST['ref_servidor_substituto_2'];
            $this->qtd_aulas              = $_POST['qtd_aulas'];
            $this->hora_inicial           = $_POST['hora_inicial'];
            $this->hora_final             = $_POST['hora_final'];

            $componentesTurma = [];
            try {
                $componentesTurma = App_Model_IedFinder::getComponentesTurma(
                    $this->ref_ref_cod_serie,
                    $this->ref_cod_escola,
                    $this->ref_cod_turma
                );
            } catch (Exception $e) {
            }

            foreach ($componentesTurma as $componente) {
                $opcoes_disc[$componente->id] = $componente->id;
            }

            foreach ($opcoes_disc as $displina) {
                $parametros = "?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";

                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    $this->ref_cod_quadro_horario,
                    $this->ref_ref_cod_serie,
                    $this->ref_cod_escola,
                    $displina,
                    null,
                    null,
                    $this->ref_cod_instituicao,
                    null,
                    $this->ref_servidor,
                    $this->hora_inicial,
                    $this->hora_final,
                    null,
                    null,
                    1,
                    $this->dia_semana,
                    $this->ref_cod_servidor_substituto_1,
                    $this->ref_cod_servidor_substituto_2,
                    $this->qtd_aulas
                );

                $cadastrou = $obj_horario->cadastra();

                if ($cadastrou) {
                    if ($verifica) {
                        $verifica = true;
                    }
                } else {
                    $verifica = false;
                }
            }
        } else {
            foreach ($this->quadro_horario as $registro) {
                $parametros = "?ref_cod_instituicao={$registro['ref_cod_instituicao_servidor_']}&ref_cod_escola={$registro['ref_ref_cod_escola_']}&ref_cod_curso={$this->ref_cod_curso_}&ref_cod_serie={$registro['ref_ref_cod_serie_']}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";

                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    $registro['ref_cod_quadro_horario_'],
                    $registro['ref_ref_cod_serie_'],
                    $registro['ref_ref_cod_escola_'],
                    $registro['ref_ref_cod_disciplina_'],
                    null,
                    null,
                    $registro['ref_cod_instituicao_servidor_'],
                    null,
                    $registro['ref_servidor_'],
                    $registro['hora_inicial_'],
                    $registro['hora_final_'],
                    null,
                    null,
                    1,
                    $registro['dia_semana_'],
                    $registro['ref_servidor_substituto_1_'],
                    $registro['ref_servidor_substituto_2_'],
                    $registro['qtd_aulas_'],
                );

                $cadastrou = $obj_horario->cadastra();

                if ($cadastrou) {
                    if ($verifica) {
                        $verifica = true;
                    }
                } else {
                    $verifica = false;
                }
            }
        }

        if ($verifica) {
            $obj_quadro_horarios_aux = new clsPmieducarQuadroHorarioHorariosAux();
            $obj_quadro_horarios_aux->excluirTodos($this->identificador);

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect("educar_quadro_horario_lst.php{$parametros}");
        }

        $this->mensagem = 'Cadastro não realizado. 1<br>';

        return false;
    }

    public function Editar()
    {
//        $obj_permissoes = new clsPermissoes();
//        $obj_permissoes->permissao_cadastra(
//            641,
//            $this->pessoa_logada,
//            7,
//            'educar_quadro_horario_lst.php'
//        );

        $this->quadro_horario = unserialize(urldecode($this->quadro_horario));

        $verifica = true;
        $parametros = '';

        if ($this->ref_cod_disciplina == 'todas_disciplinas') {
            $this->ref_cod_turma          = $_GET['ref_cod_turma'];
            $this->ref_ref_cod_serie      = $_GET['ref_cod_serie'];
            $this->ref_cod_curso          = $_GET['ref_cod_curso'];
            $this->ref_cod_escola         = $_GET['ref_cod_escola'];
            $this->ref_cod_instituicao    = $_GET['ref_cod_instituicao'];
            $this->ref_cod_quadro_horario = $_GET['ref_cod_quadro_horario'];
            $this->dia_semana             = $_GET['dia_semana'];
            $this->identificador          = $_GET['identificador'];
            $this->ref_servidor           = $_POST['ref_cod_servidor'];
            $this->ref_cod_servidor_substituto_1           = $_POST['ref_servidor_substituto_1'];
            $this->ref_cod_servidor_substituto_2           = $_POST['ref_servidor_substituto_2'];
            $this->qtd_aulas              = $_POST['qtd_aulas'];
            $this->hora_inicial           = $_POST['hora_inicial'];
            $this->hora_final             = $_POST['hora_final'];

            $componentesTurma = [];
            try {
                $componentesTurma = App_Model_IedFinder::getComponentesTurma(
                    $this->ref_ref_cod_serie,
                    $this->ref_cod_escola,
                    $this->ref_cod_turma
                );
            } catch (Exception $e) {
            }

            foreach ($componentesTurma as $componente) {
                $opcoes_disc[$componente->id] = $componente->id;
            }
            foreach ($opcoes_disc as $displina) {
                $parametros = "?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";

                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    $this->ref_cod_quadro_horario,
                    $this->ref_ref_cod_serie,
                    $this->ref_cod_escola,
                    $displina,
                    null,
                    null,
                    $this->ref_cod_instituicao,
                    null,
                    $this->ref_servidor,
                    $this->hora_inicial,
                    $this->hora_final,
                    null,
                    null,
                    1,
                    $this->dia_semana,
                    $this->ref_cod_servidor_substituto_1,
                    $this->ref_cod_servidor_substituto_2,
                    $this->qtd_aulas
                );

                $cadastrou = $obj_horario->cadastra();

                if ($cadastrou) {
                    if ($verifica) {
                        $verifica = true;
                    }
                } else {
                    $verifica = false;
                }
            }
        } elseif (is_array($this->quadro_horario)) {
            foreach ($this->quadro_horario as $registro) {
                $parametros  = "?ref_cod_instituicao={$registro['ref_cod_instituicao_servidor_']}&ref_cod_escola={$registro['ref_ref_cod_escola_']}&ref_cod_curso={$this->ref_cod_curso_}&ref_cod_serie={$registro['ref_ref_cod_serie_']}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";
                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    $registro['ref_cod_quadro_horario_'],
                    $registro['ref_ref_cod_serie_'],
                    $registro['ref_ref_cod_escola_'],
                    $registro['ref_ref_cod_disciplina_'],
                    $registro['sequencial_'],
                    null,
                    $registro['ref_cod_instituicao_servidor_'],
                    null,
                    $registro['ref_servidor_'],
                    null,
                    null,
                    null,
                    null,
                    $registro['ativo_'],
                    null,
                    $registro['ref_servidor_substituto_1_'],
                    $registro['ref_servidor_substituto_2_'],
                    $registro['qtd_aulas_']
                );

                if ($obj_horario->detalhe()) {
                    $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                        $registro['ref_cod_quadro_horario_'],
                        $registro['ref_ref_cod_serie_'],
                        $registro['ref_ref_cod_escola_'],
                        $registro['ref_ref_cod_disciplina_'],
                        $registro['sequencial_'],
                        null,
                        $registro['ref_cod_instituicao_servidor_'],
                        null,
                        $registro['ref_servidor_'],
                        $registro['hora_inicial_'],
                        $registro['hora_final_'],
                        null,
                        null,
                        $registro['ativo_'],
                        $registro['dia_semana_'],
                        $registro['ref_servidor_substituto_1_'],
                        $registro['ref_servidor_substituto_2_'],
                        $registro['qtd_aulas_']
                    );

                    $editou = $obj_horario->edita();

                    if ($editou) {
                        if ($verifica) {
                            $verifica = true;
                        }
                    } else {
                        $verifica = false;
                    }
                } else {
                    $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                        $registro['ref_cod_quadro_horario_'],
                        $registro['ref_ref_cod_serie_'],
                        $registro['ref_ref_cod_escola_'],
                        $registro['ref_ref_cod_disciplina_'],
                        null,
                        null,
                        $registro['ref_cod_instituicao_servidor_'],
                        null,
                        $registro['ref_servidor_'],
                        $registro['hora_inicial_'],
                        $registro['hora_final_'],
                        null,
                        null,
                        $registro['ativo_'],
                        $registro['dia_semana_'],
                        $registro['ref_servidor_substituto_1_'],
                        $registro['ref_servidor_substituto_2_'],
                        $registro['qtd_aulas_']
                    );

                    $cadastrou = $obj_horario->cadastra();

                    if ($cadastrou) {
                        if ($verifica) {
                            $verifica = true;
                        }
                    } else {
                        $verifica = false;
                    }
                }
            }
        }

        if ($verifica) {
            $obj_quadro_horarios_aux = new clsPmieducarQuadroHorarioHorariosAux();
            $obj_quadro_horarios_aux->excluirTodos($this->identificador);

            $this->mensagem .= 'Cadastro editado com sucesso.<br>';
            $this->simpleRedirect("educar_quadro_horario_lst.php{$parametros}");
        }

        $this->mensagem = 'Cadastro não editado.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(
            641,
            $this->pessoa_logada,
            7,
            'educar_calendario_dia_lst.php'
        );

        $obj = new clsPmieducarCalendarioDia(
            $this->ref_cod_calendario_ano_letivo,
            $this->mes,
            $this->dia,
            $this->pessoa_logada,
            $this->pessoa_logada,
            'NULL',
            'NULL',
            $this->data_cadastro,
            $this->data_exclusao,
            1
        );

        $excluiu = $obj->edita();

        if ($excluiu) {
            $obj_quadro_horarios_aux = new clsPmieducarQuadroHorarioHorariosAux();
            $obj_quadro_horarios_aux->excluirTodos($this->identificador);

            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect("educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}");
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-quadro-horario-horarios-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Servidores - Cadastro de Horários';
        $this->processoAp = '641';
    }
};
