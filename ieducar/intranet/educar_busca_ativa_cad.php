<?php

use App\Models\LegacyActiveLooking;
use App\Models\LegacyRegistration;
use App\Process;
use App\Services\Exemption\ActiveLookingService;
use iEducar\Modules\School\Model\ActiveLooking;
use iEducar\Support\View\SelectOptions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
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
            $this->simpleRedirect('/intranet/index.php');
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

        $this->ref_cod_matricula = $this->getQueryString('ref_cod_matricula');
        $this->id = $this->getQueryString('id');

        if (empty($this->ref_cod_matricula)) {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        $legacyRegistration = LegacyRegistration::find($this->ref_cod_matricula);
        $this->ref_cod_aluno = $legacyRegistration->ref_cod_aluno;

        $this->redirectIf(!$legacyRegistration, 'educar_matricula_lst.php');

        if (isset($this->id)) {
            $this->campoOculto('id', $this->id);
            $legacyActiveLookings = LegacyActiveLooking::find($this->id);

            if ($legacyActiveLookings) {
                foreach ($legacyActiveLookings->toArray() as $campo => $val) {
                    $this->$campo = $val;
                }
                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(Process::ACTIVE_LOOKING, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }

        $this->breadcrumb('Registro do busca ativa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);

        $dataInicio = $this->data_inicio ? (new DateTime($this->data_inicio))->format('d/m/Y') : null;

        $this->inputsHelper()->date('data_inicio', [
            'label' => 'Data de início',
            'placeholder' => 'dd/mm/yyyy',
            'required' => true,
            'value' => $dataInicio
        ]);

        $dataFim = $this->data_fim ? (new DateTime($this->data_fim))->format('d/m/Y') : null;

        $this->inputsHelper()->date('data_fim', [
            'label' => 'Data de retorno/abandono',
            'placeholder' => 'dd/mm/yyyy',
            'required' => false,
            'value' => $dataFim
        ]);

        $options = [
            'label' => 'Resultado da BA',
            'resources' => SelectOptions::activeSearchResultOptions(),
            'value' => $this->resultado_busca_ativa,
            'required' => true,
            'style'=> null
        ];

        $this->inputsHelper()->select('resultado_busca_ativa', $options);

        $textAreaSettings = [
            'label' => 'Observação',
            'value' => $this->observacoes,
            'max_length' => 900,
            'cols' => 60,
            'rows' => 5,
            'placeholder' => '',
            'required' => false
        ];
        $this->inputsHelper()->textArea('observacoes', $textAreaSettings);

        $this->url_cancelar = 'educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;
        $this->nome_url_cancelar = 'Cancelar';

        $this->loadAssets();

    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(Process::ACTIVE_LOOKING, $this->pessoa_logada, 7, "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        $legacyRegistration = LegacyRegistration::findOrFail($this->ref_cod_matricula);
        $this->redirectIf(!$legacyRegistration, 'educar_matricula_lst.php');
        $this->ref_cod_aluno = $legacyRegistration->ref_cod_aluno;
        $activeLookingService = new ActiveLookingService();
        $legacyActiveLooking = $this->buildObjectBeforeStore();

        try {
            DB::beginTransaction();
            $activeLookingService->store($legacyActiveLooking, $legacyRegistration);
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
            $this->simpleRedirect('educar_abandono_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula . '&ref_cod_aluno=' . $this->ref_cod_aluno);
            return true;
        }

        $this->simpleRedirect('educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        return true;
    }

    public function Editar()
    {
        return $this->Novo();
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(Process::ACTIVE_LOOKING, $this->pessoa_logada, 7, "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");

        $activeLookingService = new ActiveLookingService();
        $legacyActiveLooking = $this->buildObjectBeforeStore();

        try {
            DB::beginTransaction();
            $activeLookingService->delete($legacyActiveLooking);
            DB::commit();
        } catch (Exception) {
            DB::rollBack();
            $this->mensagem = 'Exclusão não realizada.';
            return false;
        }

        $this->mensagem = 'Exclusão efetuada com sucesso.';
        $this->simpleRedirect('educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula);

        return true;
    }

    protected function loadAssets()
    {
        $jsFiles = ['/modules/BuscaAtiva/BuscaAtiva.js', '/modules/BuscaAtiva/educar-busca-ativa-cad.js'];
        Portabilis_View_Helper_Application::loadJavascript($this, $jsFiles);
    }

    public function Formular()
    {
        $this->title = 'Busca Ativa';
        $this->processoAp = '578';

    }

    private function buildObjectBeforeStore()
    {
        if(empty($this->id)){
            $legacyActiveLooking = new LegacyActiveLooking();
        } else {
            $legacyActiveLooking = LegacyActiveLooking::find($this->id);
        }

        $this->data_inicio = Carbon::createFromFormat('d/m/Y', $this->data_inicio)->format('Y-m-d');
        if ($this->data_fim) {
            $this->data_fim = Carbon::createFromFormat('d/m/Y', $this->data_fim)->format('Y-m-d');
        } else {
            $this->data_fim = null;
            $this->resultado_busca_ativa = null;
        }
        $this->resultado_busca_ativa = empty($this->resultado_busca_ativa) ? ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT : $this->resultado_busca_ativa;

        return $legacyActiveLooking->fill(
            [
                'ref_cod_matricula' => $this->ref_cod_matricula,
                'data_inicio' => $this->data_inicio,
                'data_fim' => $this->data_fim,
                'observacoes' => $this->observacoes,
                'resultado_busca_ativa' => $this->resultado_busca_ativa
            ]
        );
    }
};
