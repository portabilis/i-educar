<?php

return new class extends clsListagem {
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

    public $cod_emprestimo;
    public $ref_usuario_devolucao;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $ref_cod_exemplar;
    public $data_retirada;
    public $data_devolucao;
    public $valor_multa;

    public $nm_cliente;
    public $nm_obra;
    public $ref_cod_biblioteca;
    public $ref_cod_acervo;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $cod_biblioteca;
    public $situacao;

    const TODOS = 0;
    const EM_ATRASO = 1;

    public function Gerar()
    {
        $this->titulo = 'Exemplar Devolu&ccedil;&atilde;o - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Cliente',
            'Código exemplar',
            'Tombo',
            'Exemplar',
            'Data Retirada'
        ];

        // Filtros de Foreign Keys
        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = 'lista_busca';
        include('include/pmieducar/educar_campo_lista.php');

        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        $this->campoTexto('nm_cliente', 'Cliente', $this->nm_cliente, 30, 255, false, false, false, '', "<img border=\"0\" onclick=\"pesquisa_cliente();\" id=\"ref_cod_cliente_lupa\" name=\"ref_cod_cliente_lupa\" src=\"imagens/lupa.png\"\/>");
        $this->campoOculto('ref_cod_cliente', $this->ref_cod_cliente);

        // outros Filtros
        $this->inputsHelper()->dynamic('instituicao', ['required' => false, 'instituicao' => $this->ref_cod_instituicao]);
        $this->campoTexto('nm_obra', 'Obra', $this->nm_obra, 30, 255, false, false, false, '', "<img border=\"0\" onclick=\"pesquisa_obra();\" id=\"ref_cod_exemplar_lupa\" name=\"ref_cod_exemplar_lupa\" src=\"imagens/lupa.png\"\/>");
        $this->campoOculto('ref_cod_acervo', $this->ref_cod_acervo);

        $situacoes = [TODOS => 'Todos',
                                             EM_ATRASO => 'Em atraso'];

        $this->campoLista('situacao', 'Situação', $situacoes, $this->situacao);

        $this->campoNumero('ref_cod_exemplar', 'Código exemplar', $this->ref_cod_exemplar, 15, 10);
        $this->campoNumero('tombo', 'Tombo', $this->tombo, 15, 13);

        if ($this->ref_cod_biblioteca) {
            $this->cod_biblioteca = $this->ref_cod_biblioteca;
            $this->campoOculto('cod_biblioteca', $this->cod_biblioteca);
        } else {
            $this->cod_biblioteca = null;
            $this->campoOculto('cod_biblioteca', $this->cod_biblioteca);
        }

        // outros Filtros
        $this->campoData('data_retirada', 'Data Retirada', $this->data_retirada, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_exemplar_emprestimo = new clsPmieducarExemplarEmprestimo();
        $obj_exemplar_emprestimo->setOrderby('data_retirada ASC');
        $obj_exemplar_emprestimo->setLimite($this->limite, $this->offset);

        if ($this->situacao == EM_ATRASO) {
            $obj_exemplar_emprestimo->em_atraso = true;
        }

        $lista = $obj_exemplar_emprestimo->lista(
            null,
            null,
            null,
            $this->ref_cod_cliente,
            $this->ref_cod_exemplar,
            Portabilis_Date_Utils::brToPgSQL($this->data_retirada),
            Portabilis_Date_Utils::brToPgSQL($this->data_retirada),
            null,
            null,
            null,
            false,
            $this->ref_cod_biblioteca,
            false,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola,
            $this->nm_obra,
            $this->tombo
        );

        $total = $obj_exemplar_emprestimo->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_retirada_time'] = strtotime(substr($registro['data_retirada'], 0, 16));
                $registro['data_retirada_br'] = date('d/m/Y', $registro['data_retirada_time']);

                $obj_exemplar = new clsPmieducarExemplar($registro['ref_cod_exemplar']);
                $det_exemplar = $obj_exemplar->detalhe();
                $acervo = $det_exemplar['ref_cod_acervo'];
                $obj_acervo = new clsPmieducarAcervo($acervo);
                $det_acervo = $obj_acervo->detalhe();
                $registro['titulo'] = $det_acervo['titulo'];

                $obj_cliente = new clsPmieducarCliente($registro['ref_cod_cliente']);
                $det_cliente = $obj_cliente->detalhe();
                $ref_idpes = $det_cliente['ref_idpes'];
                $obj_pessoa = new clsPessoa_($ref_idpes);
                $det_pessoa = $obj_pessoa->detalhe();
                $registro['ref_cod_cliente'] = $det_pessoa['nome'];

                $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
                $registro['ref_cod_biblioteca'] = $det_ref_cod_biblioteca['nm_biblioteca'];

                if ($registro['ref_cod_instituicao']) {
                    $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                    $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                    $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];
                }
                if ($registro['ref_cod_escola']) {
                    $obj_ref_cod_escola = new clsPmieducarEscola();
                    $det_ref_cod_escola = array_shift($obj_ref_cod_escola->lista($registro['ref_cod_escola']));
                    $registro['ref_cod_escola'] = $det_ref_cod_escola['nome'];
                }

                $lista_busca = [
                    "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro['cod_emprestimo']}\">{$registro['ref_cod_cliente']}</a>",
                    "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro['cod_emprestimo']}\">{$registro['ref_cod_exemplar']}</a>",
                    "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro['cod_emprestimo']}\">{$det_exemplar['tombo']}</a>",
                    "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro['cod_emprestimo']}\">{$registro['titulo']}</a>",
                    "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro['cod_emprestimo']}\">{$registro['data_retirada_br']}</a>"
                ];

                if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8)) {
                    $lista_busca[] = "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro['cod_emprestimo']}\">{$registro['ref_cod_biblioteca']}</a>";
                } elseif ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4) {
                    $lista_busca[] = "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro['cod_emprestimo']}\">{$registro['ref_cod_biblioteca']}</a>";
                }
                if ($nivel_usuario == 1 || $nivel_usuario == 2) {
                    $lista_busca[] = "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro['cod_emprestimo']}\">{$registro['ref_cod_escola']}</a>";
                }
                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_exemplar_devolucao_det.php?cod_emprestimo={$registro['cod_emprestimo']}\">{$registro['ref_cod_instituicao']}</a>";
                }

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_exemplar_devolucao_lst.php', $total, $_GET, $this->nome, $this->limite);
        $this->largura = '100%';

        $this->breadcrumb('Listagem de exemplares para devolução', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-exemplar-devolucao-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Exemplar Devolu&ccedil;&atilde;o';
        $this->processoAp = '628';
    }
};
