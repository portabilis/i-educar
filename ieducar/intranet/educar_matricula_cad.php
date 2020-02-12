<?php

use App\Exceptions\Transfer\TransferException;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegistration;
use App\Services\PromotionService;
use App\Services\SchoolClass\AvailableTimeService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Events\RegistrationEvent;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/CoreExt/Controller/Request.php';
require_once 'modules/Avaliacao/Views/PromocaoApiController.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Matrícula');
        $this->processoAp = 578;
    }
}

class indice extends clsCadastro
{
    public $pessoa_logada;

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

    public $ref_cod_instituicao;

    public $ref_cod_curso;

    public $ref_cod_escola;

    public $ref_cod_turma;

    public $semestre;

    public $is_padrao;

    public $dependencia;

    public $ref_cod_candidato_reserva_vaga;

    public $ref_cod_candidato_fila_unica;

    public $ref_cod_turma_copiar_enturmacoes;

    private $availableTimeService;

    private $transferido = 4;

    private $situacaoUltimaMatricula;

    private $serieUltimaMatricula;

    private $anoUltimaMatricula;

    public function Inicializar()
    {
        $this->ref_cod_turma_copiar_enturmacoes = $this->getQueryString('ref_cod_turma_copiar_enturmacoes');
        $this->cod_matricula = $this->getQueryString('cod_matricula');
        $this->ref_cod_aluno = $this->getQueryString('ref_cod_aluno');
        $this->ref_cod_candidato_reserva_vaga = $this->getQueryString('ref_cod_candidato_reserva_vaga');
        $this->ref_cod_candidato_fila_unica = $this->getQueryString('cod_candidato_fila_unica');

        $retorno = $this->ref_cod_turma_copiar_enturmacoes ? 'Enturmar' : 'Novo';
        $obj_aluno = new clsPmieducarAluno($this->ref_cod_aluno);

        if (!$obj_aluno->existe() and !$this->ref_cod_turma_copiar_enturmacoes) {
            throw new HttpResponseException(
                new RedirectResponse('educar_aluno_lst.php')
            );
        }

        if ($this->ref_cod_turma_copiar_enturmacoes) {
            $this->nome_url_sucesso = Portabilis_String_Utils::toLatin1('Gravar enturmações');
            $url = route('enrollments.batch.enroll.index', ['schoolClass' => $this->ref_cod_turma_copiar_enturmacoes]);
        } else {
            $url = 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno;
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, $url);

        if (is_numeric($this->cod_matricula)) {
            if ($obj_permissoes->permissao_excluir(627, $this->pessoa_logada, 7)) {
                $this->Excluir();
            }
        }

        $this->nome_url_cancelar = 'Cancelar';
        $this->url_cancelar = $url;

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Nova';

        $this->breadcrumb("{$nomeMenu} matrícula", [
            'educar_index.php' => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('ref_cod_turma_copiar_enturmacoes', $this->ref_cod_turma_copiar_enturmacoes);
        $this->campoOculto('cod_matricula', $this->cod_matricula);
        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);
        $this->campoOculto('ref_cod_candidato_reserva_vaga', $this->ref_cod_candidato_reserva_vaga);
        $this->campoOculto('ref_cod_candidato_fila_unica', $this->ref_cod_candidato_fila_unica);

        if ($this->ref_cod_aluno) {
            $obj_aluno = new clsPmieducarAluno();

            $lst_aluno = $obj_aluno->lista(
                $this->ref_cod_aluno,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                1
            );

            if (is_array($lst_aluno)) {
                $det_aluno = array_shift($lst_aluno);
                $this->nm_aluno = $det_aluno['nome_aluno'];
                $this->campoRotulo('nm_aluno', 'Aluno', $this->nm_aluno);
            }

            /*
             * Verifica se existem matrículas para o aluno para apresentar o campo
             * transferência, necessário para o relatório de movimentação mensal.
             */
            $obj_matricula = new clsPmieducarMatricula();

            $lst_matricula = $obj_matricula->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                $this->ref_cod_aluno
            );
        }

        if ($this->ref_cod_turma_copiar_enturmacoes) {
            $this->nome_url_sucesso = Portabilis_String_Utils::toLatin1('Gravar enturmações');
        }
        // inputs

        $anoLetivoHelperOptions = ['situacoes' => ['em_andamento', 'nao_iniciado']];

        $this->inputsHelper()->dynamic(['ano', 'instituicao', 'escola', 'curso', 'serie', 'turma']);
        $this->inputsHelper()->date('data_matricula', ['label' => Portabilis_String_Utils::toLatin1('Data da matrícula'), 'placeholder' => 'dd/mm/yyyy', 'value' => date('d/m/Y')]);
        $this->inputsHelper()->hidden('ano_em_andamento', ['value' => '1']);

        if (config('legacy.app.matricula.dependencia') == 1) {
            $this->inputsHelper()->checkbox(
                'dependencia',
                [
                    'label' => Portabilis_String_Utils::toLatin1('Matrícula de dependência?'),
                    'value' => $this->dependencia
                ]
            );
        }

        if (is_numeric($this->ref_cod_curso)) {
            $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
            $det_curso = $obj_curso->detalhe();

            if (is_numeric($det_curso['ref_cod_tipo_avaliacao'])) {
                $this->campoOculto('apagar_radios', $det_curso['padrao_ano_escolar']);
                $this->campoOculto('is_padrao', $det_curso['padrao_ano_escolar']);
            }
        }

        $script = ['/modules/Cadastro/Assets/Javascripts/Matricula.js'];
        Portabilis_View_Helper_Application::loadJavascript($this, $script);

        $this->acao_enviar = 'formUtils.submit()';
    }

