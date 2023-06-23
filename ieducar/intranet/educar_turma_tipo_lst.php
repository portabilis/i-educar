<?php

use App\Models\LegacySchoolClassType;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $cod_turma_tipo;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_tipo;

    public $sgl_tipo;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public $ref_cod_escola;

    public function Gerar()
    {
        $this->titulo = 'Turma Tipo - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Turma Tipo',
        ];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos(coluna: $lista_busca);

        include 'include/pmieducar/educar_campo_lista.php';

        // outros Filtros
        $this->campoTexto(nome: 'nm_tipo', campo: 'Turma Tipo', valor: $this->nm_tipo, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;

        $query = LegacySchoolClassType::where(column: 'ativo', operator: 1)
            ->orderBy('nm_tipo', 'ASC');

        if (is_string(value: $this->nm_tipo)) {
            $query->where(column: 'nm_tipo', operator: 'ilike', value: '%' . $this->nm_tipo . '%');
        }
        if (is_numeric(value: $this->ref_cod_instituicao)) {
            $query->where(column: 'ref_cod_instituicao', operator: $this->ref_cod_instituicao);
        }

        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_'.$this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $obj_cod_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                $lista_busca = [
                    "<a href=\"educar_turma_tipo_det.php?cod_turma_tipo={$registro['cod_turma_tipo']}\">{$registro['nm_tipo']}</a>",
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_turma_tipo_det.php?cod_turma_tipo={$registro['cod_turma_tipo']}\">{$registro['ref_cod_instituicao']}</a>";
                }

                $this->addLinhas(linha: $lista_busca);
            }
        }
        $this->addPaginador2(strUrl: 'educar_turma_tipo_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 570, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_turma_tipo_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de tipos de turma', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Turma Tipo';
        $this->processoAp = '570';
    }
};
