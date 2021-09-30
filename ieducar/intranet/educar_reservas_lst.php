<?php

use Illuminate\Support\Facades\Session;

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

    public $cod_reserva;
    public $ref_usuario_libera;
    public $ref_usuario_cad;
    public $ref_cod_cliente;
    public $data_reserva;
    public $data_prevista_disponivel;
    public $data_retirada;
    public $ref_cod_exemplar;

    public $nm_cliente;
    public $nm_exemplar;
    public $ref_cod_biblioteca;
    public $ref_cod_acervo;
    public $ref_cod_instituicao;
    public $ref_cod_escola;
    public $cod_biblioteca;

    public $tipo_reserva;

    public function Gerar()
    {
        Session::forget([
            'reservas.cod_cliente',
            'reservas.ref_cod_biblioteca',
        ]);

        $this->titulo = 'Reservas - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Cliente',
            'Obra',
            'Data reserva',
            'Data retirada'
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
        $this->campoTexto('nm_exemplar', 'Obra', $this->nm_exemplar, 30, 255, false, false, false, '', "<img border=\"0\" onclick=\"pesquisa_obra();\" id=\"ref_cod_exemplar_lupa\" name=\"ref_cod_exemplar_lupa\" src=\"imagens/lupa.png\"\/>");
        $this->campoOculto('ref_cod_exemplar', $this->ref_cod_exemplar);
        $this->campoOculto('ref_cod_acervo', $this->ref_cod_acervo);

        // Filtro verificando se ouve retirada
        $resources = [ 1 => 'Todas',
                       2 => 'Sem retirada',
                       3 => 'Com retirada'];

        $options = ['label' => 'Tipo de reserva', 'resources' => $resources, 'value' => $this->tipo_reserva];
        $this->inputsHelper()->select('tipo_reserva', $options);

        $this->campoData('data_reserva', 'Data reserva', $this->data_reserva, false);

        if ($this->ref_cod_biblioteca) {
            $this->cod_biblioteca = $this->ref_cod_biblioteca;
            $this->campoOculto('cod_biblioteca', $this->cod_biblioteca);
        } else {
            $this->cod_biblioteca = null;
            $this->campoOculto('cod_biblioteca', $this->cod_biblioteca);
        }

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_reservas = new clsPmieducarReservas();
        $obj_reservas->setOrderby('data_reserva ASC');
        $obj_reservas->setLimite($this->limite, $this->offset);

        $lista = $obj_reservas->lista(
            null,
            null,
            null,
            $this->ref_cod_cliente,
            Portabilis_Date_Utils::brToPgSQL($this->data_reserva),
            null,
            null,
            null,
            null,
            null,
            $this->ref_cod_exemplar,
            1,
            $this->ref_cod_biblioteca,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola,
            ($this->tipo_reserva == 1 ? null : ($this->tipo_reserva == 2 ? true : false))
        );

        $total = $obj_reservas->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                $registro['data_reserva_time'] = strtotime(substr($registro['data_reserva'], 0, 16));
                $registro['data_reserva_br'] = date('d/m/Y', $registro['data_reserva_time']);
                $registro['data_retirada_br'] = ($registro['data_retirada'] == null ? '-' :  Portabilis_Date_Utils::PgSqltoBr(substr($registro['data_retirada'], 0, 10)));

                $obj_exemplar = new clsPmieducarExemplar($registro['ref_cod_exemplar']);
                $det_exemplar = $obj_exemplar->detalhe();
                $acervo = $det_exemplar['ref_cod_acervo'];
                $obj_acervo = new clsPmieducarAcervo($acervo);
                $det_acervo = $obj_acervo->detalhe();
                $registro['ref_cod_exemplar'] = $det_acervo['titulo'];

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
                    "{$registro['ref_cod_cliente']}",
                    "{$registro['ref_cod_exemplar']}",
                    "{$registro['data_reserva_br']}",
                    "{$registro['data_retirada_br'] }"
                ];

                if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8)) {
                    $lista_busca[] = "{$registro['ref_cod_biblioteca']}";
                } elseif ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4) {
                    $lista_busca[] = "{$registro['ref_cod_biblioteca']}";
                }
                if ($nivel_usuario == 1 || $nivel_usuario == 2) {
                    $lista_busca[] = "{$registro['ref_cod_escola']}";
                }
                if ($nivel_usuario == 1) {
                    $lista_busca[] = "{$registro['ref_cod_instituicao']}";
                }

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_reservas_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(609, $this->pessoa_logada, 11)) {
            $this->acao = 'go("/module/Biblioteca/Reserva")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de reservas', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-reserva-lst.js');
    }

    public function Formular()
    {
        $this->title = 'Reservas';
        $this->processoAp = '609';
    }
};
