<?php

use App\Models\LegacyRole;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $cod_funcao;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_funcao;

    public $abreviatura;

    public $professor;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Função - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Nome Funcão',
            'Abreviatura',
            'Professor',
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        include 'include/pmieducar/educar_campo_lista.php';

        // outros Filtros
        $this->campoTexto(nome: 'nm_funcao', campo: 'Nome Função', valor: $this->nm_funcao, tamanhovisivel: 30, tamanhomaximo: 255);
        $this->campoTexto(nome: 'abreviatura', campo: 'Abreviatura', valor: $this->abreviatura, tamanhovisivel: 30, tamanhomaximo: 255);
        $opcoes = [
            '' => 'Selecione',
            'N' => 'Não',
            'S' => 'Sim',
        ];

        $this->campoLista(nome: 'professor', campo: 'Professor', valor: $opcoes, default: $this->professor, obrigatorio: false);

        if ($this->professor == 'N') {
            $this->professor = '0';
        } elseif ($this->professor == 'S') {
            $this->professor = '1';
        }

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $query = LegacyRole::query()
            ->where('ativo', 1)
            ->orderBy('nm_funcao', 'ASC');

        if (is_string(value: $this->nm_funcao)) {
            $query->where('nm_funcao', 'ilike', '%' . $this->nm_funcao . '%');
        }
        if (is_string(value: $this->abreviatura)) {
            $query->where('nm_funcao', 'ilike', '%' . $this->abreviatura . '%');
        }
        if (is_numeric(value: $this->ref_cod_instituicao)) {
            $query->where('ref_cod_instituicao', $this->ref_cod_instituicao);
        }
        if (is_numeric(value: $this->professor)) {
            $query->where('professor', $this->professor);
        }

        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_'.$this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $registro['professor'] = $registro['professor'] == 1 ? 'Sim' : 'Não';

                $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $nm_instituicao = $det_ref_cod_instituicao['nm_instituicao'];

                $lista_busca = [
                    "<a href=\"educar_funcao_det.php?cod_funcao={$registro['cod_funcao']}&ref_cod_instituicao={$registro['ref_cod_instituicao']}\">{$registro['nm_funcao']}</a>",
                    "<a href=\"educar_funcao_det.php?cod_funcao={$registro['cod_funcao']}&ref_cod_instituicao={$registro['ref_cod_instituicao']}\">{$registro['abreviatura']}</a>",
                    "<a href=\"educar_funcao_det.php?cod_funcao={$registro['cod_funcao']}&ref_cod_instituicao={$registro['ref_cod_instituicao']}\">{$registro['professor']}</a>",
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_funcao_det.php?cod_funcao={$registro['cod_funcao']}&ref_cod_instituicao={$registro['ref_cod_instituicao']}\">{$nm_instituicao}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2(strUrl: 'educar_funcao_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 634, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_funcao_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Funções do servidor', breadcrumbs: [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Funções do servidor';
        $this->processoAp = '634';
    }
};
