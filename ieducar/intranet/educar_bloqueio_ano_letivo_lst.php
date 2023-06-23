<?php

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $ref_instituicao;

    public $ref_ano;

    public $data_inicio;

    public $data_fim;

    public $ano;

    public function Gerar()
    {
        $this->titulo = 'Bloqueio do ano letivo - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->ref_ano = $this->ano;

        $this->addCabecalhos(coluna: [
            'Instituição',
            'Ano',
            'Data inicial permitida',
            'Data final permitida',
        ]);

        $this->inputsHelper()->dynamic(helperNames: 'instituicao', helperOptions: ['options' => ['required' => false]]);
        $this->inputsHelper()->dynamic(helperNames: 'ano', inputOptions: ['value' => $this->ref_ano, 'required' => false]);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj = new clsPmieducarBloqueioAnoLetivo();
        $obj->setOrderby(strNomeCampo: 'instituicao ASC, ref_ano DESC');
        $obj->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj->lista(
            ref_cod_instituicao: $this->ref_cod_instituicao,
            ref_ano: $this->ref_ano
        );

        $total = $obj->_total;

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $data_inicio = dataToBrasil(data_original: $registro['data_inicio']);
                $data_fim = dataToBrasil(data_original: $registro['data_fim']);

                $this->addLinhas(linha: [
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro['ref_cod_instituicao']}&ref_ano={$registro['ref_ano']} \">{$registro['instituicao']}</a>",
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro['ref_cod_instituicao']}&ref_ano={$registro['ref_ano']} \">{$registro['ref_ano']}</a>",
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro['ref_cod_instituicao']}&ref_ano={$registro['ref_ano']} \">{$data_inicio}</a>",
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro['ref_cod_instituicao']}&ref_ano={$registro['ref_ano']} \">{$data_fim}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_bloqueio_ano_letivo_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 21251, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->acao = 'go("educar_bloqueio_ano_letivo_cad.php")';
            $this->nome_acao = 'Novo';
        }
        //**

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de bloqueios do ano letivo', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Bloqueio do ano letivo';
        $this->processoAp = '21251';
    }
};
