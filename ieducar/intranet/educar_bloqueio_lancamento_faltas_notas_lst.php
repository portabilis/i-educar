<?php

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

    public $offset;

    public $ano;

    public $ref_cod_escola;

    public $etapa;

    public $data_inicio;

    public $data_fim;

    public function Gerar()
    {

        // Helper para url
        $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();

        $this->titulo = 'Bloqueio de lançamento de notas e faltas por etapa - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos(coluna: [
            'Escola', 'Ano', 'Etapa', 'Data início', 'Data fim',
        ]);

        $this->inputsHelper()->dynamic(helperNames: ['ano'], inputOptions: ['required' => false]);

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);

        $get_escola = true;
        $obrigatorio = false;
        $exibe_nm_escola = true;

        include 'include/pmieducar/educar_campo_lista.php';

        // Paginador
        $this->limite = 20;
        $this->offset = $_GET['pagina_' . $this->nome] ?
            $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $obj_bloqueio_lancamento_fn = new clsPmieducarBloqueioLancamentoFaltasNotas();
        $obj_bloqueio_lancamento_fn->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_bloqueio_lancamento_fn->lista(
            ano: $this->ano,
            ref_cod_escola: $this->ref_cod_escola
        );

        $total = $obj_bloqueio_lancamento_fn->_total;

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $registro) {
                $obj_ref_cod_escola = new clsPmieducarEscola(cod_escola: $registro['ref_cod_escola']);
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $nm_escola = $det_ref_cod_escola['nome'];

                $etapas = [
                    1 => '1ª Etapa',
                    2 => '2ª Etapa',
                    3 => '3ª Etapa',
                    4 => '4ª Etapa',
                ];
                $nm_etapa = $etapas[$registro['etapa']];

                // Dados para a url
                $url = 'educar_bloqueio_lancamento_faltas_notas_det.php';
                $options = ['query' => [
                    'cod_bloqueio' => $registro['cod_bloqueio'],
                ]];

                $this->addLinhas(linha: [
                    $urlHelper->l(text: $nm_escola, path: $url, options: $options),
                    $urlHelper->l(text: $registro['ano'], path: $url, options: $options),
                    $urlHelper->l(text: $nm_etapa, path: $url, options: $options),
                    $urlHelper->l(text: dataToBrasil(data_original: $registro['data_inicio']), path: $url, options: $options),
                    $urlHelper->l(text: dataToBrasil(data_original: $registro['data_fim']), path: $url, options: $options),
                ]);
            }
        }

        $this->addPaginador2(
            strUrl: 'educar_bloqueio_lancamento_faltas_notas_lst.php',
            intTotalRegistros: $total,
            mixVariaveisMantidas: $_GET,
            nome: $this->nome,
            intResultadosPorPagina: $this->limite
        );

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 999848, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->array_botao_url[] = 'educar_bloqueio_lancamento_faltas_notas_cad.php';
            $this->array_botao[] = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Listagem de bloqueio de lançamento de notas e faltas por etapa', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Bloqueio de lanÃ§amento de notas e faltas por etapa';
        $this->processoAp = 999848;
    }
};
