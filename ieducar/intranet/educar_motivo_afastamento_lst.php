<?php

use App\Models\WithdrawalReason;

return new class extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public $cod_motivo_afastamento;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_motivo;

    public $descricao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Motivo Afastamento - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null : $val;
        }

        $lista_busca = [
            'Motivo de Afastamento',
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Instituição';
        }

        $this->addCabecalhos(coluna: $lista_busca);

        $get_escola = false;
        include 'include/pmieducar/educar_campo_lista.php';
        $this->campoTexto(nome: 'nm_motivo', campo: 'Motivo de Afastamento', valor: $this->nm_motivo, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;

        $query = WithdrawalReason::query()
            ->orderBy('nm_motivo');

        if ($this->ref_cod_instituicao) {
            $query->where('ref_cod_instituicao', $this->ref_cod_instituicao);
        }
        if ($this->nm_motivo) {
            $query->where('nm_motivo', 'ilike', '%' . $this->nm_motivo . '%');
        }
        $result = $query->paginate(perPage: $this->limite, pageName: 'pagina_'.$this->nome);

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $obj_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
                $det_instituicao = $obj_instituicao->detalhe();

                $lista_busca = [
                    "<a href=\"educar_motivo_afastamento_det.php?cod_motivo_afastamento={$registro['cod_motivo_afastamento']}\">{$registro['nm_motivo']}</a>",
                ];

                if ($nivel_usuario == 1) {
                    //$lista_busca[] = "<a href=\"educar_motivo_afastamento_det.php?cod_motivo_afastamento={$registro["cod_motivo_afastamento"]}\">{$det_ins["nome"]}</a>";
                    $lista_busca[] = "<a href=\"educar_motivo_afastamento_det.php?cod_motivo_afastamento={$registro['cod_motivo_afastamento']}\">{$det_instituicao['nm_instituicao']}</a>";
                }
                $this->addLinhas(linha: $lista_busca);
            }
        }
        $this->addPaginador2(strUrl: 'educar_motivo_afastamento_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 633, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_motivo_afastamento_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Motivos de afastamento do servidor', breadcrumbs: [
            url(path: 'intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Motivos de afastamento do servidor';
        $this->processoAp = '633';
    }
};
