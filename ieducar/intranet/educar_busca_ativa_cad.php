<?php

use App\Models\LegacyActiveLooking;
use App\Models\LegacyRegistration;
use App\Process;
use App\Services\Exemption\ActiveLookingService;
use App\Services\FileService;
use App\Services\UrlPresigner;
use iEducar\Modules\School\Model\ActiveLooking;
use iEducar\Support\View\SelectOptions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $id;

    public $ref_cod_matricula;

    public $ref_cod_aluno;

    public $data_inicio;

    public $data_fim;

    public $ativo;

    public $observacoes;

    public $resultado_busca_ativa;

    public function __construct()
    {
        parent::__construct();

        $user = Auth::user();

        if ($user->isLibrary()) {
            $this->simpleRedirect(url: '/intranet/index.php');

            return false;
        }

        return true;
    }

    /**
     * @var array
     */
    public $etapa;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_matricula = $this->getQueryString(name: 'ref_cod_matricula');
        $this->id = $this->getQueryString(name: 'id');

        if (empty($this->ref_cod_matricula)) {
            $this->simpleRedirect(url: 'educar_matricula_lst.php');
        }

        $legacyRegistration = LegacyRegistration::find($this->ref_cod_matricula);
        $this->ref_cod_aluno = $legacyRegistration->ref_cod_aluno;

        $this->redirectIf(condition: !$legacyRegistration, url: 'educar_matricula_lst.php');

        if (isset($this->id)) {
            $this->campoOculto(nome: 'id', valor: $this->id);
            $legacyActiveLookings = LegacyActiveLooking::find($this->id);

            if ($legacyActiveLookings) {
                foreach ($legacyActiveLookings->getAttributes() as $campo => $val) {
                    $this->$campo = $val;
                }
                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(int_processo_ap: Process::ACTIVE_LOOKING, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }

        $this->breadcrumb(currentPage: 'Registro do busca ativa', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->form_enctype = ' enctype=\'multipart/form-data\'';

        // primary keys
        $this->campoOculto(nome: 'ref_cod_matricula', valor: $this->ref_cod_matricula);

        $dataInicio = $this->data_inicio ? (new DateTime(datetime: $this->data_inicio))->format(format: 'd/m/Y') : null;

        $this->inputsHelper()->date(attrName: 'data_inicio', inputOptions: [
            'label' => 'Data de início',
            'placeholder' => 'dd/mm/yyyy',
            'required' => true,
            'value' => $dataInicio,
        ]);

        $dataFim = $this->data_fim ? (new DateTime(datetime: $this->data_fim))->format(format: 'd/m/Y') : null;

        $this->inputsHelper()->date(attrName: 'data_fim', inputOptions: [
            'label' => 'Data de encerramento',
            'placeholder' => 'dd/mm/yyyy',
            'required' => false,
            'value' => $dataFim,
        ]);

        $options = [
            'label' => 'Resultado da BA',
            'resources' => SelectOptions::activeSearchResultOptions(),
            'value' => $this->resultado_busca_ativa,
            'required' => true,
            'style' => null,
        ];

        $this->inputsHelper()->select(attrName: 'resultado_busca_ativa', inputOptions: $options);

        $textAreaSettings = [
            'label' => 'Observação',
            'value' => $this->observacoes,
            'max_length' => 900,
            'cols' => 60,
            'rows' => 5,
            'placeholder' => '',
            'required' => false,
        ];
        $this->inputsHelper()->textArea(attrName: 'observacoes', inputOptions: $textAreaSettings);

        if (empty($this->id)) {
            $this->id = null;
        }

        $fileService = new FileService(new UrlPresigner());
        $files = $fileService->getFiles(LegacyActiveLooking::find($this->id));

        $this->addHtml(view('uploads.upload', ['files' => $files])->render());

        $this->url_cancelar = 'educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;
        $this->nome_url_cancelar = 'Cancelar';

        $this->loadAssets();

    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(int_processo_ap: Process::ACTIVE_LOOKING, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        $legacyRegistration = LegacyRegistration::findOrFail($this->ref_cod_matricula);
        $this->redirectIf(condition: !$legacyRegistration, url: 'educar_matricula_lst.php');
        $this->ref_cod_aluno = $legacyRegistration->ref_cod_aluno;
        $activeLookingService = new ActiveLookingService();
        $legacyActiveLooking = $this->buildObjectBeforeStore();

        try {
            DB::beginTransaction();
            $activeLooking = $activeLookingService->store(activeLooking: $legacyActiveLooking, registration: $legacyRegistration);

            $fileService = new FileService(new UrlPresigner());
            if ($this->file_url) {
                $newFiles = json_decode($this->file_url);
                foreach ($newFiles as $file) {
                    $fileService->saveFile(
                        $file->url,
                        $file->size,
                        $file->originalName,
                        $file->extension,
                        LegacyActiveLooking::class,
                        $activeLooking->getKey()
                    );
                }
            }

            if ($this->file_url_deleted) {
                $deletedFiles = explode(',', $this->file_url_deleted);
                $fileService->deleteFiles($deletedFiles);
            }

            DB::commit();
        } catch (ValidationException $e) {
            $this->mensagem = $e->validator->errors()->first();
            DB::rollBack();

            return false;
        } catch (Exception $e) {
            $this->mensagem = $e->getMessage();
            DB::rollBack();

            return false;
        }
        $this->mensagem = 'Cadastro efetuado com sucesso.<br />';

        if ($this->resultado_busca_ativa == ActiveLooking::ACTIVE_LOOKING_ABANDONMENT_RESULT) {
            $this->simpleRedirect(url: 'educar_abandono_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula . '&ref_cod_aluno=' . $this->ref_cod_aluno);

            return true;
        }

        if ($this->resultado_busca_ativa == ActiveLooking::ACTIVE_LOOKING_TRANSFER_RESULT) {
            $this->simpleRedirect(url: 'educar_transferencia_solicitacao_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula . '&ref_cod_aluno=' . $this->ref_cod_aluno);

            return true;
        }

        $this->simpleRedirect(url: 'educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        return true;
    }

    public function Editar()
    {
        return $this->Novo();
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(int_processo_ap: Process::ACTIVE_LOOKING, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7, str_pagina_redirecionar: "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        $activeLookingService = new ActiveLookingService();
        $legacyActiveLooking = $this->buildObjectBeforeStore();

        try {
            DB::beginTransaction();
            $files = $legacyActiveLooking->files->pluck('id');

            $activeLookingService->delete(activeLooking: $legacyActiveLooking);
            if ($files->isNotEmpty()) {
                $fileService = new FileService(new UrlPresigner());
                $fileService->deleteFiles($files);
            }

            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            $this->mensagem = 'Exclusão não realizada.';

            return false;
        }

        $this->mensagem = 'Exclusão efetuada com sucesso.';
        $this->simpleRedirect(url: 'educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        return true;
    }

    protected function loadAssets()
    {
        $jsFiles = ['/vendor/legacy/BuscaAtiva/BuscaAtiva.js', '/vendor/legacy/BuscaAtiva/educar-busca-ativa-cad.js'];
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $jsFiles);
    }

    public function Formular()
    {
        $this->title = 'Busca Ativa';
        $this->processoAp = '578';

    }

    private function buildObjectBeforeStore()
    {
        if (empty($this->id)) {
            $legacyActiveLooking = new LegacyActiveLooking();
        } else {
            $legacyActiveLooking = LegacyActiveLooking::find($this->id);
        }

        $this->data_inicio = Carbon::createFromFormat('d/m/Y', $this->data_inicio)->format(format: 'Y-m-d');
        if ($this->data_fim) {
            $this->data_fim = Carbon::createFromFormat('d/m/Y', $this->data_fim)->format(format: 'Y-m-d');
        } else {
            $this->data_fim = null;
            $this->resultado_busca_ativa = null;
        }
        $this->resultado_busca_ativa = empty($this->resultado_busca_ativa) ? ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT : $this->resultado_busca_ativa;

        return $legacyActiveLooking->fill(
            attributes: [
                'ref_cod_matricula' => $this->ref_cod_matricula,
                'data_inicio' => $this->data_inicio,
                'data_fim' => $this->data_fim,
                'observacoes' => $this->observacoes,
                'resultado_busca_ativa' => $this->resultado_busca_ativa,
            ]
        );
    }
};