    protected function getCurso($id)
    {
        $curso = new clsPmieducarCurso($id);

        return $curso->detalhe();
    }

    private function getEnturmacoesNaTurma($turma)
    {
        return (array)Portabilis_Utils_Database::fetchPreparedQuery("
            select *
            from pmieducar.matricula_turma
            where ref_cod_turma = {$turma}
            and ativo = 1
        ");
    }

    private function getMaximoAlunosNaTurma($turma)
    {
        return (int)(new clsBanco())->CampoUnico("
            select max_aluno
            from pmieducar.turma
            where cod_turma = $turma
        ");
    }

    private function getMatricula($matricula)
    {
        $matriculas = Portabilis_Utils_Database::fetchPreparedQuery("
            select *
            from pmieducar.matricula
            where cod_matricula = {$matricula}
            limit 1
        ");

        if (is_array($matriculas) && count($matriculas)) {
            return array_shift($matriculas);
        }

        throw new Exception("A matrícula {$matricula} não foi encontrada.");
    }

    private function addMatricula($escola, $curso, $serie, $aluno, $ano)
    {
        $datahora = date('Y-m-d H:i:s');

        $obj = new clsPmieducarMatricula(
            null,
            null,
            $escola,
            $serie,
            null,
            $this->pessoa_logada,
            $aluno,
            3,
            null,
            null,
            1,
            $ano,
            1,
            null,
            null,
            null,
            null,
            $curso,
            null,
            1,
            $datahora
        );

        return $obj->cadastra();
    }

    private function addEnturmacao($matricula, $turma, $sequencial, $ativo)
    {
        $data = date('Y-m-d');
        $datahora = date('Y-m-d H:i:s');
        $usuario = $this->pessoa_logada;

        (new clsBanco)->CampoUnico("
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
            $enturmacoesNaTurmaDestino = $this->getEnturmacoesNaTurma($this->ref_cod_turma);
            $enturmacoesParaCopiar = $this->getEnturmacoesNaTurma($this->ref_cod_turma_copiar_enturmacoes);
        } catch (Exception $e) {
            $this->mensagem = 'Houve um erro ao buscar informações das turmas.';

            return false;
        }

        $maximoDeAlunosTurmaDestino = $this->getMaximoAlunosNaTurma($this->ref_cod_turma);
        $quantidadeAlunosNaTurmaDestino = count($enturmacoesNaTurmaDestino);
        $quantidadeAlunosParaCopiar = count($enturmacoesParaCopiar);
        $vagasDisponiveisTurmaDestino = $maximoDeAlunosTurmaDestino - $quantidadeAlunosNaTurmaDestino;

        if ($quantidadeAlunosParaCopiar > $vagasDisponiveisTurmaDestino) {
            $this->mensagem = 'A turma não tem saldo de vagas suficiente.';

            return false;
        }

        $mensagemErro = null;
        $validarCamposEducacenso = $this->validarCamposObrigatoriosCenso();

        foreach ($enturmacoesParaCopiar as $enturmar) {
            $dadosDaMatricula = $this->getMatricula($enturmar['ref_cod_matricula']);

            if ($validarCamposEducacenso && !$this->availableTimeService()->isAvailable($dadosDaMatricula['ref_cod_aluno'], $this->ref_cod_turma)) {
                $mensagemErro = 'O aluno já está matriculado em uma turma com esse horário.';
            }

            $matricula = $this->addMatricula(
                $this->ref_cod_escola,
                $this->ref_cod_curso,
                $this->ref_cod_serie,
                $dadosDaMatricula['ref_cod_aluno'],
                $this->ano
            );

            $this->addEnturmacao($matricula, $this->ref_cod_turma, $enturmar['sequencial'], $enturmar['ativo']);
        }

        if (!is_null($mensagemErro)) {
            $this->mensagem = $mensagemErro;

            return false;
        }

        throw new HttpResponseException(
            new RedirectResponse(
                route('enrollments.batch.enroll.index', ['schoolClass' => $this->ref_cod_turma])
            )
        );
    }

    public function Novo()
    {
        DB::beginTransaction();

        $dependencia = $this->dependencia == 'on';

        if ($dependencia && !$this->verificaQtdeDependenciasPermitida()) {
            return false;
        }

        if ($this->verificaAlunoFalecido()) {
            $this->mensagem = Portabilis_String_Utils::toLatin1('Não é possível matricular alunos falecidos.');
        }

        if (!$this->permiteMatriculaSerieDestino() && $this->bloqueiaMatriculaSerieNaoSeguinte()) {
            $this->mensagem = Portabilis_String_Utils::toLatin1('Não é possível matricular alunos em séries fora da sequência de enturmação.');

            return false;
        }

        $db = new clsBanco();
        $somente_do_bairro = $db->CampoUnico("SELECT matricula_apenas_bairro_escola FROM pmieducar.instituicao where cod_instituicao = {$this->ref_cod_instituicao}");

        if ($somente_do_bairro) {
            $db = new clsBanco();
            $bairro_escola = $db->CampoUnico("select Upper(nome) from public.bairro where idbai = (select idbai from cadastro.endereco_pessoa where idpes = (select ref_idpes from pmieducar.escola where cod_escola = {$this->ref_cod_escola}))");

            $db = new clsBanco();
            $bairro_aluno = $db->CampoUnico("select Upper(nome) from public.bairro where idbai = (select idbai from cadastro.endereco_pessoa where idpes = (select ref_idpes from pmieducar.aluno where cod_aluno = {$this->ref_cod_aluno}))");

            if (strcasecmp($bairro_aluno, $bairro_escola) != 0) {
                $this->mensagem = Portabilis_String_Utils::toLatin1('O aluno deve morar no mesmo bairro da escola');

                return false;
            }
        }

        $this->url_cancelar = 'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno;
        $this->nome_url_cancelar = 'Cancelar';

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            578,
            $this->pessoa_logada,
            7,
            'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno
        );

        //novas regras matricula aluno
        $this->ano = $_POST['ano'];
        $anoLetivoEmAndamentoEscola = new clsPmieducarEscolaAnoLetivo();

        $anoLetivoEmAndamentoEscola = $anoLetivoEmAndamentoEscola->lista(
            $this->ref_cod_escola,
            $this->ano,
            null,
            null,
            1, /*somente em andamento */
            null,
            null,
            null,
            null,
            1
        );

        $objEscolaSerie = new clsPmieducarEscolaSerie();
        $dadosEscolaSerie = $objEscolaSerie->lista($this->ref_cod_escola, $this->ref_cod_serie);

        if (!$this->existeVagasDisponiveis() && $dadosEscolaSerie[0]['bloquear_enturmacao_sem_vagas']) {
            return false;
        }

        if (is_array($anoLetivoEmAndamentoEscola)) {
            require_once 'include/pmieducar/clsPmieducarSerie.inc.php';
            $db = new clsBanco();

            $db->Consulta("SELECT ref_ref_cod_serie, ref_cod_curso
                             FROM pmieducar.matricula
                            WHERE ano = $this->ano
                              AND ativo = 1
                              AND ref_ref_cod_escola = $this->ref_cod_escola
                              AND ref_cod_curso = $this->ref_cod_curso
                              AND ref_cod_aluno = $this->ref_cod_aluno
                              AND aprovado = 3
                              AND dependencia = FALSE");

            $db->ProximoRegistro();
            $m = $db->Tupla();

            if (is_array($m) && count($m) && !$dependencia) {
                $curso = $this->getCurso($this->ref_cod_curso);

                if ($m['ref_ref_cod_serie'] == $this->ref_cod_serie) {
                    $this->mensagem = 'Este aluno j&aacute; est&aacute; matriculado nesta s&eacute;rie e curso, n&atilde;o &eacute; possivel matricular um aluno mais de uma vez na mesma s&eacute;rie.<br />';

                    return false;
                } elseif ($curso['multi_seriado'] != 1) {
                    $serie = new clsPmieducarSerie($m['ref_ref_cod_serie'], null, null, $m['ref_cod_curso']);
                    $serie = $serie->detalhe();

                    if (is_array($serie) && count($serie)) {
                        $nomeSerie = $serie['nm_serie'];
                    } else {
                        $nomeSerie = '';
                    }

                    $this->mensagem = "Este aluno j&aacute; est&aacute; matriculado no(a) '$nomeSerie' deste curso e escola. Como este curso n&atilde;o &eacute; multisseriado, n&atilde;o &eacute; possivel manter mais de uma matricula em andamento para o mesmo curso.<br />";

                    return false;
                }
            } else {
                $db->Consulta("select ref_ref_cod_escola, ref_cod_curso, ref_ref_cod_serie, ano from pmieducar.matricula where ativo = 1 and ref_ref_cod_escola != $this->ref_cod_escola and ref_cod_aluno = $this->ref_cod_aluno AND dependencia = FALSE and aprovado = 3 and not exists (select 1 from pmieducar.transferencia_solicitacao as ts where ts.ativo = 1 and ts.ref_cod_matricula_saida = matricula.cod_matricula )");

                $db->ProximoRegistro();
                $m = $db->Tupla();

                if (is_array($m) && count($m) && !$dependencia) {
                    $mesmoCursoAno = ($m['ref_cod_curso'] == $this->ref_cod_curso && $m['ano'] == $this->ano);
                    $cursoADeferir = new clsPmieducarCurso($this->ref_cod_curso);
                    $cursoDeAtividadeComplementar = $cursoADeferir->cursoDeAtividadeComplementar();

                    if (($mesmoCursoAno || config('legacy.app.matricula.multiplas_matriculas') === 0) && !$cursoDeAtividadeComplementar) {
                        require_once 'include/pmieducar/clsPmieducarEscola.inc.php';
                        require_once 'include/pessoa/clsJuridica.inc.php';

                        $serie = new clsPmieducarSerie($m['ref_ref_cod_serie'], null, null, $m['ref_cod_curso']);
                        $serie = $serie->detalhe();

                        if (is_array($serie) && count($serie)) {
                            $serie = $serie['nm_serie'];
                        } else {
                            $serie = '';
                        }

                        $escola = new clsPmieducarEscola($m['ref_ref_cod_escola']);
                        $escola = $escola->detalhe();

                        if (is_array($escola) && count($escola)) {
                            $escola = new clsJuridica($escola['ref_idpes']);
                            $escola = $escola->detalhe();
                            if (is_array($escola) && count($escola)) {
                                $escola = $escola['fantasia'];
                            } else {
                                $escola = '';
                            }
                        } else {
                            $escola = '';
                        }

                        $curso = new clsPmieducarCurso($m['ref_cod_curso']);
                        $curso = $curso->detalhe();

                        if (is_array($curso) && count($curso)) {
                            $curso = $curso['nm_curso'];
                        } else {
                            $curso = '';
                        }

                        $this->mensagem = "Este aluno j&aacute; est&aacute; matriculado no(a) '$serie' do curso '$curso' na escola '$escola', para matricular este aluno na sua escola solicite transfer&ecirc;ncia ao secret&aacute;rio(a) da escola citada.<br />";

                        return false;
                    }
                }
            }

            $serie = new clsPmieducarSerie($this->ref_cod_serie);
            $detSerie = $serie->detalhe();

            $alertaFaixaEtaria = $detSerie['alerta_faixa_etaria'];
            $bloquearMatriculaFaixaEtaria = $detSerie['bloquear_matricula_faixa_etaria'];

            $verificarDataCorte = $alertaFaixaEtaria || $bloquearMatriculaFaixaEtaria;

            $reload = Session::get('reload_faixa_etaria');

            if ($verificarDataCorte && !$reload) {
                $instituicao = new clsPmiEducarInstituicao($this->ref_cod_instituicao);
                $instituicao = $instituicao->detalhe();

                $dataCorte = $instituicao['data_base_matricula'];
                $idadeInicial = $detSerie['idade_inicial'];
                $idadeFinal = $detSerie['idade_final'];

                $objAluno = new clsPmieducarAluno($this->ref_cod_aluno);
                $detAluno = $objAluno->detalhe();

                $objPes = new clsPessoaFisica($detAluno['ref_idpes']);
                $detPes = $objPes->detalhe();

                $dentroPeriodoCorte = $serie->verificaPeriodoCorteEtarioDataNascimento($detPes['data_nasc'], $this->ano);

                if ($bloquearMatriculaFaixaEtaria && !$dentroPeriodoCorte) {
                    $this->mensagem = Portabilis_String_Utils::toLatin1('Não foi possível realizar a matrícula, pois a idade do aluno está fora da faixa etária da série');

                    return false;
                } elseif ($alertaFaixaEtaria && !$dentroPeriodoCorte) {
                    echo '<script type="text/javascript">
                        var msg = \'' . Portabilis_String_Utils::toLatin1('A idade do aluno encontra-se fora da faixa etária pré-definida na série, deseja continuar com a matrícula?') . '\';
                        if (!confirm(msg)) {
                          window.location = \'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno . '\';
                        } else {
                          parent.document.getElementById(\'formcadastro\').submit();
                        }
                    </script>';

                    //Permite que o usuário possa salvar a matrícula na próxima tentativa
                    $reload = 1;

                    Session::put('reload_faixa_etaria', $reload);
                    Session::save();
                    Session::start();

                    return true;
                }
            }

            $objAluno = new clsPmieducarAluno();
            $alunoInep = $objAluno->verificaInep($this->ref_cod_aluno);
            $objSerie = new clsPmieducarSerie($this->ref_cod_serie);
            $serieDet = $objSerie->detalhe();
            $exigeInep = $serieDet['exigir_inep'];

            if (!$alunoInep && $exigeInep) {
                $this->mensagem = 'N&atilde;o foi poss&iacute;vel realizar matr&iacute;cula, necess&aacute;rio inserir o INEP no cadastro do aluno.';

                return false;
            }

            $obj_reserva_vaga = new clsPmieducarReservaVaga();

            $lst_reserva_vaga = $obj_reserva_vaga->lista(
                null,
                $this->ref_cod_escola,
                $this->ref_cod_serie,
                null,
                null,
                $this->ref_cod_aluno,
                null,
                null,
                null,
                null,
                1
            );

            // Verifica se existe reserva de vaga para o aluno
            if (is_array($lst_reserva_vaga)) {
                $det_reserva_vaga = array_shift($lst_reserva_vaga);
                $this->ref_cod_reserva_vaga = $det_reserva_vaga['cod_reserva_vaga'];

                $obj_reserva_vaga = new clsPmieducarReservaVaga(
                    $this->ref_cod_reserva_vaga,
                    null,
                    null,
                    $this->pessoa_logada,
                    null,
                    null,
                    null,
                    null,
                    0
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
                    null,
                    null,
                    null,
                    $this->ref_cod_serie,
                    $this->ref_cod_escola,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    1,
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
                    null,
                    null,
                    true
                );

                if (is_array($lst_turmas)) {
                    $total_vagas = 0;

                    foreach ($lst_turmas as $turmas) {
                        $total_vagas += $turmas['max_aluno'];
                    }
                } else {
                    $this->mensagem = 'A s&eacute;rie selecionada n&atilde;o possui turmas cadastradas.<br />';

                    return false;
                }

                $obj_matricula = new clsPmieducarMatricula();

                $lst_matricula = $obj_matricula->lista(
                    null,
                    null,
                    $this->ref_cod_escola,
                    $this->ref_cod_serie,
                    null,
                    null,
                    null,
                    3,
                    null,
                    null,
                    null,
                    null,
                    1,
                    $this->ano,
                    $this->ref_cod_curso,
                    $this->ref_cod_instituicao,
                    1
                );

                if (is_array($lst_matricula)) {
                    $matriculados = count($lst_matricula);
                }

                $obj_reserva_vaga = new clsPmieducarReservaVaga();

                $lst_reserva_vaga = $obj_reserva_vaga->lista(
                    null,
                    $this->ref_cod_escola,
                    $this->ref_cod_serie,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    1,
                    $this->ref_cod_instituicao,
                    $this->ref_cod_curso
                );

                if (is_array($lst_reserva_vaga)) {
                    $reservados = count($lst_reserva_vaga);
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

            $objInstituicao = new clsPmiEducarInstituicao($this->ref_cod_instituicao);
            $detInstituicao = $objInstituicao->detalhe();
            $controlaEspacoUtilizacaoAluno = $detInstituicao['controlar_espaco_utilizacao_aluno'];

            //se o parametro de controle de utilização de espaço estiver setado como verdadeiro
            if ($controlaEspacoUtilizacaoAluno) {
                $objTurma = new clsPmieducarTurma($this->ref_cod_turma);
                $maximoAlunosSala = $objTurma->maximoAlunosSala();
                $excedeuLimiteMatriculas = (($matriculados + $reservados) >= $maximoAlunosSala);

                if ($excedeuLimiteMatriculas) {
                    echo sprintf(
                        '<script>
                            var msg = \'\';
                            msg += \'A sala n\u00e3o comporta mais alunos!\\n\';
                            msg += \'N\u00famero total de matriculados: %d\\n\';
                            msg += \'N\u00famero total de vagas reservadas: %d\\n\';
                            msg += \'N\u00famero total de vagas: %d\\n\';
                            msg += \'M\u00e1ximo de alunos que a sala comporta: %d\\n\';
                            msg += \'N\u00e3o ser\u00e1 poss\u00edvel efetuar a matr\u00edcula do aluno.\';
                            alert(msg);
                        window.location = \'educar_aluno_det.php?cod_aluno=%d\';
                        </script>',
                        $matriculados,
                        $reservados,
                        $total_vagas,
                        $maximoAlunosSala,
                        $this->ref_cod_aluno
                    );

                    return false;
                }
            }

            $obj_matricula_aluno = new clsPmieducarMatricula();

            $lst_matricula_aluno = $obj_matricula_aluno->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                $this->ref_cod_aluno
            );

            if ($this->is_padrao == 1) {
                $this->semestre = null;
            }

            if (!$this->removerFlagUltimaMatricula($this->ref_cod_aluno)) {
                return false;
            }

            $db->Consulta("SELECT *
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

            if (is_array($m) && count($m) && $dependencia) {
                $this->mensagem = 'Esse aluno j&aacute; tem uma matr&iacute;cula de depend&ecirc;ncia nesta escola e s&eacute;rie.';

                return false;
            }

            $reloadReserva = Session::get('reload_reserva_vaga');

            $obj_CandidatoReservaVaga = new clsPmieducarCandidatoReservaVaga();

            $lst_CandidatoReservaVaga = $obj_CandidatoReservaVaga->lista(
                $this->ano,
                null,
                null,
                null,
                $this->ref_cod_serie,
                null,
                null,
                $this->ref_cod_aluno,
                true
            );

            $count = count($lst_CandidatoReservaVaga);
            $countEscolasDiferentes = 0;
            $countEscolasIguais = 0;

            if (is_array($lst_CandidatoReservaVaga)) {
                for ($i = 0; $i < $count; $i++) {
                    if ($lst_CandidatoReservaVaga[$i]['ref_cod_escola'] != $this->ref_cod_escola) {
                        $countEscolasDiferentes = $countEscolasDiferentes + 1;
                    } elseif ($lst_CandidatoReservaVaga[$i]['ref_cod_escola'] == $this->ref_cod_escola) {
                        $countEscolasIguais = $countEscolasIguais + 1;
                    }
                }

                if (($countEscolasDiferentes > 0) && (!$reloadReserva)) {
                    echo '<script type="text/javascript">
                      var msg = \'' . Portabilis_String_Utils::toLatin1('O aluno possui uma reserva de vaga em outra escola, deseja matricula-lo assim mesmo?') . '\';
                      if (!confirm(msg)) {
                        window.location = \'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno . '\';
                      } else {
                        parent.document.getElementById(\'formcadastro\').submit();
                      }
                    </script>';

                    $reloadReserva = 1;

                    Session::put('reload_reserva_vaga', $reloadReserva);
                    Session::save();
                    Session::start();

                    return true;
                } elseif (($countEscolasDiferentes > 0) && ($reloadReserva == 1)) {
                    $updateCandidatoReservaVaga = $obj_CandidatoReservaVaga->atualizaDesistente(
                        $this->ano,
                        $this->ref_cod_serie,
                        $this->ref_cod_aluno,
                        $this->ref_cod_escola
                    );
                }
            }

            $this->data_matricula = Portabilis_Date_Utils::brToPgSQL($this->data_matricula);

            $obj = new clsPmieducarMatricula(
                null,
                $this->ref_cod_reserva_vaga,
                $this->ref_cod_escola,
                $this->ref_cod_serie,
                null,
                $this->pessoa_logada,
                $this->ref_cod_aluno,
                3,
                null,
                null,
                1,
                $this->ano,
                1,
                null,
                null,
                null,
                null,
                $this->ref_cod_curso,
                null,
                $this->semestre,
                $this->data_matricula
            );

            $dataMatriculaObj = new \DateTime($this->data_matricula);
            $dataTransferencia = $obj->pegaDataDeTransferencia($this->ref_cod_aluno, $this->ano);
            $dataAnoLetivoInicio = $obj->pegaDataAnoLetivoInicio($this->ref_cod_turma);
            $dataAnoLetivoTermino = $obj->pegaDataAnoLetivoFim($this->ref_cod_turma);

            /** @var LegacyInstitution $instituicao */
            $instituicao = app(LegacyInstitution::class);

            if (empty($dataAnoLetivoTermino)) {
                $this->mensagem = 'Não está definida a data de término do ano letivo.';

                return false;
            }

            if ($dataTransferencia && $dataMatriculaObj <= $dataTransferencia) {
                $this->mensagem = sprintf(
                    'Não é possível matricular o aluno. O mesmo possui enturmação com data de saída anterior à data informada. Favor alterar a data de matrícula para ser superior a %s.',
                    $dataTransferencia->format('d/m/Y')
                );

                return false;
            } elseif ($dataMatriculaObj < $dataAnoLetivoInicio) {
                if (!$instituicao->allowRegistrationOutAcademicYear) {
                    $this->mensagem = sprintf(
                        'A data de matrícula precisa ser igual ou maior que a data de início do ano letivo da escola ou turma (%s).',
                        $dataAnoLetivoInicio->format('d/m/Y')
                    );

                    return false;
                }
            }

            if ($dataMatriculaObj > $dataAnoLetivoTermino) {
                $this->mensagem = sprintf(
                    'A data de matrícula precisa ser igual ou menor que a data fim do ano letivo da escola ou turma (%s).',
                    $dataAnoLetivoTermino->format('d/m/Y')
                );

                return false;
            }

            $validarCamposEducacenso = $this->validarCamposObrigatoriosCenso();

            if (!empty($this->ref_cod_turma) && $validarCamposEducacenso && !$this->availableTimeService()->isAvailable($this->ref_cod_aluno, $this->ref_cod_turma)) {
                $this->mensagem = 'O aluno já está matriculado em uma turma com esse horário.';
                return false;
            }

            $obj->dependencia = $dependencia;
            $cadastrou = $obj->cadastra();
            $this->cod_matricula = $cadastrou;

            if ($cadastrou) {
                if ($countEscolasIguais > 0) {
                    $obj_crv = new clsPmieducarCandidatoReservaVaga($this->ref_cod_candidato_reserva_vaga);
                    $obj_crv->vinculaMatricula($this->ref_cod_escola, $this->cod_matricula, $this->ref_cod_aluno);
                }

                if ($this->ref_cod_candidato_fila_unica) {
                    $obj_cfu = new clsPmieducarCandidatoFilaUnica($this->ref_cod_candidato_fila_unica);
                    $obj_cfu->vinculaMatricula($this->cod_matricula);
                }

                $this->enturmacaoMatricula($this->cod_matricula, $this->ref_cod_turma);

                if ($this->situacaoUltimaMatricula == $this->transferido &&
                    $this->serieUltimaMatricula == $this->ref_cod_serie &&
                    $this->anoUltimaMatricula == $this->ano
                )  {
                    /** @var LegacyRegistration $registration */

                    $registration = LegacyRegistration::find($this->cod_matricula);

                    try {
                        event(new RegistrationEvent($registration));
                    } catch (TransferException $exception) {
                        $this->mensagem = 'Não foi possível copiar os dados da matrícula antiga. ' . $exception->getMessage();

                        DB::commit();
                        $this->simpleRedirect('educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);
                    }

                    $promocao = new PromotionService($registration->enrollments()->first());
                    $promocao->fakeRequest();
                }

                $this->mensagem = 'Cadastro efetuado com sucesso.<br />';

                DB::commit();
                $this->simpleRedirect('educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno);
            }

            $this->mensagem = 'Cadastro n&atilde;o realizado.<br />';

            return false;
        } else {
            $this->mensagem = Portabilis_String_Utils::toLatin1('O ano (letivo) selecionado não está em andamento na escola selecionada.<br />');

            return false;
        }
    }

    public function permiteDependenciaAnoConcluinte()
    {
        $instituicao = new clsPmiEducarInstituicao($this->ref_cod_instituicao);
        $instituicao = $instituicao->detalhe();
        $serie = new clsPmieducarSerie($this->ref_cod_serie);
        $serie = $serie->detalhe();
        $reprovaDependenciaAnoConcluinte = $instituicao['reprova_dependencia_ano_concluinte'];
        $anoConcluinte = $serie['concluinte'] == 2;

        return !(dbBool($reprovaDependenciaAnoConcluinte) && $anoConcluinte);
    }

    public function verificaQtdeDependenciasPermitida()
    {
        $matriculasDependencia =
            Portabilis_Utils_Database::fetchPreparedQuery("SELECT *
                                                             FROM pmieducar.matricula
                                                            WHERE matricula.ano = {$this->ano}
                                                              AND matricula.ref_cod_aluno = {$this->ref_cod_aluno}
                                                              AND matricula.dependencia = TRUE
                                                              AND matricula.aprovado = 3
                                                              AND matricula.ativo = 1");

        $matriculasDependencia = count($matriculasDependencia);

        $db = new clsBanco();
        $matriculasDependenciaPermitida = $db->CampoUnico("SELECT regra_avaliacao.qtd_matriculas_dependencia
                                                             FROM pmieducar.serie
                                                       INNER JOIN modules.regra_avaliacao_serie_ano AS rasa ON (rasa.serie_id = serie.cod_serie AND rasa.ano_letivo = {$this->ano})
                                                       INNER JOIN modules.regra_avaliacao ON (regra_avaliacao.id = rasa.regra_avaliacao_id)
                                                            WHERE serie.cod_serie = {$this->ref_cod_serie}");

        if ($matriculasDependencia >= $matriculasDependenciaPermitida) {
            $this->mensagem = Portabilis_String_Utils::toLatin1("A regra desta série limita a quantidade de matrículas de dependência para {$matriculasDependenciaPermitida}.");

            return false;
        }

        return true;
    }

    public function verificaAlunoFalecido()
    {
        $aluno = new clsPmieducarAluno($this->ref_cod_aluno);
        $aluno = $aluno->detalhe();

        $pessoa = new clsPessoaFisica($aluno['ref_idpes']);
        $pessoa = $pessoa->detalhe();

        $falecido = dbBool($pessoa['falecido']);

        return $falecido;
    }

    public function bloqueiaMatriculaSerieNaoSeguinte()
    {
        $instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
        $instituicao = $instituicao->detalhe();
        $bloqueia = dbBool($instituicao['bloqueia_matricula_serie_nao_seguinte']);

        return $bloqueia;
    }

    public function permiteMatriculaSerieDestino()
    {
        $objMatricula = new clsPmieducarMatricula;
        $objSequenciaSerie = new clsPmieducarSequenciaSerie;

        $dadosUltimaMatricula = $objMatricula->getDadosUltimaMatricula($this->ref_cod_aluno);
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

        if (in_array($this->situacaoUltimaMatricula, $aprovado)) {
            $serieNovaMatricula = $objSequenciaSerie->lista($this->serieUltimaMatricula);
            $serieNovaMatricula = $serieNovaMatricula[0]['ref_serie_destino'];
        } elseif (in_array($this->situacaoUltimaMatricula, $reprovado)) {
            $serieNovaMatricula = $this->serieUltimaMatricula;
        }

        if ($this->ref_cod_serie == $serieNovaMatricula) {
            return true;
        }

        return false;
    }

    public function desativaEnturmacoesMatricula($matriculaId)
    {
        $result = true;

        $enturmacoes = new clsPmieducarMatriculaTurma();
        $enturmacoes = $enturmacoes->lista(
            $matriculaId,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        if ($enturmacoes) {
            foreach ($enturmacoes as $enturmacao) {
                $enturmacao = new clsPmieducarMatriculaTurma(
                    $matriculaId,
                    $enturmacao['ref_cod_turma'],
                    $this->pessoa_logada,
                    null,
                    null,
                    null,
                    0,
                    null,
                    $enturmacao['sequencial']
                );

                $enturmacao->removerSequencial = true;
                $detEnturmacao = $enturmacao->detalhe();
                $detEnturmacao = $detEnturmacao['data_enturmacao'];
                $enturmacao->data_enturmacao = $detEnturmacao;

                if ($result && !$enturmacao->edita()) {
                    $result = false;
                }
            }
        }

        if (!$result) {
            $this->mensagem = 'N&atilde;o foi poss&iacute;vel desativar as ' .
                'enturma&ccedil;&otilde;es da matr&iacute;cula.';
        }

        return $result;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_excluir(
            627,
            $this->pessoa_logada,
            7,
            'educar_aluno_det.php?cod_aluno=' . $this->ref_cod_aluno
        );

        if (!$this->desativaEnturmacoesMatricula($this->cod_matricula)) {
            return false;
        }

        $obj_matricula = new clsPmieducarMatricula($this->cod_matricula);
        $det_matricula = $obj_matricula->detalhe();
        $ref_cod_serie = $det_matricula['ref_ref_cod_serie'];

        $obj_sequencia = new clsPmieducarSequenciaSerie();

        $lst_sequencia = $obj_sequencia->lista(
            null,
            $ref_cod_serie,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        // Verifica se a série da matrícula cancelada é sequência de alguma outra série
        if (is_array($lst_sequencia)) {
            $det_sequencia = array_shift($lst_sequencia);
            $ref_serie_origem = $det_sequencia['ref_serie_origem'];

            $obj_matricula = new clsPmieducarMatricula();

            $lst_matricula = $obj_matricula->lista(
                null,
                null,
                null,
                $ref_serie_origem,
                null,
                null,
                $this->ref_cod_aluno,
                null,
                null,
                null,
                null,
                null,
                1,
                null,
                null,
                null,
                0
            );

            // Verifica se o aluno tem matrícula na série encontrada
            if (is_array($lst_matricula)) {
                $det_matricula = array_shift($lst_matricula);
                $ref_cod_matricula = $det_matricula['cod_matricula'];

                $obj = new clsPmieducarMatricula(
                    $ref_cod_matricula,
                    null,
                    null,
                    null,
                    $this->pessoa_logada,
                    null,
                    null,
                    null,
                    null,
                    null,
                    1,
                    null,
                    1
                );

                $editou1 = $obj->edita();

                if (!$editou1) {
                    $this->mensagem = 'N&atilde;o foi poss&iacute;vel editar a "&Uacute;ltima Matr&iacute;cula da Sequ&ecirc;ncia".<br />';

                    return false;
                }
            }
        }

        $obj = new clsPmieducarMatricula(
            $this->cod_matricula,
            null,
            null,
            null,
            $this->pessoa_logada,
            null,
            null,
            null,
            null,
            null,
            0
        );

        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem = 'Exclus&atilde;o efetuada com sucesso.<br />';

            throw new HttpResponseException(
                new RedirectResponse("educar_aluno_det.php?cod_aluno={$this->ref_cod_aluno}")
            );
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br />';

        return false;
    }

    protected function removerFlagUltimaMatricula($alunoId)
    {
        $matriculas = new clsPmieducarMatricula();

        $matriculas = $matriculas->lista(
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ref_cod_aluno,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            1
        );

        foreach ($matriculas as $matricula) {
            if (!$matricula['aprovado'] == 3) {
                $matricula = new clsPmieducarMatricula(
                    $matricula['cod_matricula'],
                    null,
                    null,
                    null,
                    $this->pessoa_logada,
                    null,
                    $alunoId,
                    null,
                    null,
                    null,
                    1,
                    null,
                    0
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
            $matriculaId,
            $turmaDestinoId,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        $enturmacaoExists = is_array($enturmacaoExists) && count($enturmacaoExists) > 0;

        if (!$enturmacaoExists) {
            $enturmacao = new clsPmieducarMatriculaTurma(
                $matriculaId,
                $turmaDestinoId,
                $this->pessoa_logada,
                $this->pessoa_logada,
                null,
                null,
                1
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
                $this->mensagem = Portabilis_String_Utils::toLatin1('Não existem vagas disponíveis para essa turma!') . '<br/>';

                return false;
            }

            // Caso a capacidade de alunos naquele turno seja menor ou igual ao ao número de alunos matrículados + alunos na reserva de vaga externa deve bloquear
            if ($this->_getMaxAlunoTurno() <= ($this->_getQtdAlunosFila() + $this->_getQtdMatriculaTurno())) {
                $this->mensagem = Portabilis_String_Utils::toLatin1('Não existem vagas disponíveis para essa série/turno!') . '<br/>';

                return false;
            }
        }

        return true;
    }

    public function _getQtdMatriculaTurma()
    {
        $obj_mt = new clsPmieducarMatriculaTurma();
        $lst_mt = $obj_mt->enturmacoesSemDependencia($this->ref_cod_turma);

        return $lst_mt[0];
    }

    public function _getMaxAlunoTurma()
    {
        $obj_t = new clsPmieducarTurma($this->ref_cod_turma);
        $det_t = $obj_t->detalhe();

        return $det_t['max_aluno'];
    }

    public function _getMaxAlunoTurno()
    {
        $obj_t = new clsPmieducarTurma();
        $det_t = $obj_t->detalhe();

        $lista_t = $obj_t->lista(
            $int_cod_turma = null,
            $int_ref_usuario_exc = null,
            $int_ref_usuario_cad = null,
            $int_ref_ref_cod_serie = $this->ref_cod_serie,
            $int_ref_ref_cod_escola = $this->ref_cod_escola,
            $int_ref_cod_infra_predio_comodo = null,
            $str_nm_turma = null,
            $str_sgl_turma = null,
            $int_max_aluno = null,
            $int_multiseriada = null,
            $date_data_cadastro_ini = null,
            $date_data_cadastro_fim = null,
            $date_data_exclusao_ini = null,
            $date_data_exclusao_fim = null,
            $int_ativo = null,
            $int_ref_cod_turma_tipo = null,
            $time_hora_inicial_ini = null,
            $time_hora_inicial_fim = null,
            $time_hora_final_ini = null,
            $time_hora_final_fim = null,
            $time_hora_inicio_intervalo_ini = null,
            $time_hora_inicio_intervalo_fim = null,
            $time_hora_fim_intervalo_ini = null,
            $time_hora_fim_intervalo_fim = null,
            $int_ref_cod_curso = null,
            $int_ref_cod_instituicao = null,
            $int_ref_cod_regente = null,
            $int_ref_cod_instituicao_regente = null,
            $int_ref_ref_cod_escola_mult = null,
            $int_ref_ref_cod_serie_mult = null,
            $int_qtd_min_alunos_matriculados = null,
            $bool_verifica_serie_multiseriada = false,
            $bool_tem_alunos_aguardando_nota = null,
            $visivel = null,
            $turma_turno_id = $det_t['turma_turno_id'],
            $tipo_boletim = null,
            $ano = $this->ano,
            $somenteAnoLetivoEmAndamento = false
        );

        $max_aluno_turmas = 0;

        foreach ($lista_t as $reg) {
            $max_aluno_turmas += $reg['max_aluno'];
        }

        return $max_aluno_turmas;
    }

    public function _getQtdAlunosFila()
    {
        $obj_t = new clsPmieducarTurma($this->ref_cod_turma);
        $det_t = $obj_t->detalhe();

        $sql = 'SELECT count(1) as qtd
                  FROM pmieducar.matricula
                 WHERE ano = $1
                   AND ref_ref_cod_escola = $2
                   AND ref_cod_curso = $3
                   AND ref_ref_cod_serie = $4
                   AND turno_pre_matricula = $5
                   AND aprovado = 11 ';

        return (int)Portabilis_Utils_Database::selectField($sql, [$this->ano, $this->ref_cod_escola, $this->ref_cod_curso, $this->ref_cod_serie, $det_t['turma_turno_id']]);
    }

    public function _getQtdMatriculaTurno()
    {
        $obj_t = new clsPmieducarTurma($this->ref_cod_turma);
        $det_t = $obj_t->detalhe();
        $obj_mt = new clsPmieducarMatriculaTurma();

        $lst_mt = $obj_mt->lista(
            $int_ref_cod_matricula = null,
            $int_ref_cod_turma = null,
            $int_ref_usuario_exc = null,
            $int_ref_usuario_cad = null,
            $date_data_cadastro_ini = null,
            $date_data_cadastro_fim = null,
            $date_data_exclusao_ini = null,
            $date_data_exclusao_fim = null,
            $int_ativo = 1,
            $int_ref_cod_serie = $this->ref_cod_serie,
            $int_ref_cod_curso = $this->ref_cod_curso,
            $int_ref_cod_escola = $this->ref_cod_escola,
            $int_ref_cod_instituicao = null,
            $int_ref_cod_aluno = null,
            $mes = null,
            $aprovado = null,
            $mes_menor_que = null,
            $int_sequencial = null,
            $int_ano_matricula = null,
            $tem_avaliacao = null,
            $bool_get_nome_aluno = false,
            $bool_aprovados_reprovados = null,
            $int_ultima_matricula = null,
            $bool_matricula_ativo = null,
            $bool_escola_andamento = false,
            $mes_matricula_inicial = false,
            $get_serie_mult = false,
            $int_ref_cod_serie_mult = null,
            $int_semestre = null,
            $pegar_ano_em_andamento = false,
            $parar = null,
            $diario = false,
            $int_turma_turno_id = $det_t['turma_turno_id'],
            $int_ano_turma = $det_t['ano'],
            $dependencia = 'f'
        );

        return count($lst_mt);
    }

    private function availableTimeService() {
        if (empty($this->availableTimeService)) {
            $this->availableTimeService = new AvailableTimeService();
        }

        return $this->availableTimeService;
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
