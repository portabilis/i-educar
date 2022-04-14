<?php

use App\Models\LegacyActiveLooking;
use App\Models\LegacyRegistration;
use App\Process;
use iEducar\Modules\School\Model\ActiveLooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;
    public $ref_cod_matricula;
    public $data_inicio;
    public $ref_cod_instituicao;
    public $ref_cod_turma;

    public function __construct()
    {
        parent::__construct();
        $user = Auth::user();
        $allow = Gate::allows('view', Process::ACTIVE_LOOKING);

        if ($user->isLibrary() || !$allow) {
            $this->simpleRedirect('/intranet/index.php');
            return false;
        }

        return true;
    }

    public function Gerar()
    {
        // Helper para url
        $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();
        $ref_cod_matricula = $this->getQueryString('ref_cod_matricula');
        $this->titulo = 'Busca ativa - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        if (!$ref_cod_matricula) {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        $legacyRegistration = LegacyRegistration::find($ref_cod_matricula);
        $legacyEnrollment = $legacyRegistration->lastEnrollment()->first();
        $this->ref_cod_turma = $legacyEnrollment->ref_cod_turma;

        $this->addCabecalhos([
            'Data de início',
            'Data de finalização',
            'Resultado da busca ativa'
        ]);

        $this->limite = 20;
        $this->offset = $_GET['pagina_' . $this->nome] ?
            $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $legacyActiveLookings = LegacyActiveLooking::query()
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->orderBy('id', 'DESC')
            ->limit($this->limite)
            ->offset($this->offset)
            ->get()
            ->toArray();

        $total = count($legacyActiveLookings);

        if ($total > 0) {
            foreach ($legacyActiveLookings as $legacyActiveLooking) {

                $row = [];

                $startDate = new DateTime($legacyActiveLooking['data_inicio']);
                $endDate = empty($legacyActiveLooking['data_fim']) ? null : (new DateTime($legacyActiveLooking['data_fim']));

                $row['data_inicio_br'] = $startDate->format('d/m/Y');
                $row['data_fim_br'] = $endDate ? $endDate->format('d/m/Y') : '-';

                $url = 'educar_busca_ativa_cad.php';
                $options = ['query' =>
                    [
                        'id' => $legacyActiveLooking['id'],
                        'ref_cod_matricula' => $this->ref_cod_matricula
                    ]
                ];

                $row['active_looking_result'] = ActiveLooking::getDescription($legacyActiveLooking['resultado_busca_ativa']);

                $this->addLinhas([
                    $urlHelper->l($row['data_inicio_br'], $url, $options),
                    $urlHelper->l($row['data_fim_br'], $url, $options),
                    $urlHelper->l($row['active_looking_result'], $url, $options)
                ]);
            }
        }

        $this->addPaginador2(
            'educar_busca_ativa_lst.php',
            $total,
            $_GET,
            $this->nome,
            $this->limite
        );

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(Process::ACTIVE_LOOKING, $this->pessoa_logada, 7) && $legacyRegistration->aprovado == App_Model_MatriculaSituacao::EM_ANDAMENTO) {
            $this->array_botao_url[] = 'educar_busca_ativa_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula;
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green'
            ];
        }

        $this->array_botao_url[] = 'educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula;
        $this->array_botao[] = 'Voltar';

        $this->largura = '100%';

        $this->breadcrumb('Busca ativa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Busca ativa';
        $this->processoAp = Process::ACTIVE_LOOKING;
    }
};
