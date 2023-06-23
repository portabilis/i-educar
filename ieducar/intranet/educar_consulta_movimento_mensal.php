<?php

use App\Models\LegacySchoolClass;
use App\Services\SchoolClass\SchoolClassService;

return new class extends clsCadastro
{
    const PROCESSO_AP = 9998910;

    public $ano;

    public $ref_cod_instituicao;

    public $ref_cod_escola;

    public $ref_cod_curso;

    public $ref_cod_serie;

    public $ref_cod_turma;

    public $data_inicial;

    public $data_final;

    public function Inicializar()
    {
        $this->ano = $this->getQueryString('ano');
        $this->ref_cod_instituicao = $this->getQueryString('ref_cod_instituicao');
        $this->ref_cod_escola = $this->getQueryString('ref_cod_escola');
        $this->ref_cod_curso = $this->getQueryString('ref_cod_curso');
        $this->ref_cod_serie = $this->getQueryString('ref_cod_serie');
        $this->ref_cod_turma = $this->getQueryString('ref_cod_turma');
        $this->data_inicial = $this->getQueryString('data_inicial');
        $this->data_final = $this->getQueryString('data_final');

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: self::PROCESSO_AP,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_index.php'
        );

        $this->nome_url_sucesso = 'Continuar';
        $this->url_cancelar = 'educar_index.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb(currentPage: 'Consulta de movimento mensal', breadcrumbs: ['educar_index.php' => 'Escola']);

        return 'Novo';
    }

    public function Gerar()
    {
        $this->inputsHelper()->dynamic(['ano', 'instituicao', 'escola']);
        $this->inputsHelper()->dynamic(helperNames: ['curso', 'serie', 'turma'], inputOptions: ['required' => false]);

        $options = [
            'label' => 'Modalidade',
            'resources' => [
                1 => 'Todas',
                2 => 'Regular',
                3 => 'Atendimento Educacional Especializado - AEE',
                4 => 'Atividade complementar',
                5 => 'Educação de Jovens e Adultos - EJA',
            ],
            'required' => true,
        ];
        $this->inputsHelper()->select(attrName: 'modalidade', inputOptions: $options);

        $calendars = $this->getCalendars();
        $this->addHtml(
            view('form.calendar')
                ->with(key: 'calendars', value: $calendars)
        );

        $this->inputsHelper()->dynamic(['dataInicial', 'dataFinal']);

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: [
            '/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js',
            '/intranet/scripts/movimento_mensal.js',
        ]);

        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: [
            '/vendor/legacy/Portabilis/Assets/Plugins/Chosen/chosen.css',
        ]);
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: self::PROCESSO_AP,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'index.php'
        );

        $campos = [
            'ano',
            'ref_cod_instituicao',
            'ref_cod_escola',
            'ref_cod_curso',
            'ref_cod_serie',
            'ref_cod_turma',
            'data_inicial',
            'data_final',
            'modalidade',
            'calendars',
        ];

        $queryString = [];

        foreach ($campos as $campo) {
            $queryString[$campo] = $this->{$campo};
        }

        $queryString = http_build_query($queryString);
        $url = 'educar_consulta_movimento_mensal_lst.php?' . $queryString;

        $this->simpleRedirect($url);
    }

    private function getCalendars()
    {
        $schoolClass = $this->getSchoolClass();

        $schoolClassService = new SchoolClassService();

        return $schoolClassService->getCalendars($schoolClass);
    }

    private function getSchoolClass()
    {
        if ($this->getQueryString('ref_cod_turma')) {
            return [$this->getQueryString('ref_cod_turma')];
        }

        return LegacySchoolClass::query()
            ->where(column: 'ano', operator: ($this->getQueryString('ano') ?: date('Y')))
            ->whereHas(relation: 'course', callback: function ($courseQuery) {
                $courseQuery->isEja();
            })
            ->when(value: $this->getQueryString('ref_cod_escola'), callback: function ($query) {
                $query->where('ref_ref_cod_escola', $this->getQueryString('ref_cod_escola'));
            })
            ->when(value: $this->getQueryString('ref_cod_serie'), callback: function ($query) {
                $query->where('ref_ref_cod_serie', $this->getQueryString('ref_cod_serie'));
            })
            ->when(value: $this->getQueryString('ref_cod_curso'), callback: function ($query) {
                $query->where('ref_cod_curso', $this->getQueryString('ref_cod_curso'));
            })
            ->get(['cod_turma'])->pluck('cod_turma')->all();
    }

    public function Formular()
    {
        $this->title = 'Consulta de movimento mensal';
        $this->processoAp = 9998910;
    }
};
