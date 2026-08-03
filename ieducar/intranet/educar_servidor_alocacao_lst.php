<?php

use App\Models\EmployeeAllocation;
use App\Models\LegacyPerson;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $ref_cod_servidor;

    public $ref_cod_funcao;

    public $carga_horaria;

    public $data_cadastro;

    public $data_exclusao;

    public $ref_cod_escola;

    public $ref_cod_instituicao;

    public $ano_letivo;

    public function Gerar()
    {
        $this->titulo = 'Alocação servidor - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $tmp_obj = new clsPmieducarServidor($this->ref_cod_servidor, null, null, null, null, null, null, $this->ref_cod_instituicao);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_servidor_lst.php');
        }

        $this->addCabecalhos([
            'Escola',
            'Função',
            'Ano',
            'Período',
            'Carga horária',
            'Data admissão',
            'Data saída',
            'Vínculo',
        ]);

        $nome = LegacyPerson::whereKey($this->ref_cod_servidor)->value('nome');

        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
        $this->campoRotulo('nm_servidor', 'Servidor', $nome);

        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'show-select' => true, 'value' => $this->ref_cod_instituicao]);
        $this->inputsHelper()->dynamic('escola', ['required' => false, 'show-select' => true, 'value' => $this->ref_cod_escola]);
        $this->inputsHelper()->dynamic('anoLetivo', ['required' => false, 'show-select' => true, 'value' => $this->ano_letivo]);

        $parametros = new clsParametrosPesquisas;
        $parametros->setSubmit(0);

        // Paginador
        $this->limite = 20;

        $usuarioBiblioteca = App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada);

        $paginador = EmployeeAllocation::query()
            ->active()
            ->when(is_numeric($this->ref_cod_instituicao), fn ($q) => $q->whereInstitution($this->ref_cod_instituicao))
            ->when(is_numeric($this->ref_cod_escola), fn ($q) => $q->whereSchool($this->ref_cod_escola))
            ->when(!is_numeric($this->ref_cod_escola) && $usuarioBiblioteca, fn ($q) => $q->whereUser($this->pessoa_logada))
            ->when(is_numeric($this->ref_cod_servidor), fn ($q) => $q->whereEmployee($this->ref_cod_servidor))
            ->when(is_numeric($this->ano_letivo), fn ($q) => $q->whereYearEq($this->ano_letivo))
            ->with([
                'school:cod_escola,ref_idpes',
                'school.organization:idpes,fantasia',
                'bond:cod_funcionario_vinculo,nm_vinculo',
                'employeeRole:cod_servidor_funcao,ref_cod_funcao',
                'employeeRole.role:cod_funcao,nm_funcao',
            ])
            ->orderBy('ano')
            ->orderBy('data_saida')
            ->orderBy('data_admissao')
            ->paginate(perPage: $this->limite, pageName: 'pagina_' . $this->nome);

        $lista = $paginador->getCollection();
        $total = $paginador->total();

        // UrlHelper
        $url = CoreExt_View_Helper_UrlHelper::getInstance();

        // Monta a lista
        if ($lista->isNotEmpty()) {
            foreach ($lista as $registro) {
                $path = 'educar_servidor_alocacao_det.php';
                $options = [
                    'query' => [
                        'cod_servidor_alocacao' => $registro['cod_servidor_alocacao'],
                    ]];

                // Escola
                $nomeEscola = $registro->school->organization?->fantasia;

                // Periodo
                $periodo = [
                    1 => 'Matutino',
                    2 => 'Vespertino',
                    3 => 'Noturno',
                ];

                // Função
                $nomeFuncao = $registro->employeeRole?->role?->nm_funcao;

                // Vinculo
                $funcionarioVinculo = $registro->bond?->nm_vinculo;

                $this->addLinhas([
                    $url->l($nomeEscola, $path, $options),
                    $url->l($nomeFuncao, $path, $options),
                    $url->l($registro['ano'], $path, $options),
                    $url->l($periodo[$registro['periodo']], $path, $options),
                    $url->l($horas = substr($registro['carga_horaria'], 0, -3), $path, $options),
                    $url->l(Portabilis_Date_Utils::pgSQLToBr($registro['data_admissao']), $path, $options),
                    $url->l(Portabilis_Date_Utils::pgSQLToBr($registro['data_saida']), $path, $options),
                    $url->l($funcionarioVinculo, $path, $options),
                ]);
            }
        }

        $this->addPaginador2('educar_servidor_alocacao_lst.php', $total, $_GET, $this->nome, $this->limite);

        $obj_permissoes = new clsPermissoes;

        $this->array_botao = [];
        $this->array_botao_url = [];
        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->array_botao_url[] = "educar_servidor_alocacao_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green',
            ];
        }

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";

        $this->largura = '100%';

        $this->breadcrumb('Registro de alocações do servidor', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor';
        $this->processoAp = 635;
    }
};
