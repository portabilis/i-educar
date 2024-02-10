<?php

use App\Models\LegacyAbandonmentType;
use App\Process;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\UnidadesCurriculares;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

return new class extends clsDetalhe
{
    public $titulo;

    public $ref_cod_matricula;

    public $ref_cod_reserva_vaga;

    public $ref_ref_cod_escola;

    public $ref_ref_cod_serie;

    public $ref_cod_abandono_tipo;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_aluno;

    public $aprovado;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function getDescription($description)
    {
        if (empty($description)) {
            return $description;
        }

        $lessDescription = $description;
        if (strlen(string: $description) >= 200) {
            $lessDescription = substr(string: $description, offset: 0, length: strpos(haystack: $description, needle: ' ', offset: 200)) . '...';
        }

        return "<div align='justify'> <span class='desc-red'>{$lessDescription}</span> <span class='descricao' style='display: none'>{$description}</span><a href='javascript:void(0)' class='ver-mais'>Mostrar mais</a><a href='javascript:void(0)' style='display: none' class='ver-menos'>Mostrar menos</a></div>";
    }

    public function Gerar()
    {
        // carrega estilo para feedback messages, exibindo msgs da api.
        $style = '/vendor/legacy/Portabilis/Assets/Stylesheets/Frontend.css';
        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: $style);

        $this->titulo = 'Matrícula - Detalhe';
        $this->ref_cod_matricula = $_GET['cod_matricula'];

        $obj_matricula = new clsPmieducarMatricula();
        $lst_matricula = $obj_matricula->lista(int_cod_matricula: $this->ref_cod_matricula);

        if ($lst_matricula) {
            $registro = array_shift(array: $lst_matricula);
        }

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_aluno_det.php?cod_aluno=' . $registro['ref_cod_aluno']);
        }

        $verificaMatriculaUltimoAno = $obj_matricula->verificaMatriculaUltimoAno(codAluno: $registro['ref_cod_aluno'], codMatricula: $registro['cod_matricula']);

        $existeSaidaEscola = $obj_matricula->existeSaidaEscola(codMatricula: $registro['cod_matricula']);

        // Curso
        $obj_ref_cod_curso = new clsPmieducarCurso(cod_curso: $registro['ref_cod_curso']);
        $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
        $curso_id = $registro['ref_cod_curso'];
        $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

        // Série
        $obj_serie = new clsPmieducarSerie(cod_serie: $registro['ref_ref_cod_serie']);
        $det_serie = $obj_serie->detalhe();
        $serie_id = $registro['ref_ref_cod_serie'];
        $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];

        // Nome da instituição
        $obj_cod_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

        // Escola
        $obj_ref_cod_escola = new clsPmieducarEscola(cod_escola: $registro['ref_ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $escola_id = $registro['ref_ref_cod_escola'];
        $registro['ref_ref_cod_escola'] = $det_ref_cod_escola['nome'];

        // Nome do aluno
        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista(
            int_cod_aluno: $registro['ref_cod_aluno'],
            int_ativo: 1
        );

        if (is_array(value: $lst_aluno)) {
            $det_aluno = array_shift(array: $lst_aluno);
            $nm_aluno = $det_aluno['nome_aluno'];
        }

        if ($registro['cod_matricula']) {
            $this->addDetalhe(detalhe: ['Número Matrícula', $registro['cod_matricula']]);
        }

        if ($nm_aluno) {
            $this->addDetalhe(detalhe: ['Aluno', $nm_aluno]);
        }

        if ($registro['ref_cod_instituicao']) {
            $this->addDetalhe(detalhe: ['Instituição', $registro['ref_cod_instituicao']]);
        }

        if ($registro['ref_ref_cod_escola']) {
            $this->addDetalhe(detalhe: ['Escola', $registro['ref_ref_cod_escola']]);
        }

        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(detalhe: ['Curso', $registro['ref_cod_curso']]);
        }

        if ($registro['ref_ref_cod_serie']) {
            $this->addDetalhe(detalhe: ['Série', $registro['ref_ref_cod_serie']]);
        }

        // Nome da turma
        $enturmacoes = new clsPmieducarMatriculaTurma();
        $enturmacoes = $enturmacoes->lista(
            int_ref_cod_matricula: $this->ref_cod_matricula
        );

        $existeTurma = false;
        $existeTurmaMulti = false;
        $existeTurmaTurnoIntegral = false;
        $existeAtendimentoEspecializado = false;
        $existeTurmaItineraria = false;
        $nomesTurmas = [];
        $datasEnturmacoes = [];
        $nomesTurnos = [];

        foreach ($enturmacoes as $enturmacao) {
            $turma = new clsPmieducarTurma(cod_turma: $enturmacao['ref_cod_turma']);
            $turma = $turma->detalhe() ?? [];
            $turma_id = $enturmacao['ref_cod_turma'];

            if (in_array(needle: $turma['etapa_educacenso'], haystack: App_Model_Educacenso::etapas_multisseriadas())) {
                $existeTurmaMulti = true;
            }

            $estruturaCurricular = transformStringFromDBInArray(string: $turma['estrutura_curricular']) ?? [];
            $unidadeCurricular = transformStringFromDBInArray(string: $turma['unidade_curricular']) ?? [];
            $turmaItineraria = in_array(needle: 2, haystack: $estruturaCurricular);
            $turmaFormacaoBasica = in_array(needle: 1, haystack: $estruturaCurricular);
            $etapasItinerario = [25, 26, 27, 28, 29, 30, 31, 32, 33, 35, 36, 37, 38, 67, 71, 74];

            if (in_array(UnidadesCurriculares::TRILHAS_DE_APROFUNDAMENTO_APRENDIZAGENS, $unidadeCurricular) &&
                in_array($turma['etapa_educacenso'], $etapasItinerario)
            ) {
                $existeTurmaItineraria = true;
            }

            if ($enturmacao['ativo'] == 0) {
                continue;
            }

            $nomesTurmas[] = $turma['nm_turma'];
            $datasEnturmacoes[] = Portabilis_Date_Utils::pgSQLToBr(timestamp: $enturmacao['data_enturmacao']);

            if ($turma['turma_turno_id'] == clsPmieducarTurma::TURNO_INTEGRAL) {
                $existeTurmaTurnoIntegral = true;
            }

            if ($turma['tipo_atendimento'] == TipoAtendimentoTurma::AEE) {
                $existeAtendimentoEspecializado = true;
            }

            if ($enturmacao['turno_id']) {
                $nomesTurnos[] = match ((int) $enturmacao['turno_id']) {
                    clsPmieducarTurma::TURNO_MATUTINO => 'Matutino',
                    clsPmieducarTurma::TURNO_VESPERTINO => 'Vespertino',
                    clsPmieducarTurma::TURNO_NOTURNO => 'Noturno',
                    default => null
                };
            }
        }
        $nomesTurmas = implode(separator: '<br />', array: $nomesTurmas);
        $datasEnturmacoes = implode(separator: '<br />', array: $datasEnturmacoes);

        if (empty($nomesTurnos)) {
            $nomesTurnos = match ((int) $turma['turma_turno_id']) {
                clsPmieducarTurma::TURNO_MATUTINO => 'Matutino',
                clsPmieducarTurma::TURNO_VESPERTINO => 'Vespertino',
                clsPmieducarTurma::TURNO_NOTURNO => 'Noturno',
                clsPmieducarTurma::TURNO_INTEGRAL => 'Integral',
                default => null
            };
        } else {
            $nomesTurnos = implode(separator: '<br />', array: $nomesTurnos);
        }

        if ($nomesTurmas) {
            $this->addDetalhe(detalhe: ['Turma', $nomesTurmas]);
            $this->addDetalhe(detalhe: ['Turno', $nomesTurnos]);
            $this->addDetalhe(detalhe: ['Data Enturmação', $datasEnturmacoes]);
            $existeTurma = true;
        } else {
            $this->addDetalhe(detalhe: ['Turma', '']);
            $this->addDetalhe(detalhe: ['Turno', '']);
            $this->addDetalhe(detalhe: ['Data Enturmação', '']);
        }

        if ($registro['ref_cod_reserva_vaga']) {
            $this->addDetalhe(detalhe: ['Número Reserva Vaga', $registro['ref_cod_reserva_vaga']]);
        }

        $situacao = App_Model_MatriculaSituacao::getSituacao(id: $registro['aprovado']);
        $this->addDetalhe(detalhe: ['Situação', $situacao]);

        if ($registro['aprovado'] == App_Model_MatriculaSituacao::TRANSFERIDO) {
            $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();

            $lst_transferencia = $obj_transferencia->lista(int_ref_cod_matricula_saida: $registro['cod_matricula'], int_ativo: 1, int_ref_cod_aluno: $registro['ref_cod_aluno']);

            if (is_array(value: $lst_transferencia)) {
                $det_transferencia = array_shift(array: $lst_transferencia);
            }
            if (!$det_transferencia['ref_cod_escola_destino'] == '0') {
                $tmp_obj = new clsPmieducarEscola(cod_escola: $det_transferencia['ref_cod_escola_destino']);
                $tmp_det = $tmp_obj->detalhe();
                $this->addDetalhe(detalhe: ['Escola destino', $tmp_det['nome']]);
            } else {
                $this->addDetalhe(detalhe: ['Escola destino', $det_transferencia['escola_destino_externa']]);
                $this->addDetalhe(detalhe: ['Estado escola destino', $det_transferencia['estado_escola_destino_externa']]);
                $this->addDetalhe(detalhe: ['Município escola destino', $det_transferencia['municipio_escola_destino_externa']]);
            }
            $this->addDetalhe(detalhe: ['Observação', $det_transferencia['observacao']]);
        }

        if ($registro['aprovado'] == App_Model_MatriculaSituacao::FALECIDO) {
            $this->addDetalhe(detalhe: ['Observação', $registro['observacao']]);
        }

        if ($existeSaidaEscola) {
            $this->addDetalhe(detalhe: ['Saída da escola', 'Sim']);
            $this->addDetalhe(detalhe: ['Data de saída da escola', Portabilis_Date_Utils::pgSQLToBr(timestamp: $registro['data_saida_escola'])]);
            $this->addDetalhe(detalhe: ['Observação', $registro['observacao']]);
        }

        if ($registro['aprovado'] == App_Model_MatriculaSituacao::ABANDONO) {

            $tipoAbandono = LegacyAbandonmentType::find(id: $registro['ref_cod_abandono_tipo'])?->getAttributes();

            $observacaoAbandono = $registro['observacao'];

            $this->addDetalhe(detalhe: ['Motivo do Abandono', $tipoAbandono ? $tipoAbandono['nome'] : '']);
            $this->addDetalhe(detalhe: ['Observação', $observacaoAbandono]);
        }

        if ($registro['aprovado'] == App_Model_MatriculaSituacao::RECLASSIFICADO) {
            $this->addDetalhe(detalhe: ['Descrição', $this->getDescription(description: $registro['descricao_reclassificacao'])]);
        }

        $this->addDetalhe(detalhe: ['Formando', $registro['formando'] == 0 ? 'Não' : 'Sim']);

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            // verifica se existe transferencia
            if ($registro['aprovado'] != 4 && $registro['aprovado'] != 6) {
                $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();

                $lst_transferencia = $obj_transferencia->lista(
                    int_ref_cod_matricula_saida: $registro['cod_matricula'],
                    int_ativo: 1,
                    int_ref_cod_aluno: $registro['ref_cod_aluno']
                );

                // verifica se existe uma solicitacao de transferencia INTERNA
                if (is_array(value: $lst_transferencia)) {
                    $det_transferencia = array_shift(array: $lst_transferencia);
                }

                $data_transferencia = $det_transferencia['data_transferencia'];
            }

            if ($registro['aprovado'] == 3 &&
                (!is_array(value: $lst_transferencia) && !isset($data_transferencia))
            ) {

                // Verificar se tem permissao para executar cancelamento de matricula
                if ($this->permissaoCancelar()) {
                    $this->array_botao[] = 'Cancelar matrícula';
                    $this->array_botao_url_script[] = "showConfirmationMessage(\"educar_matricula_cad.php?cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
                }

                if ($this->permissaoOcorrenciaDisciplinar()) {
                    $this->array_botao[] = 'Ocorrências disciplinares';
                    $this->array_botao_url_script[] = "go(\"educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
                }

                // Apenas libera a dispensa de disciplina quando o aluno estiver enturmado
                if ($this->permissaoDispensaComponenteCurriculares() && $registro['ref_ref_cod_serie'] && $existeTurma) {
                    $this->array_botao[] = 'Dispensa de componentes curriculares';
                    $this->array_botao_url_script[] = "go(\"educar_dispensa_disciplina_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
                }

                if ($this->permissaoBuscaAtiva() && $registro['aprovado'] == App_Model_MatriculaSituacao::EM_ANDAMENTO) {
                    $this->array_botao[] = 'Busca ativa';
                    $this->array_botao_url_script[] = "go(\"educar_busca_ativa_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
                }

                $dependencia = $registro['dependencia'];

                if ($this->permissaoDisciplinasDependência() && $registro['ref_ref_cod_serie'] && $existeTurma && $dependencia) {
                    $this->array_botao[] = 'Disciplinas de dependência';
                    $this->array_botao_url_script[] = "go(\"educar_disciplina_dependencia_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
                }

                if ($this->permissaoEnturmar()) {
                    $this->array_botao[] = _cl(key: 'matricula.detalhe.enturmar');
                    $this->array_botao_url_script[] = "go(\"educar_matricula_turma_lst.php?ref_cod_matricula={$registro['cod_matricula']}&ano_letivo={$registro['ano']}\")";
                }

                if ($this->permissaoModalidadeEnsino()) {
                    $this->array_botao[] = 'Modalidade de ensino';
                    $this->array_botao_url_script[] = "go(\"educar_matricula_modalidade_ensino.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
                }

                if ($this->permissaoAbandono()) {
                    $this->array_botao[] = 'Abandono';
                    $this->array_botao_url_script[] = "go(\"educar_abandono_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\");";
                }

                if ($this->permissaoFalecido()) {
                    $this->array_botao[] = 'Falecido';
                    $this->array_botao_url_script[] = "go(\"educar_falecido_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\");";
                }

                if ($this->permissaoReclassificar() && $registro['ref_ref_cod_serie']) {
                    $this->array_botao[] = 'Reclassificar';
                    $this->array_botao_url_script[] = "go(\"educar_matricula_reclassificar_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
                }
            }

            if ($this->permissaoEtapaAluno() && $existeTurmaMulti) {
                $this->array_botao[] = 'Etapa do aluno';
                $this->array_botao_url_script[] = "go(\"educar_matricula_etapa_turma_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
            }

            if ($this->permissaoTipoAEEAluno() && $existeAtendimentoEspecializado) {
                $this->array_botao[] = 'Tipo do AEE do aluno';
                $this->array_botao_url_script[] = "go(\"educar_matricula_turma_tipo_aee_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
            }

            if ($this->permissaoTurno() && $existeTurmaTurnoIntegral) {
                $this->array_botao[] = 'Turno';
                $this->array_botao_url_script[] = "go(\"educar_matricula_turma_turno_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
            }

            if ($this->permissaoItinerarioFormativo() && $existeTurmaItineraria) {
                $this->array_botao[] = 'Itinerário formativo';
                $link = route(name: 'registration.formative-itinerary.index', parameters: $registro['cod_matricula']);
                $this->array_botao_url_script[] = "go(\"{$link}\")";
            }

            if ($registro['aprovado'] != 4 && $registro['aprovado'] != 6) {
                if ($this->permissaoSolicitarTransferencia()) {
                    if (is_array(value: $lst_transferencia) && isset($data_transferencia)) {
                        $this->array_botao[] = 'Cancelar solicitação transferência';
                        $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
                    } elseif ($registro['ref_ref_cod_serie'] && $registro['aprovado'] == App_Model_MatriculaSituacao::EM_ANDAMENTO) {
                        $this->array_botao[] = _cl(key: 'matricula.detalhe.solicitar_transferencia');
                        $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
                    }
                }

                if ($this->permissaoFormando() && $registro['aprovado'] == 3 &&
                    (!is_array(value: $lst_transferencia) && !isset($data_transferencia))
                ) {
                    if ($registro['formando'] == 0) {
                        $this->array_botao[] = 'Formando';
                        $this->array_botao_url_script[] = "if(confirm(\"Deseja marcar a matrícula como formando?\"))go(\"educar_matricula_formando_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&formando=1\")";
                    } else {
                        $this->array_botao[] = 'Desmarcar como formando';
                        $this->array_botao_url_script[] = "if(confirm(\"Deseja desmarcar a matrícula como formando?\"))go(\"educar_matricula_formando_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&formando=0\")";
                    }
                }
            }

            $ultimaMatricula = $obj_matricula->getEndMatricula(codAluno: $registro['ref_cod_aluno']);
            $permiteCancelarTransferencia = new clsPermissoes();
            $permiteCancelarTransferencia = $permiteCancelarTransferencia->permissao_excluir(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7);

            if ($this->permissaoSolicitarTransferencia()) {
                if ($permiteCancelarTransferencia && $registro['aprovado'] == App_Model_MatriculaSituacao::TRANSFERIDO && $this->canCancelTransferencia(matriculaId: $registro['cod_matricula'])) {
                    $this->array_botao[] = 'Cancelar transferência';

                    // TODO ver se código, seta matricula como em andamento, ativa ultima matricula_turma for matricula, e desativa transferencia solicitacao
                    $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true&reabrir_matricula=true&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
                } elseif ($permiteCancelarTransferencia && $registro['aprovado'] == App_Model_MatriculaSituacao::TRANSFERIDO && $ultimaMatricula == 4) {
                    $this->array_botao[] = 'Cancelar transferência';

                    // TODO ver se código, seta matricula como em andamento, ativa ultima matricula_turma for matricula, e desativa transferencia solicitacao
                    $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true&reabrir_matricula=true&ano={$registro['ano']}&escola={$escola_id}&curso={$curso_id}&serie={$serie_id}&turma={$turma_id}\")";
                }
            }

            if ($this->permissaoAbandono() && $registro['aprovado'] == App_Model_MatriculaSituacao::ABANDONO && $this->permissaoAbandono()) {
                $this->array_botao[] = 'Desfazer abandono';
                $this->array_botao_url_script[] = "deleteAbandono({$registro['cod_matricula']})";
            }

            if ($this->permissaoSaidaEscola() && !$existeSaidaEscola &&
                $verificaMatriculaUltimoAno &&
                ($registro['aprovado'] == App_Model_MatriculaSituacao::APROVADO ||
                    $registro['aprovado'] == App_Model_MatriculaSituacao::REPROVADO ||
                    $registro['aprovado'] == App_Model_MatriculaSituacao::APROVADO_COM_DEPENDENCIA ||
                    $registro['aprovado'] == App_Model_MatriculaSituacao::APROVADO_PELO_CONSELHO ||
                    $registro['aprovado'] == App_Model_MatriculaSituacao::REPROVADO_POR_FALTAS)) {
                $this->array_botao[] = 'Saída da escola';
                $this->array_botao_url_script[] = "go(\"educar_saida_escola_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&escola={$registro['ref_ref_cod_escola']}\");";
            }

            if ($this->permissaoSaidaEscola() && $existeSaidaEscola && $verificaMatriculaUltimoAno) {
                $this->array_botao[] = 'Cancelar saída da escola';
                $this->array_botao_url_script[] = "desfazerSaidaEscola({$registro['cod_matricula']})";
            }

            if ($this->permissaoReclassificar() && $registro['aprovado'] == App_Model_MatriculaSituacao::RECLASSIFICADO && $this->permissaoReclassificar()) {
                $this->array_botao[] = 'Desfazer reclassificação';
                $this->array_botao_url_script[] = "deleteReclassificacao({$registro['cod_matricula']})";
            }
        }

        if ($this->user()->can(abilities: 'view', arguments: Process::ENROLLMENT_HISTORY)) {
            $this->array_botao[] = 'Histórico de enturmações';
            $link = route(name: 'enrollments.enrollment-history', parameters: ['id' => $registro['cod_matricula']]);
            $this->array_botao_url_script[] = "go(\"{$link}\")";
        }

        $this->url_cancelar = 'educar_aluno_det.php?cod_aluno=' . $registro['ref_cod_aluno'];
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Matrícula', breadcrumbs: [
            'educar_index.php' => 'Escola',
        ]);

        // js
        $scripts = [
            '/vendor/legacy/Portabilis/Assets/Javascripts/Utils.js',
            '/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js',
            '/vendor/legacy/Cadastro/Assets/Javascripts/MatriculaShow.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
    }

    public function makeExtra()
    {
        return file_get_contents(filename: __DIR__ . '/scripts/extra/educar-matricula.js');
    }

    private function getPermissaoVisualizar($process)
    {
        $user = Auth::user();
        $allow = Gate::allows(ability: 'view', arguments: $process);
        if ($user->isLibrary()) {
            return false;
        }

        return $allow;
    }

    // Verificar se pode cancelar matricula
    public function permissaoCancelar()
    {
        return $this->getPermissaoVisualizar(627);
    }

    public function permissaoOcorrenciaDisciplinar()
    {
        return $this->getPermissaoVisualizar(681);
    }

    public function permissaoDispensaComponenteCurriculares()
    {
        return $this->getPermissaoVisualizar(628);
    }

    public function permissaoBuscaAtiva()
    {
        return $this->getPermissaoVisualizar(Process::ACTIVE_LOOKING);
    }

    public function permissaoDisciplinasDependência()
    {
        return $this->getPermissaoVisualizar(682);
    }

    public function permissaoEnturmar()
    {
        return $this->getPermissaoVisualizar(683);
    }

    public function permissaoModalidadeEnsino()
    {
        return $this->getPermissaoVisualizar(684);
    }

    public function permissaoAbandono()
    {
        return $this->getPermissaoVisualizar(685);
    }

    public function permissaoFalecido()
    {
        return $this->getPermissaoVisualizar(686);
    }

    public function permissaoReclassificar()
    {
        return $this->getPermissaoVisualizar(Process::RECLASSIFY_REGISTRATION);
    }

    public function permissaoEtapaAluno()
    {
        return $this->getPermissaoVisualizar(687);
    }

    public function permissaoTipoAEEAluno()
    {
        return $this->getPermissaoVisualizar(688);
    }

    public function permissaoTurno()
    {
        return $this->getPermissaoVisualizar(689);
    }

    public function permissaoItinerarioFormativo()
    {
        return $this->getPermissaoVisualizar(690);
    }

    public function permissaoSolicitarTransferencia()
    {
        return $this->getPermissaoVisualizar(691);
    }

    public function permissaoFormando()
    {
        return $this->getPermissaoVisualizar(692);
    }

    public function permissaoSaidaEscola()
    {
        return $this->getPermissaoVisualizar(693);
    }

    public function canCancelTransferencia($matriculaId)
    {
        $sql = "SELECT transferencia_solicitacao.cod_transferencia_solicitacao
              FROM pmieducar.transferencia_solicitacao
             WHERE ref_cod_matricula_saida = $matriculaId
               AND ativo = 1";

        $db = new clsBanco();

        return $db->CampoUnico(consulta: $sql);
    }

    public function Formular()
    {
        $this->title = 'Matrícula';
        $this->processoAp = 578;
    }
};
