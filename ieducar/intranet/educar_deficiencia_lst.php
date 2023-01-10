<?php

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;
    public $cod_deficiencia;
    public $nm_deficiencia;

    public function Gerar()
    {
        $this->titulo = 'Deficiência - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Deficiência'
        ]);

        // Filtros de Foreign Keys

        // outros Filtros
        $this->campoTexto(nome: 'nm_deficiencia', campo: 'Deficiência', valor: $this->nm_deficiencia, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_deficiencia = new clsCadastroDeficiencia();
        $obj_deficiencia->setOrderby('nm_deficiencia ASC');
        $obj_deficiencia->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_deficiencia->lista(
            int_cod_deficiencia: $this->cod_deficiencia,
            str_nm_deficiencia: $this->nm_deficiencia
        );

        $total = $obj_deficiencia->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // muda os campos data

                // pega detalhes de foreign_keys

                $this->addLinhas([
                    "<a href=\"educar_deficiencia_det.php?cod_deficiencia={$registro['cod_deficiencia']}\">{$registro['nm_deficiencia']}</a>"
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_deficiencia_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 631, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_deficiencia_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de deficiência', breadcrumbs: [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Deficiência';
        $this->processoAp = '631';
    }
};
