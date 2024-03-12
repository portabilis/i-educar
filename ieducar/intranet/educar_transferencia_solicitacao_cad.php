<?php

use App\Events\TransferEvent;
use App\Models\LegacyActiveLooking;
use App\Models\LegacyRegistration;
use App\Models\LegacyTransferRequest;
use App\Models\LegacyTransferType;
use App\Models\LegacyUser;
use App\Services\PromotionService;
use iEducar\Modules\School\Model\ActiveLooking;
use Illuminate\Support\Facades\DB;

return new class extends clsCadastro
{
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
        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: [
            '/vendor/legacy/Portabilis/Assets/Stylesheets/Frontend/Resource.css',
        ]);
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: [
            '/vendor/legacy/Cadastro/Assets/Javascripts/TransferenciaSolicitacao.js',
        ]);

        $user = Auth::user();
        $allow = Gate::allows('view', 691);

        if ($user->isLibrary() || !$allow) {
            $this->simpleRedirect(url: '/intranet/index.php');

            return false;
        }
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_aluno = $_GET['ref_cod_aluno'];
        $cancela = $_GET['cancela'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        if (
            $cancela == true
            && is_numeric(value: $this->ref_cod_matricula)
            && is_numeric(value: $this->ref_cod_aluno)
            && $obj_permissoes->permissao_excluir(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)
        ) {
            if ($_GET['reabrir_matricula']) {
                $this->reabrirMatricula(matriculaId: $this->ref_cod_matricula);
            }

            /** @var LegacyRegistration $registration */
            $registration = LegacyRegistration::find(id: $this->ref_cod_matricula);
            if ($lastEnrollment = $registration->lastEnrollment()->first()) {
                $promocao = new PromotionService(enrollment: $lastEnrollment);
                $promocao->fakeRequest();
            }

            $this->Excluir();
        }

        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb(currentPage: 'Registro da solicitação de transferência da matrícula', breadcrumbs: [
            'educar_index.php' => 'Escola',
        ]);

        return $retorno;
    }

    public function reabrirMatricula($matriculaId)
    {
        $matricula = new clsPmieducarMatricula(cod_matricula: $matriculaId);
        $matricula->ref_usuario_exc = null;
        $matricula->aprovado = 3;
        $matricula->data_exclusao = null;
        $matricula->data_cancel = null;
        $matricula->edita();

        $sql = "select ref_cod_turma, sequencial from pmieducar.matricula_turma where ref_cod_matricula = $matriculaId and sequencial = (select max(sequencial) from pmieducar.matricula_turma where ref_cod_matricula = $matriculaId) and not exists(select 1 from pmieducar.matricula_turma where ref_cod_matricula = $matriculaId and ativo = 1 limit 1) limit 1";

        $db = new clsBanco();
        $db->Consulta(consulta: $sql);
        $db->ProximoRegistro();
        $ultimaEnturmacao = $db->Tupla();

        if (empty($ultimaEnturmacao)) {
            return;
        }

        $enturmacao = new clsPmieducarMatriculaTurma(ref_cod_matricula: $matriculaId, ref_cod_turma: $ultimaEnturmacao['ref_cod_turma'], ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, data_cadastro: null, data_exclusao: null, ativo: 1, ref_cod_turma_transf: null, sequencial: $ultimaEnturmacao['sequencial']);
        $detEnturmacao = $enturmacao->detalhe();
        $enturmacao->data_enturmacao = $detEnturmacao['data_enturmacao'];
        $enturmacao->edita();
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);
        $this->campoOculto(nome: 'ref_cod_matricula', valor: $this->ref_cod_matricula);

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista(int_cod_aluno: $this->ref_cod_aluno, int_ativo: 1);

        if (is_array(value: $lst_aluno)) {
            $det_aluno = array_shift(array: $lst_aluno);
            $this->nm_aluno = $det_aluno['nome_aluno'];
            $this->campoTexto(nome: 'nm_aluno', campo: 'Aluno', valor: $this->nm_aluno, tamanhovisivel: 30, tamanhomaximo: 255, evento: '', disabled: true);
        }

        $obj_matricula = new clsPmieducarMatricula(cod_matricula: $this->ref_cod_matricula);
        $det_matricula = $obj_matricula->detalhe();
        $ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];

        $this->inputsHelper()->dynamic(helperNames: ['instituicao'], inputOptions: ['required' => false]);
        $this->inputsHelper()->dynamic(helperNames: ['escolaSemFiltroPorUsuario'], inputOptions: ['label_hint' => 'Destino do aluno', 'required' => false]);
        $labelHintEscolaForaDoMunicipio = 'Transferência para uma escola externa (outro município, particular, etc)';
        $this->inputsHelper()->checkbox(attrName: 'escola_em_outro_municipio', inputOptions: ['label' => 'Escola em outro município ou fora da rede?', '<br>label_hint' => $labelHintEscolaForaDoMunicipio]);
        $this->campoTexto(nome: 'escola_destino_externa', campo: 'Nome da escola ', valor: '', tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoTexto(nome: 'estado_escola_destino_externa', campo: 'Estado da escola ', valor: '', tamanhovisivel: 20, tamanhomaximo: 50);
        $this->campoTexto(nome: 'municipio_escola_destino_externa', campo: 'Município da escola ', valor: '', tamanhovisivel: 20, tamanhomaximo: 50);

        $opcoesMotivo = LegacyTransferType::query()
            ->where(column: 'ativo', operator: 1)
            ->where(column: 'ref_cod_instituicao', operator: $ref_cod_instituicao)
            ->orderBy(column: 'nm_tipo', direction: 'ASC')
            ->pluck(column: 'nm_tipo', key: 'cod_transferencia_tipo')
            ->prepend(value: 'Selecione', key: '');

        $this->campoLista(nome: 'ref_cod_transferencia_tipo', campo: 'Motivo', valor: $opcoesMotivo, default: $this->ref_cod_transferencia_tipo);
        $this->inputsHelper()->date(attrName: 'data_cancel', inputOptions: ['label' => 'Data', 'placeholder' => 'dd/mm/yyyy', 'value' => date(format: 'd/m/Y')]);
        $this->campoMemo(nome: 'observacao', campo: 'Observação', valor: $this->observacao, colunas: 60, linhas: 5);
    }

    public function Novo()
    {
        DB::beginTransaction();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        $this->data_cancel = Portabilis_Date_Utils::brToPgSQL(date: $this->data_cancel);
        $obj = new clsPmieducarMatricula(cod_matricula: $this->ref_cod_matricula, ref_cod_reserva_vaga: null, ref_ref_cod_escola: null, ref_ref_cod_serie: null, ref_usuario_exc: $this->pessoa_logada);
        $det_matricula = $obj->detalhe();

        if (is_null(value: $det_matricula['data_matricula'])) {
            if (substr(string: $det_matricula['data_cadastro'], offset: 0, length: 10) > $this->data_cancel) {
                $this->mensagem = 'Data de transferência não pode ser inferior a data da matrícula.<br>';

                return false;
            }
        } elseif (substr(string: $det_matricula['data_matricula'], offset: 0, length: 10) > $this->data_cancel) {
            $this->mensagem = 'Data de transferência não pode ser inferior a data da matrícula.<br>';

            return false;
        }

        $obj->data_cancel = $this->data_cancel;

        $this->data_transferencia = date(format: 'Y-m-d');
        $this->ativo = 1;

        $obj_matricula = new clsPmieducarMatricula(cod_matricula: $this->ref_cod_matricula);
        $det_matricula = $obj_matricula->detalhe();
        $aprovado = $det_matricula['aprovado'];

        if ($aprovado == 3) {
            $obj = new clsPmieducarMatricula(cod_matricula: $this->ref_cod_matricula, ref_cod_reserva_vaga: null, ref_ref_cod_escola: null, ref_ref_cod_serie: null, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, ref_cod_aluno: null, aprovado: 4, data_cadastro: null, data_exclusao: null, ativo: 1);
            $obj->data_cancel = $this->data_cancel;
            $editou = $obj->edita();
            if (!$editou) {
                $this->mensagem = 'Não foi possível editar a Matrícula do Aluno.<br>';

                return false;
            }

            $enturmacoes = new clsPmieducarMatriculaTurma();
            $enturmacoes = $enturmacoes->lista(int_ref_cod_matricula: $this->ref_cod_matricula, int_ativo: 1);

            if ($enturmacoes) {
                // foreach necessário pois metodo edita e exclui da classe clsPmieducarMatriculaTurma, necessitam do
                // código da turma e do sequencial
                foreach ($enturmacoes as $enturmacao) {
                    $enturmacao = new clsPmieducarMatriculaTurma(ref_cod_matricula: $this->ref_cod_matricula, ref_cod_turma: $enturmacao['ref_cod_turma'], ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, data_cadastro: null, data_exclusao: null, ativo: 0, ref_cod_turma_transf: null, sequencial: $enturmacao['sequencial'], data_enturmacao: $this->data_enturmacao);
                    $detEnturmacao = $enturmacao->detalhe();
                    $enturmacao->data_enturmacao = $detEnturmacao['data_enturmacao'];
                    if (!$enturmacao->edita()) {
                        $this->mensagem = 'Não foi possível desativar as enturmações da matrícula.';

                        return false;
                    } else {
                        $enturmacao->marcaAlunoTransferido(data: $this->data_cancel);
                    }
                }
            }
        }
        clsPmieducarHistoricoEscolar::gerarHistoricoTransferencia(ref_cod_matricula: $this->ref_cod_matricula, pessoa_logada: $this->pessoa_logada);

        if ($this->escola_em_outro_municipio === 'on') {
            $this->ref_cod_escola = null;
        } else {
            $this->escola_destino_externa = null;
            $this->estado_escola_destino_externa = null;
            $this->municipio_escola_destino_externa = null;
        }

        $obj = new clsPmieducarTransferenciaSolicitacao(cod_transferencia_solicitacao: null, ref_cod_transferencia_tipo: $this->ref_cod_transferencia_tipo, ref_usuario_exc: null, ref_usuario_cad: $this->pessoa_logada, ref_cod_matricula_entrada: null, ref_cod_matricula_saida: $this->ref_cod_matricula, observacao: $this->observacao, data_cadastro: null, data_exclusao: null, ativo: $this->ativo, data_transferencia: $this->data_transferencia, escola_destino_externa: $this->escola_destino_externa, ref_cod_escola_destino: $this->ref_cod_escola, estado_escola_destino_externa: $this->estado_escola_destino_externa, municipio_escola_destino_externa: $this->municipio_escola_destino_externa);
        if ($obj->existSolicitacaoTransferenciaAtiva()) {
            $this->mensagem = 'Já existe uma solitação de transferência ativa.<br>';

            return false;
        }
        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $registration = LegacyRegistration::find($this->ref_cod_matricula);
            $exists = LegacyUser::query()
                ->whereKey($registration->ref_usuario_cad)
                ->exists();

            $registration->update([
                'ref_usuario_exc' => $this->pessoa_logada,
                'ref_usuario_cad' => $exists ? $registration->ref_usuario_cad : $this->pessoa_logada,
                'data_cancel' => $this->data_cancel,
            ]);

            $notasAluno = (new Avaliacao_Model_NotaAlunoDataMapper())->findAll(columns: ['id'], where: ['matricula_id' => $this->ref_cod_matricula]);

            if ($notasAluno && count(value: $notasAluno)) {
                $notaAlunoId = $notasAluno[0]->get('id');

                try {
                    (new Avaliacao_Model_NotaComponenteMediaDataMapper())
                        ->updateSituation(notaAlunoId: $notaAlunoId, situacao: App_Model_MatriculaSituacao::TRANSFERIDO);
                } catch (\Throwable) {
                    DB::rollback();
                }
            }
            //Marca a busca ativa como transferência
            LegacyActiveLooking::query()
                ->where('ref_cod_matricula', $this->ref_cod_matricula)
                ->where('resultado_busca_ativa', ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT)
                ->update([
                    'resultado_busca_ativa' => ActiveLooking::ACTIVE_LOOKING_TRANSFER_RESULT,
                    'data_fim' => $this->data_cancel,
                ]);

            DB::commit();

            event(new TransferEvent(transfer: LegacyTransferRequest::findOrFail(id: $cadastrou)));

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect(url: "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
        }

        DB::rollback();

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();
        $lst_transferencia = $obj_transferencia->lista(int_ref_cod_matricula_saida: $this->ref_cod_matricula, int_ativo: 1, int_ref_cod_aluno: $this->ref_cod_aluno);
        if (is_array(value: $lst_transferencia)) {
            try {
                DB::beginTransaction();
                $det_transferencia = array_shift(array: $lst_transferencia);
                $this->cod_transferencia_solicitacao = $det_transferencia['cod_transferencia_solicitacao'];
                $obj = new clsPmieducarTransferenciaSolicitacao(cod_transferencia_solicitacao: $this->cod_transferencia_solicitacao, ref_cod_transferencia_tipo: null, ref_usuario_exc: $this->pessoa_logada, ref_usuario_cad: null, ref_cod_matricula_entrada: null, ref_cod_matricula_saida: null, observacao: null, data_cadastro: null, data_exclusao: null, ativo: 0);
                $excluiu = $obj->excluir();
                //Desfaz a busca ativa com transferencia
                LegacyActiveLooking::query()
                    ->where('ref_cod_matricula', $this->ref_cod_matricula)
                    ->where('resultado_busca_ativa', ActiveLooking::ACTIVE_LOOKING_TRANSFER_RESULT)
                    ->update([
                        'resultado_busca_ativa' => ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT,
                        'data_fim' => null,
                    ]);
                DB::commit();
            } catch (Exception) {
                DB::rollBack();
            }

            if ($excluiu) {
                $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
                $this->simpleRedirect(url: "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
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
