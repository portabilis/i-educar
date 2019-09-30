<?php


use Illuminate\Support\Facades\Session;

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once 'Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Reservas" );
        $this->processoAp = "609";
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $offset;

    var $cod_reserva;
    var $ref_usuario_libera;
    var $ref_usuario_cad;
    var $ref_cod_cliente;
    var $data_reserva;
    var $data_prevista_disponivel;
    var $data_retirada;
    var $ref_cod_exemplar;

    var $nm_cliente;
    var $nm_exemplar;
    var $ref_cod_biblioteca;
    var $ref_cod_acervo;
    var $ref_cod_instituicao;
    var $ref_cod_escola;
    var $cod_biblioteca;

    var $tipo_reserva;

    function Gerar()
    {
        Session::forget([
            'reservas.cod_cliente',
            'reservas.ref_cod_biblioteca',
        ]);

        $this->titulo = "Reservas - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $lista_busca = array(
            "Cliente",
            "Obra",
            "Data reserva",
            "Data retirada"
        );

        // Filtros de Foreign Keys
        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = "lista_busca";
        include("include/pmieducar/educar_campo_lista.php");

        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        $this->campoTexto("nm_cliente", "Cliente", $this->nm_cliente, 30, 255, false, false, false, "", "<img border=\"0\" onclick=\"pesquisa_cliente();\" id=\"ref_cod_cliente_lupa\" name=\"ref_cod_cliente_lupa\" src=\"imagens/lupa.png\"\/>");
        $this->campoOculto("ref_cod_cliente", $this->ref_cod_cliente);


        // outros Filtros
        $this->campoTexto("nm_exemplar","Obra", $this->nm_exemplar, 30, 255, false, false, false, "", "<img border=\"0\" onclick=\"pesquisa_obra();\" id=\"ref_cod_exemplar_lupa\" name=\"ref_cod_exemplar_lupa\" src=\"imagens/lupa.png\"\/>");
        $this->campoOculto("ref_cod_exemplar", $this->ref_cod_exemplar);
        $this->campoOculto("ref_cod_acervo", $this->ref_cod_acervo);

        // Filtro verificando se ouve retirada
        $resources = array( 1 => 'Todas',
                       2 => 'Sem retirada',
                       3 => 'Com retirada');

        $options = array('label' => 'Tipo de reserva', 'resources' => $resources, 'value' => $this->tipo_reserva);
        $this->inputsHelper()->select('tipo_reserva', $options);

        $this->campoData( "data_reserva", "Data reserva", $this->data_reserva, false );

        if ($this->ref_cod_biblioteca)
        {
            $this->cod_biblioteca = $this->ref_cod_biblioteca;
            $this->campoOculto("cod_biblioteca", $this->cod_biblioteca);
        }
        else
        {
            $this->cod_biblioteca = null;
            $this->campoOculto("cod_biblioteca", $this->cod_biblioteca);
        }

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_reservas = new clsPmieducarReservas();
        $obj_reservas->setOrderby( "data_reserva ASC" );
        $obj_reservas->setLimite( $this->limite, $this->offset );

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
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                // muda os campos data
                $registro["data_reserva_time"] = strtotime( substr( $registro["data_reserva"], 0, 16 ) );
                $registro["data_reserva_br"] = date( "d/m/Y", $registro["data_reserva_time"] );
                $registro["data_retirada_br"] = ($registro["data_retirada"] == null ? '-' :  Portabilis_Date_Utils::PgSqltoBr(substr($registro["data_retirada"],0,10) ));

                    $obj_exemplar = new clsPmieducarExemplar($registro["ref_cod_exemplar"]);
                    $det_exemplar = $obj_exemplar->detalhe();
                    $acervo = $det_exemplar["ref_cod_acervo"];
                    $obj_acervo = new clsPmieducarAcervo($acervo);
                    $det_acervo = $obj_acervo->detalhe();
                    $registro["ref_cod_exemplar"] = $det_acervo["titulo"];

                    $obj_cliente = new clsPmieducarCliente($registro["ref_cod_cliente"]);
                    $det_cliente = $obj_cliente->detalhe();
                    $ref_idpes = $det_cliente["ref_idpes"];
                    $obj_pessoa = new clsPessoa_($ref_idpes);
                    $det_pessoa = $obj_pessoa->detalhe();
                    $registro["ref_cod_cliente"] = $det_pessoa["nome"];

                    $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
                    $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
                    $registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];

                if( $registro["ref_cod_instituicao"] )
                {
                    $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
                    $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                    $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
                }
                if( $registro["ref_cod_escola"] )
                {
                    $obj_ref_cod_escola = new clsPmieducarEscola();
                    $det_ref_cod_escola = array_shift($obj_ref_cod_escola->lista($registro["ref_cod_escola"]));
                    $registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
                }

                $lista_busca = array(
                    "{$registro["ref_cod_cliente"]}",
                    "{$registro["ref_cod_exemplar"]}",
                    "{$registro["data_reserva_br"]}",
                    "{$registro["data_retirada_br"] }"
                );


                if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8))
                    $lista_busca[] = "{$registro["ref_cod_biblioteca"]}";
                else if ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4)
                    $lista_busca[] = "{$registro["ref_cod_biblioteca"]}";
                if ($nivel_usuario == 1 || $nivel_usuario == 2)
                    $lista_busca[] = "{$registro["ref_cod_escola"]}";
                if ($nivel_usuario == 1)
                    $lista_busca[] = "{$registro["ref_cod_instituicao"]}";

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_reservas_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 609, $this->pessoa_logada, 11 ) )
        {
            $this->acao = "go(\"/module/Biblioteca/Reserva\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $this->breadcrumb('Listagem de reservas', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
    }
}
// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>

<script>

function pesquisa_cliente()
{
    pesquisa_valores_popless('educar_pesquisa_cliente_lst.php?campo1=ref_cod_cliente&campo2=nm_cliente')
}

function pesquisa_obra()
{
    var campoBiblioteca = document.getElementById('cod_biblioteca').value;
    pesquisa_valores_popless('educar_pesquisa_obra_lst.php?campo1=ref_cod_exemplar&campo2=nm_exemplar&campo3='+campoBiblioteca)
}

</script>
