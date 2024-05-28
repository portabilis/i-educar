<?php

use App\Models\Employee;

return new class extends clsListagem
{
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

        $this->addCabecalhos(coluna: [
            'Nome do Servidor',
            'Matrícula',
            'CPF',
            'Instituição',
        ]);

        $this->inputsHelper()->dynamic(helperNames: ['instituicao', 'escola', 'anoLetivo'], helperOptions: ['options' => ['required' => false]]);

        $parametros = new clsParametrosPesquisas();
        $parametros->setSubmit(submit: 0);
        $this->campoTexto(nome: 'nome', campo: 'Nome do servidor', valor: $this->nome, tamanhovisivel: 50, tamanhomaximo: 255);
        $this->campoTexto(nome: 'matricula_servidor', campo: 'Matrícula', valor: $this->matricula_servidor, tamanhovisivel: 50, tamanhomaximo: 255);
        $this->inputsHelper()->dynamic(helperNames: 'escolaridade', inputOptions: ['required' => false]);
        $this->campoCheck(nome: 'servidor_sem_alocacao', campo: 'Incluir servidores sem alocação', valor: isset($_GET['servidor_sem_alocacao']));

        // Paginador
        $this->limite = 20;

        if (!$this->ref_idesco && $_GET['idesco']) {
            $this->ref_idesco = $_GET['idesco'];
        }

        $lista = Employee::join(table: 'cadastro.pessoa', first: 'cod_servidor', operator: 'idpes')->filter([
            'institution' => $this->ref_cod_instituicao,
            'name' => $this->nome,
            'role' => $this->matricula_servidor,
            'schooling_degree' => $this->ref_idesco,
            'allocation' => [request()->has('servidor_sem_alocacao'), $this->ref_cod_escola, $this->ano_letivo],
            'employee' => $this->cod_servidor,
        ])->with([
            'institution:cod_instituicao,nm_instituicao',
            'individual:idpes,cpf',
            'employeeRoles:ref_cod_servidor,matricula',
        ])->active()->orderBy('pessoa.nome')->paginate($this->limite, [
            'pessoa.nome as name',
            'ref_cod_instituicao',
            'cod_servidor',
        ], 'pagina_formulario');

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
                    ],
                ];

                $this->addLinhas(linha: [
                    $url->l(text: $registro->name, path: $path, options: $options),
                    $url->l(text: $registro->employeeRoles->unique('matricula')->implode('matricula', ', '), path: $path, options: $options),
                    $url->l(text: $registro->individual->cpf, path: $path, options: $options),
                    $url->l(text: $registro->institution->name, path: $path, options: $options),
                ]);
            }
        }

        $this->addPaginador2(
            strUrl: 'educar_servidor_lst.php',
            intTotalRegistros: $lista->total(),
            mixVariaveisMantidas: $_GET,
            intResultadosPorPagina: $this->limite
        );
        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 635, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_servidor_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Funções do servidor', breadcrumbs: [
            url(path: 'intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidor';
        $this->processoAp = 635;
    }
};
