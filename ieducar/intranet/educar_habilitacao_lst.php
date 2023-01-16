<?php

use App\Models\LegacyQualification;

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;
    public $cod_habilitacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Habilitação - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $get_escola = false;
        include('include/pmieducar/educar_campo_lista.php');

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

        switch ($nivel_usuario) {
            case 1:
                $this->addCabecalhos([
                    'Instituição',
                    'Habilitacão'
                ]);
                break;

            default:
                $this->addCabecalhos([
                    'Habilitacão'
                ]);
                break;
        }

        // outros Filtros
        $this->campoTexto(nome: 'nm_tipo', campo: 'Habilitação', valor: $this->nm_tipo, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;

        $query = LegacyQualification::query()
            ->where(column: 'ativo', operator: 1)
            ->orderBy(column: 'nm_tipo', direction: 'ASC');

        if (is_string($this->nm_tipo)) {
            $query->where(column: 'nm_tipo', operator: 'ilike', value: '%' . $this->nm_tipo . '%');
        }

        if (is_numeric($this->ref_cod_instituicao)) {
            $query->where(column: 'ref_cod_instituicao', operator: $this->ref_cod_instituicao);
        }

        $result = $query->paginate($this->limite, pageName: 'pagina_'.$this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

                switch ($nivel_usuario) {
                    case 1:
                        $this->addLinhas([
                            "<a href=\"educar_habilitacao_det.php?cod_habilitacao={$registro['cod_habilitacao']}\">{$registro['nm_tipo']}</a>",
                            "<a href=\"educar_habilitacao_det.php?cod_habilitacao={$registro['cod_habilitacao']}\">{$registro['ref_cod_instituicao']}</a>"
                        ]);
                        break;

                    default:
                        $this->addLinhas([
                            "<a href=\"educar_habilitacao_det.php?cod_habilitacao={$registro['cod_habilitacao']}\">{$registro['nm_tipo']}</a>"
                        ]);
                        break;
                }
            }
        }
        $this->addPaginador2(strUrl: 'educar_habilitacao_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 573, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_habilitacao_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Lista de habilitações', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->titulo = 'i-Educar - Habilitação';
        $this->processoAp = '573';
    }
};
