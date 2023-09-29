<?php

use App\Models\LegacyCalendarDay;

return new class extends clsCadastro
{
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

    public $ref_cod_servidor;

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

        $this->ref_cod_turma = $_GET['ref_cod_turma'];
        $this->ref_ref_cod_serie = $_GET['ref_cod_serie'];
        $this->ref_cod_curso = $_GET['ref_cod_curso'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];
        $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
        $this->ref_cod_disciplina = $_GET['ref_cod_disciplina'];
        $this->ref_ref_cod_serie_ = $_GET['ref_ref_cod_serie_'];
        $this->ref_cod_quadro_horario = $_GET['ref_cod_quadro_horario'];
        $this->dia_semana = $_GET['dia_semana'];
        $this->identificador = $_GET['identificador'];
        $this->ano_alocacao = $_GET['ano'];

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 641,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: "educar_quadro_horario_lst.php?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}"
        );

        if (!$_POST) {
            $obj_quadro_horarios_aux = new clsPmieducarQuadroHorarioHorariosAux();
            $obj_quadro_horarios_aux->excluirTodos(identificador: $this->identificador);
        }

        if (is_numeric(value: $this->ref_cod_turma) && is_numeric(value: $this->ref_cod_quadro_horario)) {
            echo '<script>
              var quadro_horario = 0;
            </script>';

            $obj = new clsPmieducarQuadroHorarioHorarios();
            $lista = $obj->lista(
                int_ref_cod_quadro_horario: $this->ref_cod_quadro_horario,
                int_ref_ref_cod_serie: $this->ref_ref_cod_serie,
                int_ref_ref_cod_escola: $this->ref_cod_escola,
                int_ref_ref_cod_turma: $this->ref_cod_turma,
                int_dia_semana: $this->dia_semana
            );

            if ($lista) {
                $qtd_horario = 1;
                foreach ($lista as $campo) {
                    $this->quadro_horario[$qtd_horario]['ref_cod_quadro_horario_'] = $campo['ref_cod_quadro_horario'];
                    $this->quadro_horario[$qtd_horario]['ref_ref_cod_serie_'] = $campo['ref_cod_serie'];
                    $this->quadro_horario[$qtd_horario]['ref_ref_cod_escola_'] = $campo['ref_cod_escola'];
                    $this->quadro_horario[$qtd_horario]['ref_ref_cod_disciplina_'] = $campo['ref_cod_disciplina'];
                    $this->quadro_horario[$qtd_horario]['sequencial_'] = $campo['sequencial'];
                    $this->quadro_horario[$qtd_horario]['ref_cod_instituicao_servidor_'] = $campo['ref_cod_instituicao_servidor'];
                    $this->quadro_horario[$qtd_horario]['ref_servidor_'] = $campo['ref_servidor'];
                    $this->quadro_horario[$qtd_horario]['ref_servidor_substituto_'] = $campo['ref_servidor_substituto'];
                    $this->quadro_horario[$qtd_horario]['hora_inicial_'] = substr(string: $campo['hora_inicial'], offset: 0, length: 5);
                    $this->quadro_horario[$qtd_horario]['hora_final_'] = substr(string: $campo['hora_final'], offset: 0, length: 5);
                    $this->quadro_horario[$qtd_horario]['ativo_'] = $campo['ativo'];
                    $this->quadro_horario[$qtd_horario]['dia_semana_'] = $campo['dia_semana'];
                    $this->quadro_horario[$qtd_horario]['qtd_horario_'] = $qtd_horario;
                    $qtd_horario++;

                    /**
                     * salva os dados em uma tabela temporaria
                     * para realizar consulta na listagem
                     */
                    if (!$_POST['identificador']) {
                        $obj_quadro_horario = new clsPmieducarQuadroHorarioHorariosAux(
                            ref_cod_quadro_horario: $campo['ref_cod_quadro_horario'],
                            sequencial: null,
                            ref_cod_disciplina: $campo['ref_cod_disciplina'],
                            ref_cod_escola: $campo['ref_cod_escola'],
                            ref_cod_serie: $campo['ref_cod_serie'],
                            ref_cod_instituicao_servidor: $campo['ref_cod_instituicao_servidor'],
                            ref_servidor: $campo['ref_servidor'],
                            dia_semana: $campo['dia_semana'],
                            hora_inicial: substr(string: $campo['hora_inicial'], offset: 0, length: 5),
                            hora_final: substr(string: $campo['hora_final'], offset: 0, length: 5),
                            identificador: $this->identificador
                        );

                        $obj_quadro_horario->cadastra();
                    }
                }
            }

            if ($lista) {
                $obj_permissoes = new clsPermissoes();

                if ($obj_permissoes->permissao_excluir(int_processo_ap: 641, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    if ($this->descricao) {
                        $this->fexcluir = true;
                    }
                }

                $retorno = 'Editar';
            }
        } else {
            $this->simpleRedirect(url: 'educar_quadro_horario_lst.php');
        }

        $this->url_cancelar = "educar_quadro_horario_lst.php?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb(currentPage: $nomeMenu . ' horário',
            breadcrumbs: [
                url(path: 'intranet/educar_servidores_index.php') => 'Servidores',
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

        $this->inputsHelper()->dynamic(helperNames: 'instituicao', inputOptions: ['value' => $this->ref_cod_instituicao, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic(helperNames: 'escola', inputOptions: ['value' => $this->ref_cod_escola, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic(helperNames: 'curso', inputOptions: ['value' => $this->ref_cod_curso, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic(helperNames: 'serie', inputOptions: ['value' => $this->ref_ref_cod_serie, 'disabled' => $desabilitado]);
        $this->inputsHelper()->dynamic(helperNames: 'anoLetivo', inputOptions: ['value' => $this->ano_alocacao, 'disabled' => $desabilitado]);

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
                serieId: $this->ref_ref_cod_serie,
                escola: $this->ref_cod_escola,
                turma: $this->ref_cod_turma
            );
        } catch (Exception) {
        }

        if (count(value: $componentesTurma) == 0) {
            $opcoes_disc = ['NULL' => 'A série dessa escola não possui componentes cadastrados'];
        } else {
            $opcoes_disc['todas_disciplinas'] = 'Todas as disciplinas';
            foreach ($componentesTurma as $componente) {
                $opcoes_disc[$componente->id] = $componente;
            }
        }

        $this->campoLista(
            nome: 'ref_cod_disciplina',
            campo: 'Componente curricular',
            valor: $opcoes_disc,
            default: $this->ref_cod_disciplina,
            obrigatorio: false
        );

        $this->campoOculto(nome: 'identificador', valor: $this->identificador);

        $opcoesDias = [
            '' => 'Selecione um dia da semana',
            1 => 'Domingo',
            2 => 'Segunda-Feira',
            3 => 'Terça-Feira',
            4 => 'Quarta-Feira',
            5 => 'Quinta-Feira',
            6 => 'Sexta-Feira',
            7 => 'Sábado',
        ];

        $this->campoOculto(nome: 'dia_semana', valor: $this->dia_semana);
        $this->campoLista(
            nome: 'dia_semana_',
            campo: 'Dia da Semana',
            valor: $opcoesDias,
            default: $this->dia_semana,
            desabilitado: true,
            obrigatorio: false
        );

        $this->campoHora(nome: 'hora_inicial', campo: 'Hora Inicial', valor: $this->hora_inicial);
        $this->campoHora(nome: 'hora_final', campo: 'Hora Final', valor: $this->hora_final);

        $this->campoListaPesq(
            nome: 'ref_cod_servidor',
            campo: 'Servidor',
            valor: ['' => 'Selecione um servidor'],
            default: $this->ref_cod_servidor,
            div: true
        );

        $this->campoRotulo(
            nome: 'bt_incluir_horario',
            campo: 'Horário',
            valor: '<a href=\'#\' id=\'btn_incluir_horario\' ><img src=\'imagens/nvp_bot_adiciona.gif\' title=\'Incluir\' border=0></a>'
        );

        $this->campoOculto(nome: 'incluir_horario', valor: '');

        /**
         * Inclui horários
         */
        if ($_POST['quadro_horario']) {
            $this->quadro_horario = unserialize(data: urldecode(string: $_POST['quadro_horario']));
        }

        $qtd_horario = is_array(value: $this->quadro_horario) ? (count(value: $this->quadro_horario) == 0 ? 1 : count(value: $this->quadro_horario) + 1) : 1;

        // primary keys
        if ($this->incluir_horario) {
            if (is_numeric(value: $_POST['ref_cod_servidor']) &&
                is_string(value: $_POST['hora_inicial']) &&
                is_string(value: $_POST['hora_final']) &&
                is_numeric(value: $_POST['dia_semana'])
                && is_numeric(value: $_POST['ref_cod_disciplina'])
            ) {
                $this->quadro_horario[$qtd_horario]['ref_cod_quadro_horario_'] = $this->ref_cod_quadro_horario;
                $this->quadro_horario[$qtd_horario]['ref_ref_cod_serie_'] = $this->ref_ref_cod_serie;
                $this->quadro_horario[$qtd_horario]['ref_ref_cod_escola_'] = $this->ref_cod_escola;
                $this->quadro_horario[$qtd_horario]['ref_ref_cod_disciplina_'] = $_POST['ref_cod_disciplina'];
                $this->quadro_horario[$qtd_horario]['ref_cod_instituicao_servidor_'] = $this->ref_cod_instituicao;
                $this->quadro_horario[$qtd_horario]['ref_servidor_'] = $_POST['ref_cod_servidor'];
                $this->quadro_horario[$qtd_horario]['ref_servidor_substituto_'] = $_POST['ref_servidor_substituto'];
                $this->quadro_horario[$qtd_horario]['hora_inicial_'] = $_POST['hora_inicial'];
                $this->quadro_horario[$qtd_horario]['hora_final_'] = $_POST['hora_final'];
                $this->quadro_horario[$qtd_horario]['ativo_'] = 1;
                $this->quadro_horario[$qtd_horario]['dia_semana_'] = $_POST['dia_semana'];
                $this->quadro_horario[$qtd_horario]['qtd_horario_'] = $qtd_horario;

                /**
                 * salva os dados em uma tabela temporaria
                 * para realizar consulta na listagem
                 */
                $obj_quadro_horario = new clsPmieducarQuadroHorarioHorariosAux(
                    ref_cod_quadro_horario: $this->ref_cod_quadro_horario,
                    sequencial: null,
                    ref_cod_disciplina: $this->ref_cod_disciplina,
                    ref_cod_escola: $this->ref_cod_escola,
                    ref_cod_serie: $this->ref_ref_cod_serie,
                    ref_cod_instituicao_servidor: $this->ref_cod_instituicao,
                    ref_servidor: $this->ref_cod_servidor,
                    dia_semana: $this->dia_semana,
                    hora_inicial: $this->hora_inicial,
                    hora_final: $this->hora_final,
                    identificador: $this->identificador
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

        $count = is_array(value: $this->quadro_horario) ? count(value: $this->quadro_horario) : 0;
        echo "<script>
            quadro_horario = {$count};
        </script>";

        $this->campoOculto(nome: 'excluir_horario', valor: '');
        $qtd_horario = 1;

        $this->lst_matriculas = urldecode(string: $this->lst_matriculas);

        $this->min_mat = $this->min_ves = $this->min_not = 0;

        if (is_array(value: $this->quadro_horario)) {
            foreach ($this->quadro_horario as $campo) {
                if ($this->excluir_horario == $campo['qtd_horario_']) {
                    $obj_horario = new clsPmieducarQuadroHorarioHorarios();
                    $lst_horario = $obj_horario->lista(
                        int_ref_cod_quadro_horario: $campo['ref_cod_quadro_horario_'],
                        int_ref_ref_cod_serie: $campo['ref_ref_cod_serie_'],
                        int_ref_ref_cod_escola: $campo['ref_ref_cod_escola_'],
                        int_ref_ref_cod_disciplina: $campo['ref_ref_cod_disciplina_'],
                        int_ref_cod_instituicao_servidor: $campo['ref_cod_instituicao_servidor_'],
                        int_ref_servidor: $campo['ref_servidor_'],
                        time_hora_inicial_ini: $campo['hora_inicial_'],
                        time_hora_final_ini: $campo['hora_final_'],
                        int_ativo: 1,
                        int_dia_semana: $campo['dia_semana_']
                    );

                    if (is_array(value: $lst_horario)) {
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
                        ref_cod_quadro_horario: $campo['ref_cod_quadro_horario_'],
                        ref_cod_serie: $campo['ref_ref_cod_serie_'],
                        ref_cod_escola: $campo['ref_ref_cod_escola_'],
                        ref_cod_disciplina: $campo['ref_ref_cod_disciplina_'],
                        ref_cod_instituicao_servidor: $campo['ref_cod_instituicao_servidor_'],
                        ref_servidor: $campo['ref_servidor_'],
                        identificador: $this->identificador
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
                            $campo['nm_dia_semana_'] = 'Sábado';
                            break;
                    }
                }

                if ($campo['ativo_'] == 1) {
                    $this->campoTextoInv(
                        nome: $campo['qtd_horario_'] . '_nm_dia_semana',
                        campo: '',
                        valor: $campo['nm_dia_semana_'],
                        tamanhovisivel: 13,
                        tamanhomaximo: 255,
                        duplo: true
                    );

                    $this->campoOculto(
                        nome: $campo['qtd_horario_'] . '_dia_semana',
                        valor: $campo['dia_semana_']
                    );

                    $this->campoTextoInv(
                        nome: $campo['qtd_horario_'] . '_hora_inicial',
                        campo: '',
                        valor: $campo['hora_inicial_'],
                        tamanhovisivel: 5,
                        tamanhomaximo: 255,
                        duplo: true
                    );

                    $this->campoTextoInv(
                        nome: $campo['qtd_horario_'] . '_hora_final',
                        campo: '',
                        valor: $campo['hora_final_'],
                        tamanhovisivel: 5,
                        tamanhomaximo: 255,
                        duplo: true
                    );

                    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
                    $componente = $componenteMapper->find(pkey: $campo['ref_ref_cod_disciplina_']);

                    $this->campoTextoInv(
                        nome: $campo['qtd_horario_'] . '_ref_cod_disciplina',
                        campo: '',
                        valor: $componente->nome,
                        tamanhovisivel: 30,
                        tamanhomaximo: 255,
                        duplo: true
                    );

                    $obj_pes = new clsPessoa_(int_idpes: $campo['ref_servidor_']);
                    $det_pes = $obj_pes->detalhe();

                    if (is_numeric(value: $campo['ref_servidor_substituto_'])) {
                        $this->campoTextoInv(
                            nome: $campo['qtd_horario_'] . '_ref_cod_servidor',
                            campo: '',
                            valor: $det_pes['nome'],
                            tamanhovisivel: 30,
                            tamanhomaximo: 255
                        );
                    } else {
                        $this->campoTextoInv(
                            nome: $campo['qtd_horario_'] . '_ref_cod_servidor',
                            campo: '',
                            valor: $det_pes['nome'],
                            tamanhovisivel: 30,
                            tamanhomaximo: 255,
                            descricao2: "<a href='#' onclick=\"getElementById('excluir_horario').value = '{$campo['qtd_horario_']}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>"
                        );
                    }
                }

                if ($campo['ativo_'] != 2) {
                    $horarios_incluidos[$qtd_horario]['ref_cod_quadro_horario_'] = $campo['ref_cod_quadro_horario_'];
                    $horarios_incluidos[$qtd_horario]['ref_ref_cod_serie_'] = $campo['ref_ref_cod_serie_'];
                    $horarios_incluidos[$qtd_horario]['ref_ref_cod_escola_'] = $campo['ref_ref_cod_escola_'];
                    $horarios_incluidos[$qtd_horario]['ref_ref_cod_disciplina_'] = $campo['ref_ref_cod_disciplina_'];
                    $horarios_incluidos[$qtd_horario]['sequencial_'] = $campo['sequencial_'];
                    $horarios_incluidos[$qtd_horario]['ref_cod_instituicao_servidor_'] = $campo['ref_cod_instituicao_servidor_'];
                    $horarios_incluidos[$qtd_horario]['ref_servidor_'] = $campo['ref_servidor_'];
                    $horarios_incluidos[$qtd_horario]['ref_servidor_substituto_'] = $campo['ref_servidor_substituto_'];
                    $horarios_incluidos[$qtd_horario]['hora_inicial_'] = $campo['hora_inicial_'];
                    $horarios_incluidos[$qtd_horario]['hora_final_'] = $campo['hora_final_'];
                    $horarios_incluidos[$qtd_horario]['ativo_'] = $campo['ativo_'];
                    $horarios_incluidos[$qtd_horario]['dia_semana_'] = $campo['dia_semana_'];
                    $horarios_incluidos[$qtd_horario]['qtd_horario_'] = $qtd_horario;
                    $qtd_horario++;
                }
            }

            unset($this->quadro_horario);
            $this->quadro_horario = $horarios_incluidos;
        }

        $this->campoOculto(nome: 'ref_cod_turma', valor: $this->ref_cod_turma);
        $this->campoOculto(nome: 'quadro_horario', valor: serialize(value: $this->quadro_horario));
        $this->campoOculto(nome: 'ref_cod_curso_', valor: $this->ref_cod_curso);
        $this->campoOculto(nome: 'ano_alocacao', valor: $this->ano_alocacao);
        $this->campoOculto(nome: 'lst_matriculas', valor: urlencode(string: $this->lst_matriculas));
        $this->campoOculto(nome: 'min_mat', valor: $this->min_mat);
        $this->campoOculto(nome: 'min_ves', valor: $this->min_ves);
        $this->campoOculto(nome: 'min_not', valor: $this->min_not);

        $this->campoQuebra();
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 641,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_quadro_horario_lst.php'
        );

        $this->quadro_horario = unserialize(data: urldecode(string: $this->quadro_horario));

        $verifica = true;
        $parametros = '';
        if ($this->ref_cod_disciplina == 'todas_disciplinas') {
            $this->ref_cod_turma = $_GET['ref_cod_turma'];
            $this->ref_ref_cod_serie = $_GET['ref_cod_serie'];
            $this->ref_cod_curso = $_GET['ref_cod_curso'];
            $this->ref_cod_escola = $_GET['ref_cod_escola'];
            $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
            $this->ref_cod_quadro_horario = $_GET['ref_cod_quadro_horario'];
            $this->dia_semana = $_GET['dia_semana'];
            $this->identificador = $_GET['identificador'];
            $this->ref_servidor = $_POST['ref_cod_servidor'];
            $this->hora_inicial = $_POST['hora_inicial'];
            $this->hora_final = $_POST['hora_final'];

            $componentesTurma = [];
            try {
                $componentesTurma = App_Model_IedFinder::getComponentesTurma(
                    serieId: $this->ref_ref_cod_serie,
                    escola: $this->ref_cod_escola,
                    turma: $this->ref_cod_turma
                );
            } catch (Exception) {
            }

            foreach ($componentesTurma as $componente) {
                $opcoes_disc[$componente->id] = $componente->id;
            }

            foreach ($opcoes_disc as $displina) {
                $parametros = "?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";

                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    ref_cod_quadro_horario: $this->ref_cod_quadro_horario,
                    ref_ref_cod_serie: $this->ref_ref_cod_serie,
                    ref_ref_cod_escola: $this->ref_cod_escola,
                    ref_ref_cod_disciplina: $displina,
                    sequencial: null,
                    ref_cod_instituicao_substituto: null,
                    ref_cod_instituicao_servidor: $this->ref_cod_instituicao,
                    ref_servidor_substituto: null,
                    ref_servidor: $this->ref_servidor,
                    hora_inicial: $this->hora_inicial,
                    hora_final: $this->hora_final,
                    data_cadastro: null,
                    data_exclusao: null,
                    ativo: 1,
                    dia_semana: $this->dia_semana
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
                    ref_cod_quadro_horario: $registro['ref_cod_quadro_horario_'],
                    ref_ref_cod_serie: $registro['ref_ref_cod_serie_'],
                    ref_ref_cod_escola: $registro['ref_ref_cod_escola_'],
                    ref_ref_cod_disciplina: $registro['ref_ref_cod_disciplina_'],
                    ref_cod_instituicao_servidor: $registro['ref_cod_instituicao_servidor_'],
                    ref_servidor: $registro['ref_servidor_'],
                    hora_inicial: $registro['hora_inicial_'],
                    hora_final: $registro['hora_final_'],
                    ativo: 1,
                    dia_semana: $registro['dia_semana_']
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
            $obj_quadro_horarios_aux->excluirTodos(identificador: $this->identificador);

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect(url: "educar_quadro_horario_lst.php{$parametros}");
        }

        $this->mensagem = 'Cadastro não realizado. 1<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 641,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_quadro_horario_lst.php'
        );

        $this->quadro_horario = unserialize(data: urldecode(string: $this->quadro_horario));

        $verifica = true;
        $parametros = '';

        if ($this->ref_cod_disciplina == 'todas_disciplinas') {
            $this->ref_cod_turma = $_GET['ref_cod_turma'];
            $this->ref_ref_cod_serie = $_GET['ref_cod_serie'];
            $this->ref_cod_curso = $_GET['ref_cod_curso'];
            $this->ref_cod_escola = $_GET['ref_cod_escola'];
            $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
            $this->ref_cod_quadro_horario = $_GET['ref_cod_quadro_horario'];
            $this->dia_semana = $_GET['dia_semana'];
            $this->identificador = $_GET['identificador'];
            $this->ref_servidor = $_POST['ref_cod_servidor'];
            $this->hora_inicial = $_POST['hora_inicial'];
            $this->hora_final = $_POST['hora_final'];

            $componentesTurma = [];
            try {
                $componentesTurma = App_Model_IedFinder::getComponentesTurma(
                    serieId: $this->ref_ref_cod_serie,
                    escola: $this->ref_cod_escola,
                    turma: $this->ref_cod_turma
                );
            } catch (Exception) {
            }

            foreach ($componentesTurma as $componente) {
                $opcoes_disc[$componente->id] = $componente->id;
            }
            foreach ($opcoes_disc as $displina) {
                $parametros = "?ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";

                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    ref_cod_quadro_horario: $this->ref_cod_quadro_horario,
                    ref_ref_cod_serie: $this->ref_ref_cod_serie,
                    ref_ref_cod_escola: $this->ref_cod_escola,
                    ref_ref_cod_disciplina: $displina,
                    ref_cod_instituicao_servidor: $this->ref_cod_instituicao,
                    ref_servidor: $this->ref_servidor,
                    hora_inicial: $this->hora_inicial,
                    hora_final: $this->hora_final,
                    ativo: 1,
                    dia_semana: $this->dia_semana
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
        } elseif (is_array(value: $this->quadro_horario)) {
            foreach ($this->quadro_horario as $registro) {
                $parametros = "?ref_cod_instituicao={$registro['ref_cod_instituicao_servidor_']}&ref_cod_escola={$registro['ref_ref_cod_escola_']}&ref_cod_curso={$this->ref_cod_curso_}&ref_cod_serie={$registro['ref_ref_cod_serie_']}&ref_cod_turma={$this->ref_cod_turma}&ano={$this->ano_alocacao}&busca=S";
                $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                    ref_cod_quadro_horario: $registro['ref_cod_quadro_horario_'],
                    ref_ref_cod_serie: $registro['ref_ref_cod_serie_'],
                    ref_ref_cod_escola: $registro['ref_ref_cod_escola_'],
                    ref_ref_cod_disciplina: $registro['ref_ref_cod_disciplina_'],
                    sequencial: $registro['sequencial_'],
                    ref_cod_instituicao_servidor: $registro['ref_cod_instituicao_servidor_'],
                    ref_servidor: $registro['ref_servidor_'],
                    ativo: $registro['ativo_'],
                );

                if ($obj_horario->detalhe()) {
                    $obj_horario = new clsPmieducarQuadroHorarioHorarios(
                        ref_cod_quadro_horario: $registro['ref_cod_quadro_horario_'],
                        ref_ref_cod_serie: $registro['ref_ref_cod_serie_'],
                        ref_ref_cod_escola: $registro['ref_ref_cod_escola_'],
                        ref_ref_cod_disciplina: $registro['ref_ref_cod_disciplina_'],
                        sequencial: $registro['sequencial_'],
                        ref_cod_instituicao_servidor: $registro['ref_cod_instituicao_servidor_'],
                        ref_servidor: $registro['ref_servidor_'],
                        hora_inicial: $registro['hora_inicial_'],
                        hora_final: $registro['hora_final_'],
                        ativo: $registro['ativo_'],
                        dia_semana: $registro['dia_semana_']
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
                        ref_cod_quadro_horario: $registro['ref_cod_quadro_horario_'],
                        ref_ref_cod_serie: $registro['ref_ref_cod_serie_'],
                        ref_ref_cod_escola: $registro['ref_ref_cod_escola_'],
                        ref_ref_cod_disciplina: $registro['ref_ref_cod_disciplina_'],
                        ref_cod_instituicao_servidor: $registro['ref_cod_instituicao_servidor_'],
                        ref_servidor: $registro['ref_servidor_'],
                        hora_inicial: $registro['hora_inicial_'],
                        hora_final: $registro['hora_final_'],
                        ativo: $registro['ativo_'],
                        dia_semana: $registro['dia_semana_']
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
            $obj_quadro_horarios_aux->excluirTodos(identificador: $this->identificador);

            $this->mensagem .= 'Cadastro editado com sucesso.<br>';
            $this->simpleRedirect(url: "educar_quadro_horario_lst.php{$parametros}");
        }

        $this->mensagem = 'Cadastro não editado.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(
            int_processo_ap: 641,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_calendario_anotacao_lst.php'
        );

        $obj = LegacyCalendarDay::find($this->ref_cod_calendario_ano_letivo);

        if ($obj->delete()) {
            $obj_quadro_horarios_aux = new clsPmieducarQuadroHorarioHorariosAux();
            $obj_quadro_horarios_aux->excluirTodos(identificador: $this->identificador);

            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect(url: "educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}");
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-quadro-horario-horarios-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Servidores - Cadastro de Horários';
        $this->processoAp = '641';
    }
};
