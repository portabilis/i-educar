<?php

use App\Models\LegacyActiveLooking;
use App\Models\LegacyRegistration;
use App\Process;
use iEducar\Modules\School\Model\ActiveLooking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

return new class extends clsListagem
{
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
            $this->simpleRedirect(url: '/intranet/index.php');

            return false;
        }
    }

    public function Gerar()
    {
        // Helper para url
        $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();
        $ref_cod_matricula = $this->getQueryString(name: 'ref_cod_matricula');
        $this->titulo = 'Busca ativa - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        if (!$ref_cod_matricula) {
            $this->simpleRedirect(url: 'educar_matricula_lst.php');
        }

        $legacyRegistration = LegacyRegistration::find($ref_cod_matricula);
        $legacyEnrollment = $legacyRegistration->lastEnrollment()->first();
        $this->ref_cod_turma = $legacyEnrollment->ref_cod_turma;

        $this->addCabecalhos(coluna: [
            'Data de início',
            'Data de finalização',
            'Resultado da busca ativa',
        ]);

        $this->limite = 20;
        $this->offset = $_GET['pagina_' . $this->nome] ?
            $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $legacyActiveLookings = LegacyActiveLooking::query()
            ->where(column: 'ref_cod_matricula', operator: $this->ref_cod_matricula)
            ->orderBy(column: 'id', direction: 'DESC')
            ->limit(value: $this->limite)
            ->offset(value: $this->offset)
            ->get();

        $total = count(value: $legacyActiveLookings);

        if ($total > 0) {
            foreach ($legacyActiveLookings as $legacyActiveLooking) {
                $row = [];

                $row['data_inicio_br'] = $legacyActiveLooking->start->format('d/m/Y');
                $row['data_fim_br'] = $legacyActiveLooking->end?->format('d/m/Y');

                $url = 'educar_busca_ativa_cad.php';
                $options = ['query' => [
                    'id' => $legacyActiveLooking->id,
                    'ref_cod_matricula' => $this->ref_cod_matricula,
                ],
                ];

                $row['active_looking_result'] = ActiveLooking::getDescription(value: $legacyActiveLooking->result);

                $this->addLinhas(linha: [
                    $urlHelper->l(text: $row['data_inicio_br'], path: $url, options: $options),
                    $urlHelper->l(text: $row['data_fim_br'], path: $url, options: $options),
                    $urlHelper->l(text: $row['active_looking_result'], path: $url, options: $options),
                ]);
            }
        }

        $this->addPaginador2(
            strUrl: 'educar_busca_ativa_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: Process::ACTIVE_LOOKING, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7) && $legacyRegistration->aprovado == App_Model_MatriculaSituacao::EM_ANDAMENTO) {
            $this->array_botao_url[] = 'educar_busca_ativa_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula;
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green',
            ];
        }

        $this->array_botao_url[] = 'educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula;
        $this->array_botao[] = 'Voltar';

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Busca ativa', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Busca ativa';
        $this->processoAp = Process::ACTIVE_LOOKING;
    }
};
