<?php

use App\Events\RegistrationEvent;
use App\Exceptions\Registration\RegistrationException;
use App\Exceptions\Transfer\TransferException;
use App\Models\LegacyEnrollment;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySequenceGrade;
use App\Models\LegacyStudent;
use App\Models\RegistrationStatus;
use App\Services\EnrollmentService;
use App\Services\PromotionService;
use App\Services\SchoolClass\AvailableTimeService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

return new class extends clsCadastro
{
    public $cod_matricula;

    public $ref_cod_reserva_vaga;

    public $ref_ref_cod_escola;

    public $ref_ref_cod_serie;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_aluno;

    public $aprovado;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ano;

    public $data_matricula;

    public $observacoes;

    public $ref_cod_instituicao;

    public $ref_cod_curso;

    public $ref_cod_escola;

    public $ref_cod_turma;

    public $semestre;

    public $is_padrao;

    public $dependencia;

    public $ref_cod_candidato_reserva_vaga;

    public $ref_cod_turma_copiar_enturmacoes;

    private $availableTimeService;

    private $transferido = 4;

    private $situacaoUltimaMatricula;

    private $serieUltimaMatricula;

    private $anoUltimaMatricula;

    public function __construct()
    {
        parent::__construct();

        $user = Auth::user();
        $allow = Gate::allows('view', 680);

        if ($user->isLibrary() || !$allow) {
            $this->simpleRedirect(url: '/intranet/index.php');

            return false;
        }
    }

    public function Inicializar()
    {
        $this->ref_cod_turma_copiar_enturmacoes = $this->getQueryString(name: 'ref_cod_turma_copiar_enturmacoes');
        $this->cod_matricula = $this->getQueryString(name: 'cod_matricula');
        $this->ref_cod_aluno = $this->getQueryString(name: 'ref_cod_aluno');
        $this->ref_cod_candidato_reserva_vaga = $this->getQueryString(name: 'ref_cod_candidato_reserva_vaga');
        $this->ano = $this->getQueryString(name: 'ano');

        $retorno = $this->ref_cod_turma_copiar_enturmacoes ? 'Enturmar' : 'Novo';
        $obj_aluno = new clsPmieducarAluno(cod_aluno: $this->ref_cod_aluno);

        if (!$obj_aluno->existe() and !$this->ref_cod_turma_copiar_enturmacoes) {
            throw new HttpResponseException(
                response: new RedirectResponse(url: 'educar_aluno_lst.php')
            );
        }

        if ($this->ref_cod_turma_copiar_enturmacoes) {
            $this->nome_url_sucesso = 'Gravar enturmações';
            $url = route(name: 'enrollments.batch.enroll.index', parameters: ['schoolClass' => $this->ref_cod_turma_copiar_enturmacoes]);
        } else {
            $url = 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno;
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: $url);

        if (is_numeric(value: $this->cod_matricula)) {
            if ($obj_permissoes->permissao_excluir(int_processo_ap: 627, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                $this->Excluir();
            }
        }

        $this->nome_url_cancelar = 'Cancelar';
        $this->url_cancelar = $url;

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Nova';

        $this->breadcrumb(currentPage: "{$nomeMenu} matrícula", breadcrumbs: [
            'educar_index.php' => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto(nome: 'ref_cod_turma_copiar_enturmacoes', valor: $this->ref_cod_turma_copiar_enturmacoes);
        $this->campoOculto(nome: 'cod_matricula', valor: $this->cod_matricula);
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);
        $this->campoOculto(nome: 'ref_cod_candidato_reserva_vaga', valor: $this->ref_cod_candidato_reserva_vaga);

        if ($this->ref_cod_aluno) {
            $obj_aluno = new clsPmieducarAluno();

            $lst_aluno = $obj_aluno->lista(
                int_cod_aluno: $this->ref_cod_aluno,
                int_ativo: 1
            );

            if (is_array(value: $lst_aluno)) {
                $det_aluno = array_shift(array: $lst_aluno);
                $this->nm_aluno = $det_aluno['nome_aluno'];
                $this->campoRotulo(nome: 'nm_aluno', campo: 'Aluno', valor: $this->nm_aluno);
            }
        }

        if ($this->ref_cod_turma_copiar_enturmacoes) {
            $this->nome_url_sucesso = 'Gravar enturmações';
        }

        $this->inputsHelper()->dynamic(helperNames: ['ano', 'instituicao', 'escola', 'curso', 'serie', 'turma']);
        $this->inputsHelper()->date(attrName: 'data_matricula', inputOptions: ['label' => 'Data da matrícula', 'placeholder' => 'dd/mm/yyyy', 'value' => date(format: 'd/m/Y')]);
        $this->inputsHelper()->textArea(attrName: 'observacoes', inputOptions: ['required' => false, 'label' => 'Observações']);
        $this->inputsHelper()->hidden(attrName: 'ano_em_andamento', inputOptions: ['value' => '1']);

        if (config(key: 'legacy.app.matricula.dependencia') == 1) {
            $this->inputsHelper()->checkbox(
                attrName: 'dependencia',
                inputOptions: [
                    'label' => 'Matrícula de dependência?',
                    'value' => $this->dependencia,
                ]
            );
        }

        if (is_numeric(value: $this->ref_cod_curso)) {
            $obj_curso = new clsPmieducarCurso(cod_curso: $this->ref_cod_curso);
            $det_curso = $obj_curso->detalhe();

            if (is_numeric(value: $det_curso['ref_cod_tipo_avaliacao'])) {
                $this->campoOculto(nome: 'apagar_radios', valor: $det_curso['padrao_ano_escolar']);
                $this->campoOculto(nome: 'is_padrao', valor: $det_curso['padrao_ano_escolar']);
            }
        }

        $this->acao_enviar = 'formUtils.submit()';
    }

    protected function getCurso($id)
    {
        $curso = new clsPmieducarCurso(cod_curso: $id);

        return $curso->detalhe();
    }

    private function getEnturmacoesNaTurma($turma)
    {
        return (array) Portabilis_Utils_Database::fetchPreparedQuery(sql: "
            select *
            from pmieducar.matricula_turma
            where ref_cod_turma = {$turma}
            and ativo = 1
        ");
    }

    private function getMaximoAlunosNaTurma($turma)
    {
        return (int) (new clsBanco())->CampoUnico(consulta: "
            select max_aluno
            from pmieducar.turma
            where cod_turma = $turma
        ");
    }

    private function getMatricula($matricula)
    {
        $matriculas = Portabilis_Utils_Database::fetchPreparedQuery(sql: "
            select *
            from pmieducar.matricula
            where cod_matricula = {$matricula}
            limit 1
        ");

        if (is_array(value: $matriculas) && count(value: $matriculas)) {
            return array_shift(array: $matriculas);
        }

        throw new Exception(message: "A matrícula {$matricula} não foi encontrada.");
    }

    private function addMatricula($escola, $curso, $serie, $aluno, $ano)
    {
        $datahora = date(format: 'Y-m-d H:i:s');

        $obj = new clsPmieducarMatricula(
            ref_ref_cod_escola: $escola,
            ref_ref_cod_serie: $serie,
            ref_usuario_cad: $this->pessoa_logada,
            ref_cod_aluno: $aluno,
            aprovado: 3,
            ativo: 1,
            ano: $ano,
            ultima_matricula: 1,
            ref_cod_curso: $curso,
            semestre: 1,
            data_matricula: $datahora
        );

        return $obj->cadastra();
    }

    private function addEnturmacao($matricula, $turma, $sequencial, $ativo)
    {
        $data = date(format: 'Y-m-d');
        $datahora = date(format: 'Y-m-d H:i:s');
        $usuario = $this->pessoa_logada;

        (new clsBanco)->CampoUnico(consulta: "
            insert into pmieducar.matricula_turma (
                ref_cod_matricula, ref_cod_turma, sequencial, ref_usuario_exc, ref_usuario_cad, data_cadastro,
                ativo, data_enturmacao
            )
            values ({$matricula}, {$turma}, {$sequencial}, NULL, {$usuario}, '{$datahora}', {$ativo}, '{$data}')
        ");

        return true;
    }

    public function Enturmar()
    {
        try {
            $enturmacoesNaTurmaDestino = $this->getEnturmacoesNaTurma(turma: $this->ref_cod_turma);
            $enturmacoesParaCopiar = $this->getEnturmacoesNaTurma(turma: $this->ref_cod_turma_copiar_enturmacoes);
        } catch (Exception) {
            $this->mensagem = 'Houve um erro ao buscar informações das turmas.';

            return false;
        }

        $maximoDeAlunosTurmaDestino = $this->getMaximoAlunosNaTurma(turma: $this->ref_cod_turma);
        $quantidadeAlunosNaTurmaDestino = count(value: $enturmacoesNaTurmaDestino);
        $quantidadeAlunosParaCopiar = count(value: $enturmacoesParaCopiar);
        $vagasDisponiveisTurmaDestino = $maximoDeAlunosTurmaDestino - $quantidadeAlunosNaTurmaDestino;

        if ($quantidadeAlunosParaCopiar > $vagasDisponiveisTurmaDestino) {
            $this->mensagem = 'A turma não tem saldo de vagas suficiente.';

            return false;
        }

        $mensagemErro = null;
        $validarCamposEducacenso = $this->validarCamposObrigatoriosCenso();

        foreach ($enturmacoesParaCopiar as $enturmar) {
            $dadosDaMatricula = $this->getMatricula(matricula: $enturmar['ref_cod_matricula']);

            if ($validarCamposEducacenso && !$this->availableTimeService()->isAvailable(studentId: $dadosDaMatricula['ref_cod_aluno'], schoolClassId: $this->ref_cod_turma)) {
                $mensagemErro = 'O aluno já está matriculado em uma turma com esse horário.';
            }

            $matricula = $this->addMatricula(
                escola: $this->ref_cod_escola,
                curso: $this->ref_cod_curso,
                serie: $this->ref_cod_serie,
                aluno: $dadosDaMatricula['ref_cod_aluno'],
                ano: $this->ano
            );

            $this->addEnturmacao(matricula: $matricula, turma: $this->ref_cod_turma, sequencial: $enturmar['sequencial'], ativo: $enturmar['ativo']);
        }

        if (!is_null(value: $mensagemErro)) {
            $this->mensagem = $mensagemErro;

            return false;
        }

        throw new HttpResponseException(
            response: new RedirectResponse(
                url: route(name: 'enrollments.batch.enroll.index', parameters: ['schoolClass' => $this->ref_cod_turma])
            )
        );
    }

    public function Novo()
    {
        DB::beginTransaction();

        $dependencia = $this->dependencia == 'on';

        if (!$this->validaAlunoAtivo()) {
            $this->mensagem = 'Não é possível matricular alunos inativos ou inexistentes.';

            return false;
        }

        if (!$this->validaPeriodoDeMatriculasPelaDataFechamento()) {
            $this->mensagem = 'Não é possível matricular alunos após a data de fechamento.';

            return false;
        }

        if ($dependencia && !$this->verificaQtdeDependenciasPermitida()) {
            return false;
        }

        if ($this->verificaAlunoFalecido()) {
            $this->mensagem = 'Não é possível matricular alunos falecidos.';
        }

        if (!$this->permiteMatriculaSerieDestino() && $this->bloqueiaMatriculaSerieNaoSeguinte()) {
            $this->mensagem = 'Não é possível matricular alunos em séries fora da sequência de enturmação.';

            return false;
        }

        $db = new clsBanco();
        $somente_do_bairro = $db->CampoUnico(consulta: "SELECT matricula_apenas_bairro_escola FROM pmieducar.instituicao where cod_instituicao = {$this->ref_cod_instituicao}");

        if ($somente_do_bairro) {
            $db = new clsBanco();
            $bairro_escola = $db->CampoUnico(consulta: "
                SELECT neighborhood
                FROM addresses a
                JOIN person_has_place php ON true
                    AND a.id = php.place_id
                JOIN pmieducar.escola e ON e.ref_idpes = php.person_id
                WHERE e.cod_escola = {$this->ref_cod_escola}
            ");

            $db = new clsBanco();
            $bairro_aluno = $db->CampoUnico(consulta: "
                SELECT neighborhood
                FROM addresses a
                JOIN person_has_place php ON true
                    AND a.id = php.place_id
                JOIN pmieducar.aluno al ON al.ref_idpes = php.person_id
                WHERE al.cod_aluno = {$this->ref_cod_aluno}
            ");

            if (strcasecmp(string1: $bairro_aluno, string2: $bairro_escola) != 0) {
                $this->mensagem = 'O aluno deve morar no mesmo bairro da escola';

                return false;
            }
        }

        $this->url_cancelar = 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno;
        $this->nome_url_cancelar = 'Cancelar';

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 578,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno
        );

        //novas regras matricula aluno
        $this->ano = $_POST['ano'];
        $anoLetivoEmAndamentoEscola = LegacySchoolAcademicYear::query()->whereSchool($this->ref_cod_escola)->whereYearEq($this->ano)->inProgress()->active()->exists();

        $objEscolaSerie = new clsPmieducarEscolaSerie();
        $dadosEscolaSerie = $objEscolaSerie->lista(int_ref_cod_escola: $this->ref_cod_escola, int_ref_cod_serie: $this->ref_cod_serie);

        if (!$this->existeVagasDisponiveis() && $dadosEscolaSerie[0]['bloquear_enturmacao_sem_vagas']) {
            return false;
        }

        if ($anoLetivoEmAndamentoEscola) {
            $matriculas = LegacyRegistration::query()
                ->filter([
                    'yearEq' => $this->ano,
                    'school' => $this->ref_cod_escola,
                    'course' => $this->ref_cod_curso,
                    'student' => $this->ref_cod_aluno,
                ])
                ->active()
                ->with('grade:cod_serie,nm_serie')
                ->where('aprovado', RegistrationStatus::ONGOING)
                ->where('dependencia', false)
                ->get(['ref_ref_cod_serie', 'ref_cod_curso']);

            if ($matriculas->isNotEmpty() && !$dependencia) {
                $curso = $this->getCurso(id: $this->ref_cod_curso);

                $cursoADeferir = new clsPmieducarCurso(cod_curso: $this->ref_cod_curso);
                $cursoDeAtividadeComplementar = $cursoADeferir->cursoDeAtividadeComplementar();

                if ($matriculas->firstWhere('ref_ref_cod_serie', $this->ref_cod_serie) && !$cursoDeAtividadeComplementar) {
                    $this->mensagem = 'Este aluno já está matriculado nesta série e curso, não é possivel matricular um aluno mais de uma vez na mesma série.<br />';

                    return false;
                } elseif ($curso['multi_seriado'] != 1) {
                    $nomeSerie = $matriculas->pluck('grade.nm_serie')->unique()->implode(', ');

                    $this->mensagem = "Este aluno já está matriculado no(a) '$nomeSerie' deste curso e escola. Como este curso não é multisseriado, não é possivel manter mais de uma matricula em andamento para o mesmo curso.<br />";

                    return false;
                }
            } else {
                $db->Consulta(consulta: "select ref_ref_cod_escola, ref_cod_curso, ref_ref_cod_serie, ano from pmieducar.matricula where ativo = 1 and ref_ref_cod_escola != $this->ref_cod_escola and ref_cod_aluno = $this->ref_cod_aluno AND dependencia = FALSE and aprovado = 3 and not exists (select 1 from pmieducar.transferencia_solicitacao as ts where ts.ativo = 1 and ts.ref_cod_matricula_saida = matricula.cod_matricula )");

                $db->ProximoRegistro();
                $m = $db->Tupla();

                if (is_array(value: $m) && count(value: $m) && !$dependencia) {
                    $mesmoCursoAno = ($m['ref_cod_curso'] == $this->ref_cod_curso && $m['ano'] == $this->ano);
                    $cursoADeferir = new clsPmieducarCurso(cod_curso: $this->ref_cod_curso);
                    $cursoDeAtividadeComplementar = $cursoADeferir->cursoDeAtividadeComplementar();

                    if (($mesmoCursoAno || config(key: 'legacy.app.matricula.multiplas_matriculas') == 0) && !$cursoDeAtividadeComplementar) {
                        $serie = new clsPmieducarSerie(cod_serie: $m['ref_ref_cod_serie'], ref_usuario_exc: null, ref_usuario_cad: null, ref_cod_curso: $m['ref_cod_curso']);
                        $serie = $serie->detalhe();

                        if (is_array(value: $serie) && count(value: $serie)) {
                            $serie = $serie['nm_serie'];
                        } else {
                            $serie = '';
                        }

                        $escola = new clsPmieducarEscola(cod_escola: $m['ref_ref_cod_escola']);
                        $escola = $escola->detalhe();

                        if (is_array(value: $escola) && count(value: $escola)) {
                            $escola = new clsJuridica(idpes: $escola['ref_idpes']);
                            $escola = $escola->detalhe();
                            if (is_array(value: $escola) && count(value: $escola)) {
                                $escola = $escola['fantasia'];
                            } else {
                                $escola = '';
                            }
                        } else {
                            $escola = '';
                        }

                        $curso = new clsPmieducarCurso(cod_curso: $m['ref_cod_curso']);
                        $curso = $curso->detalhe();

                        if (is_array(value: $curso) && count(value: $curso)) {
                            $curso = $curso['nm_curso'];
                        } else {
                            $curso = '';
                        }

                        $this->mensagem = "Este aluno já está matriculado no(a) '$serie' do curso '$curso' na escola '$escola', para matricular este aluno na sua escola solicite transferência ao secretário(a) da escola citada.<br />";

                        return false;
                    }
                }
            }

            $serie = new clsPmieducarSerie(cod_serie: $this->ref_cod_serie);
            $detSerie = $serie->detalhe();

            $alertaFaixaEtaria = $detSerie['alerta_faixa_etaria'];
            $bloquearMatriculaFaixaEtaria = $detSerie['bloquear_matricula_faixa_etaria'];

            $verificarDataCorte = $alertaFaixaEtaria || $bloquearMatriculaFaixaEtaria;

            $reload = Session::get(key: 'reload_faixa_etaria');

            if ($verificarDataCorte && !$reload) {
                $instituicao = new clsPmieducarInstituicao(cod_instituicao: $this->ref_cod_instituicao);
                $instituicao = $instituicao->detalhe();

                $objAluno = new clsPmieducarAluno(cod_aluno: $this->ref_cod_aluno);
                $detAluno = $objAluno->detalhe();

                $objPes = new clsPessoaFisica(int_idpes: $detAluno['ref_idpes']);
                $detPes = $objPes->detalhe();

                $dentroPeriodoCorte = $serie->verificaPeriodoCorteEtarioDataNascimento(dataNascimento: $detPes['data_nasc'], ano: $this->ano);

                if ($bloquearMatriculaFaixaEtaria && !$dentroPeriodoCorte) {
                    $this->mensagem = 'Não foi possível realizar a matrícula, pois a idade do aluno está fora da faixa etária da série';

                    return false;
                } elseif ($alertaFaixaEtaria && !$dentroPeriodoCorte) {
                    echo '<script type="text/javascript">
                        var msg = \'' . 'A idade do aluno encontra-se fora da faixa etária pré-definida na série, deseja continuar com a matrícula?' . '\';
                        if (!confirm(msg)) {
                          window.location = \'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno . '\';
                        } else {
                          parent.document.getElementById(\'formcadastro\').submit();
                        }
                    </script>';

                    //Permite que o usuário possa salvar a matrícula na próxima tentativa
                    $reload = 1;

                    Session::put(key: 'reload_faixa_etaria', value: $reload);
                    Session::save();
                    Session::start();

                    return true;
                }
            }

            $objAluno = new clsPmieducarAluno();
            $alunoInep = $objAluno->verificaInep(cod_aluno: $this->ref_cod_aluno);
            $objSerie = new clsPmieducarSerie(cod_serie: $this->ref_cod_serie);
            $serieDet = $objSerie->detalhe();
            $exigeInep = $serieDet['exigir_inep'];

            if (!$alunoInep && $exigeInep) {
                $this->mensagem = 'Não foi possível realizar matrícula, necessário inserir o INEP no cadastro do aluno.';

                return false;
            }

            $obj_reserva_vaga = new clsPmieducarReservaVaga();

            $lst_reserva_vaga = $obj_reserva_vaga->lista(
                int_ref_ref_cod_escola: $this->ref_cod_escola,
                int_ref_ref_cod_serie: $this->ref_cod_serie,
                int_ref_cod_aluno: $this->ref_cod_aluno,
                int_ativo: 1
            );

            // Verifica se existe reserva de vaga para o aluno
            if (is_array(value: $lst_reserva_vaga)) {
                $det_reserva_vaga = array_shift(array: $lst_reserva_vaga);
                $this->ref_cod_reserva_vaga = $det_reserva_vaga['cod_reserva_vaga'];

                $obj_reserva_vaga = new clsPmieducarReservaVaga(
                    cod_reserva_vaga: $this->ref_cod_reserva_vaga,
                    ref_ref_cod_escola: null,
                    ref_ref_cod_serie: null,
                    ref_usuario_exc: $this->pessoa_logada,
                    ref_usuario_cad: null,
                    ref_cod_aluno: null,
                    data_cadastro: null,
                    data_exclusao: null,
                    ativo: 0
                );

                $editou = $obj_reserva_vaga->edita();

                if (!$editou) {
                    $this->mensagem = 'Edição não realizada.<br />';

                    return false;
                }
            }

            $vagas_restantes = 1;

            if (!$this->ref_cod_reserva_vaga) {
                $obj_turmas = new clsPmieducarTurma();

                $lst_turmas = $obj_turmas->lista(
                    int_ref_ref_cod_serie: $this->ref_cod_serie,
                    int_ref_ref_cod_escola: $this->ref_cod_escola,
                    int_ativo: 1,
                    bool_verifica_serie_multiseriada: true
                );

                if (is_array(value: $lst_turmas)) {
                    $total_vagas = 0;

                    foreach ($lst_turmas as $turmas) {
                        $total_vagas += $turmas['max_aluno'];
                    }
                } else {
                    $this->mensagem = 'A série selecionada não possui turmas cadastradas.<br />';

                    return false;
                }

                $obj_matricula = new clsPmieducarMatricula();

                $lst_matricula = $obj_matricula->lista(
                    int_ref_ref_cod_escola: $this->ref_cod_escola,
                    int_ref_ref_cod_serie: $this->ref_cod_serie,
                    int_aprovado: 3,
                    int_ativo: 1,
                    int_ano: $this->ano,
                    int_ref_cod_curso2: $this->ref_cod_curso,
                    int_ref_cod_instituicao: $this->ref_cod_instituicao,
                    int_ultima_matricula: 1
                );

                if (is_array(value: $lst_matricula)) {
                    $matriculados = count(value: $lst_matricula);
                }

                $obj_reserva_vaga = new clsPmieducarReservaVaga();

                $lst_reserva_vaga = $obj_reserva_vaga->lista(
                    int_ref_ref_cod_escola: $this->ref_cod_escola,
                    int_ref_ref_cod_serie: $this->ref_cod_serie,
                    int_ativo: 1,
                    int_ref_cod_instituicao: $this->ref_cod_instituicao,
                    int_ref_cod_curso: $this->ref_cod_curso
                );

                if (is_array(value: $lst_reserva_vaga)) {
                    $reservados = count(value: $lst_reserva_vaga);
                }

                $vagas_restantes = $total_vagas - ($matriculados + $reservados);
            }

            if ($vagas_restantes <= 0) {
                echo sprintf(
                    '<script>
                        var msg = \'\';
                        msg += \'Excedido o n\u00famero de total de vagas para Matr\u00cdcula!\\n\';
                        msg += \'N\u00famero total de matriculados: %d\\n\';
                        msg += \'N\u00famero total de vagas reservadas: %d\\n\';
                        msg += \'N\u00famero total de vagas: %d\\n\';
                        msg += \'Deseja mesmo assim realizar a Matr\u00cdcula?\';
                        if (!confirm(msg)) {
                          window.location = \'educar_aluno_det.php?cod_aluno=%d\';
                        }
                    </script>',
                    $matriculados,
                    $reservados,
                    $total_vagas,
                    $this->ref_cod_aluno
                );
            }

            $obj_matricula_aluno = new clsPmieducarMatricula();

            $obj_matricula_aluno->lista(ref_cod_aluno: $this->ref_cod_aluno);

            if ($this->is_padrao == 1) {
                $this->semestre = null;
            }

            if (!$this->removerFlagUltimaMatricula(alunoId: $this->ref_cod_aluno)) {
                return false;
            }

            $db->Consulta(consulta: "SELECT *
                             FROM pmieducar.matricula m
                            WHERE m.ano = {$this->ano}
                              AND m.aprovado = 3
                              AND m.ativo = 1
                              AND m.ref_cod_aluno = {$this->ref_cod_aluno}
                              AND m.ref_ref_cod_serie = {$this->ref_cod_serie}
                              AND m.ref_ref_cod_escola = {$this->ref_cod_escola}
                              AND dependencia ");

            $db->ProximoRegistro();
            $m = $db->Tupla();

            if (is_array(value: $m) && count(value: $m) && $dependencia) {
                $this->mensagem = 'Esse aluno já tem uma matrícula de dependência nesta escola e série.';

                return false;
            }

            $reloadReserva = Session::get(key: 'reload_reserva_vaga');

            $obj_CandidatoReservaVaga = new clsPmieducarCandidatoReservaVaga();

            $lst_CandidatoReservaVaga = $obj_CandidatoReservaVaga->lista(
                ano_letivo: $this->ano,
                ref_cod_serie: $this->ref_cod_serie,
                ref_cod_aluno: $this->ref_cod_aluno,
                situacaoEmEspera: true
            );

            $count = is_array(value: $lst_CandidatoReservaVaga) ? count(value: $lst_CandidatoReservaVaga) : 0;
            $countEscolasDiferentes = 0;
            $countEscolasIguais = 0;

            if (is_array(value: $lst_CandidatoReservaVaga)) {
                for ($i = 0; $i < $count; $i++) {
                    if ($lst_CandidatoReservaVaga[$i]['ref_cod_escola'] != $this->ref_cod_escola) {
                        $countEscolasDiferentes = $countEscolasDiferentes + 1;
                    } elseif ($lst_CandidatoReservaVaga[$i]['ref_cod_escola'] == $this->ref_cod_escola) {
                        $countEscolasIguais = $countEscolasIguais + 1;
                    }
                }

                if (($countEscolasDiferentes > 0) && (!$reloadReserva)) {
                    echo '<script type="text/javascript">
                      var msg = \'' . 'O aluno possui uma reserva de vaga em outra escola, deseja matricula-lo assim mesmo?' . '\';
                      if (!confirm(msg)) {
                        window.location = \'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno . '\';
                      } else {
                        parent.document.getElementById(\'formcadastro\').submit();
                      }
                    </script>';

                    $reloadReserva = 1;

                    Session::put(key: 'reload_reserva_vaga', value: $reloadReserva);
                    Session::save();
                    Session::start();

                    return true;
                } elseif (($countEscolasDiferentes > 0) && ($reloadReserva == 1)) {
                    $obj_CandidatoReservaVaga->atualizaDesistente(
                        ano_letivo: $this->ano,
                        ref_cod_serie: $this->ref_cod_serie,
                        ref_cod_aluno: $this->ref_cod_aluno,
                        ref_cod_escola: $this->ref_cod_escola
                    );
                }
            }

            $this->data_matricula = Portabilis_Date_Utils::brToPgSQL(date: $this->data_matricula);

            $obj = new clsPmieducarMatricula(
                ref_cod_reserva_vaga: $this->ref_cod_reserva_vaga,
                ref_ref_cod_escola: $this->ref_cod_escola,
                ref_ref_cod_serie: $this->ref_cod_serie,
                ref_usuario_cad: $this->pessoa_logada,
                ref_cod_aluno: $this->ref_cod_aluno,
                aprovado: 3,
                ativo: 1,
                ano: $this->ano,
                ultima_matricula: 1,
                ref_cod_curso: $this->ref_cod_curso,
                semestre: $this->semestre,
                data_matricula: $this->data_matricula,
                observacoes: $this->observacoes
            );

            $dataMatriculaObj = new \DateTime(datetime: $this->data_matricula);
            $dataTransferencia = $obj->pegaDataDeTransferencia(cod_aluno: $this->ref_cod_aluno, ano: $this->ano);
            $dataAnoLetivoInicio = $obj->pegaDataAnoLetivoInicio(cod_turma: $this->ref_cod_turma);
            $dataAnoLetivoTermino = $obj->pegaDataAnoLetivoFim(cod_turma: $this->ref_cod_turma);

            /** @var LegacyInstitution $instituicao */
            $instituicao = app(abstract: LegacyInstitution::class);

            if (empty($dataAnoLetivoTermino)) {
                $this->mensagem = 'Não está definida a data de término do ano letivo.';

                return false;
            }

            if ($dataTransferencia && $dataMatriculaObj <= $dataTransferencia) {
                $this->mensagem = sprintf(
                    'Não é possível matricular o aluno. O mesmo possui enturmação com data de saída anterior à data informada. Favor alterar a data de matrícula para ser superior a %s.',
                    $dataTransferencia->format(format: 'd/m/Y')
                );

                return false;
            } elseif ($dataMatriculaObj < $dataAnoLetivoInicio) {
                if (!$instituicao->allowRegistrationOutAcademicYear) {
                    $this->mensagem = sprintf(
                        'A data de matrícula precisa ser igual ou maior que a data de início do ano letivo da escola ou turma (%s).',
                        $dataAnoLetivoInicio->format(format: 'd/m/Y')
                    );

                    return false;
                }
            }

            if ($dataMatriculaObj > $dataAnoLetivoTermino) {
                $this->mensagem = sprintf(
                    'A data de matrícula precisa ser igual ou menor que a data fim do ano letivo da escola ou turma (%s).',
                    $dataAnoLetivoTermino->format(format: 'd/m/Y')
                );

                return false;
            }

            $validarCamposEducacenso = $this->validarCamposObrigatoriosCenso();

            if (!empty($this->ref_cod_turma) && $validarCamposEducacenso && !$this->availableTimeService()->isAvailable(studentId: $this->ref_cod_aluno, schoolClassId: $this->ref_cod_turma)) {
                $this->mensagem = 'O aluno já está matriculado em uma turma com esse horário.';

                return false;
            }

            $obj->dependencia = $dependencia;
            $cadastrou = $obj->cadastra();
            $this->cod_matricula = $cadastrou;

            if ($cadastrou) {
                if ($countEscolasIguais > 0) {
                    $obj_crv = new clsPmieducarCandidatoReservaVaga(cod_candidato_reserva_vaga: $this->ref_cod_candidato_reserva_vaga);
                    $obj_crv->vinculaMatricula(ref_cod_escola: $this->ref_cod_escola, ref_cod_matricula: $this->cod_matricula, ref_cod_aluno: $this->ref_cod_aluno);
                }

                $this->enturmacaoMatricula(matriculaId: $this->cod_matricula, turmaDestinoId: $this->ref_cod_turma);

                $ultimaMatriculaSerieAno = LegacyRegistration::query()
                    ->active()
                    ->where('ref_cod_aluno', $this->ref_cod_aluno)
                    ->where('ref_ref_cod_serie', $this->ref_cod_serie)
                    ->where('ano', $this->ano)
                    ->whereNot('cod_matricula', $this->cod_matricula)
                    ->orderBy('cod_matricula', 'desc')
                    ->first();

                if ($ultimaMatriculaSerieAno->aprovado == App_Model_MatriculaSituacao::TRANSFERIDO) {
                    /** @var LegacyRegistration $registration */
                    $registration = LegacyRegistration::find(id: $this->cod_matricula);

                    $mensagem = '';

                    try {
                        event(new RegistrationEvent(registration: $registration));
                    } catch (TransferException $exception) {
                        $mensagem = 'Não foi possível copiar os dados da matrícula antiga. ' . $exception->getMessage();
                    } catch (RegistrationException $exception) {
                        $this->mensagem = 'Não é possível concluir a matrícula. ' . $exception->getMessage();

                        DB::rollBack();
                        $this->simpleRedirect(url: 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);
                    }

                    $promocao = new PromotionService(enrollment: $registration->enrollments()->first());
                    $promocao->fakeRequest();
                }

                $this->mensagem = 'Cadastro efetuado com sucesso.<br />' . $mensagem;

                DB::commit();
                $this->simpleRedirect(url: 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);
            }

            $this->mensagem = 'Cadastro não realizado.<br />';

            return false;
        } else {
            $this->mensagem = 'O ano (letivo) selecionado não está em andamento na escola selecionada.<br />';

            return false;
        }
    }

    public function permiteDependenciaAnoConcluinte()
    {
        $instituicao = new clsPmieducarInstituicao(cod_instituicao: $this->ref_cod_instituicao);
        $instituicao = $instituicao->detalhe();
        $serie = new clsPmieducarSerie(cod_serie: $this->ref_cod_serie);
        $serie = $serie->detalhe();
        $reprovaDependenciaAnoConcluinte = $instituicao['reprova_dependencia_ano_concluinte'];
        $anoConcluinte = $serie['concluinte'] == 2;

        return !(dbBool(val: $reprovaDependenciaAnoConcluinte) && $anoConcluinte);
    }

    public function verificaQtdeDependenciasPermitida()
    {
        $matriculasDependencia =
            Portabilis_Utils_Database::fetchPreparedQuery(sql: "SELECT *
                                                             FROM pmieducar.matricula
                                                            WHERE matricula.ano = {$this->ano}
                                                              AND matricula.ref_cod_aluno = {$this->ref_cod_aluno}
                                                              AND matricula.dependencia = TRUE
                                                              AND matricula.aprovado = 3
                                                              AND matricula.ativo = 1");

        $matriculasDependencia = count(value: $matriculasDependencia);

        $db = new clsBanco();
        $matriculasDependenciaPermitida = $db->CampoUnico(consulta: "SELECT regra_avaliacao.qtd_matriculas_dependencia
                                                             FROM pmieducar.serie
                                                       INNER JOIN modules.regra_avaliacao_serie_ano AS rasa ON (rasa.serie_id = serie.cod_serie AND rasa.ano_letivo = {$this->ano})
                                                       INNER JOIN modules.regra_avaliacao ON (regra_avaliacao.id = rasa.regra_avaliacao_id)
                                                            WHERE serie.cod_serie = {$this->ref_cod_serie}");

        if ($matriculasDependencia >= $matriculasDependenciaPermitida) {
            $this->mensagem = "A regra desta série limita a quantidade de matrículas de dependência para {$matriculasDependenciaPermitida}.";

            return false;
        }

        return true;
    }

    public function verificaAlunoFalecido()
    {
        $aluno = new clsPmieducarAluno(cod_aluno: $this->ref_cod_aluno);
        $aluno = $aluno->detalhe();

        $pessoa = new clsPessoaFisica(int_idpes: $aluno['ref_idpes']);
        $pessoa = $pessoa->detalhe();

        return dbBool(val: $pessoa['falecido']);
    }

    public function bloqueiaMatriculaSerieNaoSeguinte()
    {
        $instituicao = new clsPmieducarInstituicao(cod_instituicao: $this->ref_cod_instituicao);
        $instituicao = $instituicao->detalhe();

        return dbBool(val: $instituicao['bloqueia_matricula_serie_nao_seguinte']);
    }

    public function permiteMatriculaSerieDestino()
    {
        $objMatricula = new clsPmieducarMatricula;

        $dadosUltimaMatricula = $objMatricula->getDadosUltimaMatricula(codAluno: $this->ref_cod_aluno);
        $this->situacaoUltimaMatricula = $dadosUltimaMatricula[0]['aprovado'];
        $this->serieUltimaMatricula = $dadosUltimaMatricula[0]['ref_ref_cod_serie'];
        $this->anoUltimaMatricula = $dadosUltimaMatricula[0]['ano'];
        $aprovado = [1, 12, 13];
        $reprovado = [2, 14];

        if (!$dadosUltimaMatricula) {
            return true;
        }

        if ($this->situacaoUltimaMatricula == $this->transferido) {
            return true;
        }

        if (in_array(needle: $this->situacaoUltimaMatricula, haystack: $aprovado)) {
            $serieNovaMatricula = LegacySequenceGrade::query()->whereGradeOrigin($this->serieUltimaMatricula)->active()->value('ref_serie_destino');
        } elseif (in_array(needle: $this->situacaoUltimaMatricula, haystack: $reprovado)) {
            $serieNovaMatricula = $this->serieUltimaMatricula;
        }

        if ($this->ref_cod_serie == $serieNovaMatricula) {
            return true;
        }

        return false;
    }

    private function validaAlunoAtivo(): bool
    {
        return LegacyStudent::where('cod_aluno', $this->ref_cod_aluno)->active()->exists();
    }

    private function validaPeriodoDeMatriculasPelaDataFechamento(): bool
    {
        $instituicao = app(abstract: LegacyInstitution::class);

        if (empty($instituicao->data_fechamento)) {
            return true;
        }

        $dataFechamento = explode(separator: '-', string: $instituicao->data_fechamento);
        $dataFechamento = $this->ano . '-' . $dataFechamento[1] . '-' . $dataFechamento[2];
        $dataMatricula = Portabilis_Date_Utils::brToPgSQL(date: $this->data_matricula);

        return $dataMatricula <= $dataFechamento;
    }

    public function desativaEnturmacoesMatricula($matriculaId)
    {
        $result = true;

        $enturmacoes = new clsPmieducarMatriculaTurma();
        $enturmacoes = $enturmacoes->lista(
            int_ref_cod_matricula: $matriculaId,
            int_ativo: 1
        );

        if ($enturmacoes) {
            foreach ($enturmacoes as $enturmacao) {
                $enturmacao = new clsPmieducarMatriculaTurma(
                    ref_cod_matricula: $matriculaId,
                    ref_cod_turma: $enturmacao['ref_cod_turma'],
                    ref_usuario_exc: $this->pessoa_logada,
                    ativo: 0,
                    sequencial: $enturmacao['sequencial']
                );

                $detEnturmacao = $enturmacao->detalhe();
                $enturmacao->data_enturmacao = $detEnturmacao['data_enturmacao'];

                if ($result && !$enturmacao->edita()) {
                    $result = false;
                }
            }
        }

        if (!$result) {
            $this->mensagem = 'Não foi possível desativar as ' .
                'enturmações da matrícula.';
        }

        return $result;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_excluir(
            int_processo_ap: 627,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno
        );

        if (!$this->desativaEnturmacoesMatricula(matriculaId: $this->cod_matricula)) {
            return false;
        }

        $obj_matricula = new clsPmieducarMatricula(cod_matricula: $this->cod_matricula);
        $det_matricula = $obj_matricula->detalhe();
        $ref_cod_serie = $det_matricula['ref_ref_cod_serie'];

        $lst_sequencia = LegacySequenceGrade::query()
            ->whereGradeDestiny($ref_cod_serie)
            ->active()
            ->get()
            ->toArray();

        // Verifica se a série da matrícula cancelada é sequência de alguma outra série
        if (is_array(value: $lst_sequencia)) {
            $det_sequencia = array_shift(array: $lst_sequencia);
            $ref_serie_origem = $det_sequencia['ref_serie_origem'];

            $obj_matricula = new clsPmieducarMatricula();

            $lst_matricula = $obj_matricula->lista(
                int_ref_ref_cod_serie: $ref_serie_origem,
                ref_cod_aluno: $this->ref_cod_aluno,
                int_ativo: 1,
                int_ultima_matricula: 0
            );

            // Verifica se o aluno tem matrícula na série encontrada
            if (is_array(value: $lst_matricula)) {
                $det_matricula = array_shift(array: $lst_matricula);
                $ref_cod_matricula = $det_matricula['cod_matricula'];

                $obj = new clsPmieducarMatricula(
                    cod_matricula: $ref_cod_matricula,
                    ref_usuario_exc: $this->pessoa_logada,
                    ativo: 1,
                    ultima_matricula: 1
                );

                $editou1 = $obj->edita();

                if (!$editou1) {
                    $this->mensagem = 'Não foi possível editar a "&Uacute;ltima Matrícula da Sequência".<br />';

                    return false;
                }
            }
        }

        $obj = new clsPmieducarMatricula(
            cod_matricula: $this->cod_matricula,
            ref_usuario_exc: $this->pessoa_logada,
            ativo: 0
        );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $enrollments = LegacyEnrollment::query()
                ->where('ref_cod_matricula', $this->cod_matricula)
                ->get();

            $enrollmentService = new EnrollmentService(auth()->user());

            foreach ($enrollments as $enrollment) {
                $enrollmentService->reorderSchoolClass($enrollment);
            }

            $this->mensagem = 'Exclusão efetuada com sucesso.<br />';

            throw new HttpResponseException(
                response: new RedirectResponse(url: "educar_aluno_det.php?cod_aluno={$this->ref_cod_aluno}")
            );
        }

        $this->mensagem = 'Exclusão não realizada.<br />';

        return false;
    }

    protected function removerFlagUltimaMatricula($alunoId)
    {
        $matriculas = new clsPmieducarMatricula();

        $matriculas = $matriculas->lista(
            ref_cod_aluno: $this->ref_cod_aluno,
            int_ativo: 1,
            int_ultima_matricula: 1
        );

        foreach ($matriculas as $matricula) {
            if (!$matricula['aprovado'] == 3) {
                $matricula = new clsPmieducarMatricula(
                    cod_matricula: $matricula['cod_matricula'],
                    ref_usuario_exc: $this->pessoa_logada,
                    ref_cod_aluno: $alunoId,
                    ativo: 1,
                    ultima_matricula: 0
                );

                if (!$matricula->edita()) {
                    $this->mensagem = 'Erro ao remover flag ultima matricula das matriculas anteriores.';

                    return false;
                }
            }
        }

        return true;
    }

    public function enturmacaoMatricula($matriculaId, $turmaDestinoId)
    {
        $enturmacaoExists = new clsPmieducarMatriculaTurma();

        $enturmacaoExists = $enturmacaoExists->lista(
            int_ref_cod_matricula: $matriculaId,
            int_ref_cod_turma: $turmaDestinoId,
            int_ativo: 1
        );

        $enturmacaoExists = is_array(value: $enturmacaoExists) && count(value: $enturmacaoExists) > 0;

        if (!$enturmacaoExists) {
            $enturmacao = new clsPmieducarMatriculaTurma(
                ref_cod_matricula: $matriculaId,
                ref_cod_turma: $turmaDestinoId,
                ref_usuario_exc: $this->pessoa_logada,
                ref_usuario_cad: $this->pessoa_logada,
                ativo: 1
            );

            $enturmacao->data_enturmacao = $this->data_matricula;

            return $enturmacao->cadastra();
        }

        return false;
    }

    public function existeVagasDisponiveis()
    {
        $dependencia = $this->dependencia == 'on';
        if (!$dependencia) {
            // Caso quantidade de matrículas naquela turma seja maior ou igual que a capacidade da turma deve bloquear
            if ($this->_getQtdMatriculaTurma() >= $this->_getMaxAlunoTurma()) {
                $this->mensagem = 'Não existem vagas disponíveis para essa turma! <br/>';

                return false;
            }

            // Caso a capacidade de alunos naquele turno seja menor ou igual ao ao número de alunos matrículados + alunos na reserva de vaga externa deve bloquear
            if ($this->_getMaxAlunoTurno() <= ($this->_getQtdAlunosFila() + $this->_getQtdMatriculaTurno())) {
                $this->mensagem = 'Não existem vagas disponíveis para essa série/turno! <br/>';

                return false;
            }
        }

        return true;
    }

    public function _getQtdMatriculaTurma()
    {
        $obj_mt = new clsPmieducarMatriculaTurma();
        $lst_mt = $obj_mt->enturmacoesSemDependencia(turmaId: $this->ref_cod_turma);

        return $lst_mt[0];
    }

    public function _getMaxAlunoTurma()
    {
        $obj_t = new clsPmieducarTurma(cod_turma: $this->ref_cod_turma);
        $det_t = $obj_t->detalhe();

        return $det_t['max_aluno'];
    }

    public function _getMaxAlunoTurno()
    {
        $obj_t = new clsPmieducarTurma();
        $det_t = $obj_t->detalhe();

        $lista_t = $obj_t->lista(
            int_ref_ref_cod_serie: $this->ref_cod_serie,
            int_ref_ref_cod_escola: $this->ref_cod_escola,
            turma_turno_id: $det_t['turma_turno_id'],
            ano: $this->ano
        );

        $max_aluno_turmas = 0;

        foreach ($lista_t as $reg) {
            $max_aluno_turmas += $reg['max_aluno'];
        }

        return $max_aluno_turmas;
    }

    public function _getQtdAlunosFila()
    {
        $obj_t = new clsPmieducarTurma(cod_turma: $this->ref_cod_turma);
        $det_t = $obj_t->detalhe();

        $sql = 'SELECT count(1) as qtd
                  FROM pmieducar.matricula
                 WHERE ano = $1
                   AND ref_ref_cod_escola = $2
                   AND ref_cod_curso = $3
                   AND ref_ref_cod_serie = $4
                   AND turno_pre_matricula = $5
                   AND aprovado = 11 ';

        return (int) Portabilis_Utils_Database::selectField(sql: $sql, paramsOrOptions: [$this->ano, $this->ref_cod_escola, $this->ref_cod_curso, $this->ref_cod_serie, $det_t['turma_turno_id']]);
    }

    public function _getQtdMatriculaTurno()
    {
        $obj_t = new clsPmieducarTurma(cod_turma: $this->ref_cod_turma);
        $det_t = $obj_t->detalhe();
        $obj_mt = new clsPmieducarMatriculaTurma();

        $obj_mt->lista(
            int_ativo: 1,
            int_ref_cod_serie: $this->ref_cod_serie,
            int_ref_cod_curso: $this->ref_cod_curso,
            int_ref_cod_escola: $this->ref_cod_escola,
            int_turma_turno_id: $det_t['turma_turno_id'],
            int_ano_turma: $det_t['ano'],
            dependencia: 'f'
        );

        return $obj_mt->_total;
    }

    private function availableTimeService()
    {
        if (empty($this->availableTimeService)) {
            $this->availableTimeService = new AvailableTimeService();
        }

        $this->availableTimeService->onlySchoolClassesInformedOnCensus();

        return $this->availableTimeService;
    }

    public function Formular()
    {
        $this->title = 'Matrícula';
        $this->processoAp = 578;
    }
};
