<?php

use App\Models\Employee;

return new class extends clsListagem {
    public $limite;
    public $offset;
    public $cod_servidor;
    public $ref_idesco;
    public $ref_cod_funcao;
    public $carga_horaria;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nome;
    public $matricula_servidor;
    public $ref_cod_escola;
    public $ref_cod_instituicao;
    public $servidor_sem_alocacao;
    public $ano_letivo;

    public function Gerar()
    {
        $this->titulo = 'Servidor - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Nome do Servidor',
            'Matrícula',
            'CPF',
            'Instituição'
        ]);

        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'anoLetivo'], [],['options' => ['required' => false]]);

        $parametros = new clsParametrosPesquisas();
        $parametros->setSubmit(0);
        $this->campoTexto('nome', 'Nome do servidor', $this->nome, 50, 255, false);
        $this->campoTexto('matricula_servidor', 'Matrícula', $this->matricula_servidor, 50, 255, false);
        $this->inputsHelper()->dynamic('escolaridade', ['required' => false]);
        $this->campoCheck('servidor_sem_alocacao', 'Incluir servidores sem alocação', isset($_GET['servidor_sem_alocacao']));

        // Paginador
        $this->limite = 20;

        if (!$this->ref_idesco && $_GET['idesco']) {
            $this->ref_idesco = $_GET['idesco'];
        }

        $lista = Employee::join('pessoa', 'cod_servidor', 'idpes')->filter([
            'institution' => $this->ref_cod_instituicao,
            'name' => $this->nome,
            'role' => $this->matricula_servidor,
            'schooling_degree' => $this->ref_idesco,
            'allocation' => [request()->has('servidor_sem_alocacao'),$this->ref_cod_escola,$this->ano_letivo],
            'employee' => $this->cod_servidor,
        ])->with([
            'institution:cod_instituicao,nm_instituicao',
            'individual:idpes,cpf',
            'employeeRoles:ref_cod_servidor,matricula'
        ])->active()->orderBy('pessoa.nome')->paginate($this->limite, [
            'pessoa.nome as name',
            'ref_cod_instituicao',
            'cod_servidor',
        ], 'pagina_');


        // UrlHelper
        $url = CoreExt_View_Helper_UrlHelper::getInstance();

        // Monta a lista
        if ($lista->isNotEmpty()) {
            foreach ($lista as $registro) {
                $path = 'educar_servidor_det.php';
                $options = [
                    'query' => [
                        'cod_servidor' => $registro->id,
                        'ref_cod_instituicao' => $registro->institution->id,
                    ]
                ];

                $this->addLinhas([
                    $url->l($registro->name, $path, $options),
                    $url->l($registro->employeeRoles->unique('matricula')->implode('matricula',', '), $path, $options),
                    $url->l($registro->individual->cpf, $path, $options),
                    $url->l($registro->institution->name, $path, $options),
                ]);
            }
        }

        $this->addPaginador2('educar_servidor_lst.php', $lista->total(), $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_servidor_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Funções do servidor', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidor';
        $this->processoAp = 635;
    }
};
