<?php

use App\Events\TransferEvent;
use App\Models\LegacyRegistration;
use App\Models\LegacyTransferRequest;
use App\Services\PromotionService;
use Illuminate\Support\Facades\DB;

return new class() extends clsCadastro {
    public $cod_transferencia_solicitacao;
    public $ref_cod_transferencia_tipo;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_matricula_entrada;
    public $ref_cod_matricula_saida;
    public $observacao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $data_transferencia;
    public $data_cancel;
    public $ref_cod_matricula;
    public $transferencia_tipo;
    public $ref_cod_aluno;
    public $nm_aluno;
    public $escola_destino_externa;
    public $estado_escola_destino_externa;
    public $municipio_escola_destino_externa;
    public $ref_cod_escola;
    public $ref_cod_escola_destino;
    public $escola_em_outro_municipio;

    public function __construct()
    {
        parent::__construct();
        Portabilis_View_Helper_Application::loadStylesheet($this, [
            '/modules/Portabilis/Assets/Stylesheets/Frontend/Resource.css',
        ]);
        Portabilis_View_Helper_Application::loadJavascript($this, [
            '/modules/Cadastro/Assets/Javascripts/TransferenciaSolicitacao.js',
        ]);
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_aluno = $_GET['ref_cod_aluno'];
        $cancela = $_GET['cancela'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        if (
            $cancela == true
            && is_numeric($this->ref_cod_matricula)
            && is_numeric($this->ref_cod_aluno)
            && $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7)
        ) {
            if ($_GET['reabrir_matricula']) {
                $this->reabrirMatricula($this->ref_cod_matricula);
            }

            /** @var LegacyRegistration $registration */
            $registration = LegacyRegistration::find($this->ref_cod_matricula);
            if ($lastEnrollment = $registration->lastEnrollment()->first()) {
                $promocao = new PromotionService($lastEnrollment);
                $promocao->fakeRequest();
            }

            $this->Excluir();
        }

        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Registro da solicitação de transferência da matrícula', [
            'educar_index.php' => 'Escola',
        ]);

        return $retorno;
    }

    public function reabrirMatricula($matriculaId)
    {
        $matricula = new clsPmieducarMatricula($matriculaId);
        $matricula->ref_usuario_exc = null;
        $matricula->aprovado = 3;
        $matricula->data_exclusao = null;
        $matricula->data_cancel = null;
        $matricula->edita();

        $sql = "select ref_cod_turma, sequencial from pmieducar.matricula_turma where ref_cod_matricula = $matriculaId and sequencial = (select max(sequencial) from pmieducar.matricula_turma where ref_cod_matricula = $matriculaId) and not exists(select 1 from pmieducar.matricula_turma where ref_cod_matricula = $matriculaId and ativo = 1 limit 1) limit 1";

        $db = new clsBanco();
        $db->Consulta($sql);
        $db->ProximoRegistro();
        $ultimaEnturmacao = $db->Tupla();

        if (empty($ultimaEnturmacao)) {
            return;
        }

        $enturmacao = new clsPmieducarMatriculaTurma($matriculaId, $ultimaEnturmacao['ref_cod_turma'], $this->pessoa_logada, null, null, null, 1, null, $ultimaEnturmacao['sequencial']);
        $detEnturmacao = $enturmacao->detalhe();
        $detEnturmacao = $detEnturmacao['data_enturmacao'];
        $enturmacao->data_enturmacao = $detEnturmacao;
        $enturmacao->edita();
    }

    public function Gerar()
    {
        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);
        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($this->ref_cod_aluno, null, null, null, null, null, null, null, null, null, 1);

        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno['nome_aluno'];
            $this->campoTexto('nm_aluno', 'Aluno', $this->nm_aluno, 30, 255, false, false, false, '', '', '', '', true);
        }

        $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
        $det_matricula = $obj_matricula->detalhe();
        $ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];

        $this->inputsHelper()->dynamic(['instituicao'], ['required' => false]);
        $this->inputsHelper()->dynamic(['escolaSemFiltroPorUsuario'], ['label_hint' => 'Destino do aluno', 'required' => false]);
        $labelHintEscolaForaDoMunicipio = 'Transferência para uma escola externa (outro município, particular, etc)';
        $this->inputsHelper()->checkbox('escola_em_outro_municipio', ['label' => 'Escola em outro município ou fora da rede?', '<br>label_hint' => $labelHintEscolaForaDoMunicipio]);
        $this->campoTexto('escola_destino_externa', 'Nome da escola ', '', 30, 255, false, false, false, '');
        $this->campoTexto('estado_escola_destino_externa', 'Estado da escola ', '', 20, 50, false, false, false, '');
        $this->campoTexto('municipio_escola_destino_externa', 'Município da escola ', '', 20, 50, false, false, false, '');

        $objTemp = new clsPmieducarTransferenciaTipo();
        $objTemp->setOrderby(' nm_tipo ASC ');
        $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, null, $ref_cod_instituicao);

        $opcoesMotivo = ['' => 'Selecione'];

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoesMotivo[$registro['cod_transferencia_tipo']] = $registro['nm_tipo'];
            }
        }

        $this->campoLista('ref_cod_transferencia_tipo', 'Motivo', $opcoesMotivo, $this->ref_cod_transferencia_tipo);
        $this->inputsHelper()->date('data_cancel', ['label' => 'Data', 'placeholder' => 'dd/mm/yyyy', 'value' => date('d/m/Y')]);
        $this->campoMemo('observacao', 'Observação', $this->observacao, 60, 5, false);
    }

    public function Novo()
    {
        $frequencia = new clsModulesFrequencia();
        $dataFrequencia = $frequencia->selectDataFrequenciaByTurma($_GET['turma']);

        $atendimento = new clsModulesComponenteMinistradoAee();
        $dataAtendimento = $atendimento->selectDataAtendimentoByMatricula($_GET['ref_cod_matricula']);

        $data_cancel = Portabilis_Date_Utils::brToPgSQL($this->data_cancel);

        if (($data_cancel <= $dataFrequencia['data']) || ($data_cancel <= $dataAtendimento['data'])) {
            $this->mensagem = 'Não é possível realizar a operação, existem frequências registradas no período <br>';

            return false;
        } else {
            DB::beginTransaction();

            $obj_permissoes = new clsPermissoes();
            $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

            $this->data_cancel = Portabilis_Date_Utils::brToPgSQL($this->data_cancel);
            $obj = new clsPmieducarMatricula($this->ref_cod_matricula, null, null, null, $this->pessoa_logada);
            $det_matricula = $obj->detalhe();

            if (is_null($det_matricula['data_matricula'])) {
                if (substr($det_matricula['data_cadastro'], 0, 10) > $this->data_cancel) {
                    $this->mensagem = 'Data de transferência não pode ser inferior a data da matrícula.<br>';

                    return false;
                }
            } elseif (substr($det_matricula['data_matricula'], 0, 10) > $this->data_cancel) {
                $this->mensagem = 'Data de transferência não pode ser inferior a data da matrícula.<br>';

                return false;
            }

            $obj->data_cancel = $this->data_cancel;

            $this->data_transferencia = date('Y-m-d');
            $this->ativo = 1;

            $obj_matricula = new clsPmieducarMatricula($this->ref_cod_matricula);
            $det_matricula = $obj_matricula->detalhe();
            $aprovado = $det_matricula['aprovado'];

            if ($aprovado == 3) {
                $obj = new clsPmieducarMatricula($this->ref_cod_matricula, null, null, null, $this->pessoa_logada, null, null, 4, null, null, 1);
                $obj->data_cancel = $this->data_cancel;
                $editou = $obj->edita();
                if (!$editou) {
                    $this->mensagem = 'Não foi possível editar a Matrícula do Aluno.<br>';

                    return false;
                }

                $enturmacoes = new clsPmieducarMatriculaTurma();
                $enturmacoes = $enturmacoes->lista($this->ref_cod_matricula, null, null, null, null, null, null, null, 1);

                if ($enturmacoes) {
                    // foreach necessário pois metodo edita e exclui da classe clsPmieducarMatriculaTurma, necessitam do
                    // código da turma e do sequencial
                    foreach ($enturmacoes as $enturmacao) {
                        $enturmacao = new clsPmieducarMatriculaTurma($this->ref_cod_matricula, $enturmacao['ref_cod_turma'], $this->pessoa_logada, null, null, null, 0, null, $enturmacao['sequencial'], $this->data_enturmacao);
                        $detEnturmacao = $enturmacao->detalhe();
                        $detEnturmacao = $detEnturmacao['data_enturmacao'];
                        $enturmacao->data_enturmacao = $detEnturmacao;
                        if (!$enturmacao->edita()) {
                            $this->mensagem = 'Não foi possível desativar as enturmações da matrícula.';

                            return false;
                        } else {
                            $enturmacao->marcaAlunoTransferido($this->data_cancel);
                        }
                    }
                }
            }
            clsPmieducarHistoricoEscolar::gerarHistoricoTransferencia($this->ref_cod_matricula, $this->pessoa_logada);

            if ($this->escola_em_outro_municipio === 'on') {
                $this->ref_cod_escola = null;
            } else {
                $this->escola_destino_externa = null;
                $this->estado_escola_destino_externa = null;
                $this->municipio_escola_destino_externa = null;
            }

            $obj = new clsPmieducarTransferenciaSolicitacao(null, $this->ref_cod_transferencia_tipo, null, $this->pessoa_logada, null, $this->ref_cod_matricula, $this->observacao, null, null, $this->ativo, $this->data_transferencia, $this->escola_destino_externa, $this->ref_cod_escola, $this->estado_escola_destino_externa, $this->municipio_escola_destino_externa);
            if ($obj->existSolicitacaoTransferenciaAtiva()) {
                $this->mensagem = 'Já existe uma solitação de transferência ativa.<br>';

                return false;
            }

            $cadastrou = $obj->cadastra();

            if ($cadastrou) {
                $obj = new clsPmieducarMatricula($this->ref_cod_matricula, null, null, null, $this->pessoa_logada);
                $obj->data_cancel = $this->data_cancel;
                $obj->edita();

                $notasAluno = (new Avaliacao_Model_NotaAlunoDataMapper())->findAll(['id'], ['matricula_id' => $obj->cod_matricula]);

                if ($notasAluno && count($notasAluno)) {
                    $notaAlunoId = $notasAluno[0]->get('id');

                    try {
                        (new Avaliacao_Model_NotaComponenteMediaDataMapper())
                        ->updateSituation($notaAlunoId, App_Model_MatriculaSituacao::TRANSFERIDO);
                    } catch (\Throwable $exception) {
                        DB::rollback();
                    }
                }

                DB::commit();

                event(new TransferEvent(LegacyTransferRequest::findOrFail($cadastrou)));

                $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                $this->simpleRedirect("educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
            }

            DB::rollback();

            $this->mensagem = 'Cadastro não realizado.<br>';

            return false;
        }
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(578, $this->pessoa_logada, 7, "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();
        $lst_transferencia = $obj_transferencia->lista(null, null, null, null, null, $this->ref_cod_matricula, null, null, null, null, null, 1, null, null, $this->ref_cod_aluno, false);
        if (is_array($lst_transferencia)) {
            $det_transferencia = array_shift($lst_transferencia);
            $this->cod_transferencia_solicitacao = $det_transferencia['cod_transferencia_solicitacao'];
            $obj = new clsPmieducarTransferenciaSolicitacao($this->cod_transferencia_solicitacao, null, $this->pessoa_logada, null, null, null, null, null, null, 0);
            $excluiu = $obj->excluir();
            if ($excluiu) {
                $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
                $this->simpleRedirect("educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
            }
        } else {
            $this->mensagem = 'Não foi possível encontrar a Solicitação de Transferência do Aluno.<br>';

            return false;
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Transferência Solicitação';
        $this->processoAp = '578';
    }
};
