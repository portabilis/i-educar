<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Exemplar" );
        $this->processoAp = "606";
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

    var $cod_exemplar;
    var $ref_cod_fonte;
    var $ref_cod_motivo_baixa;
    var $ref_cod_acervo;
    var $ref_cod_situacao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $permite_emprestimo;
    var $preco;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $data_aquisicao;

    var $ref_cod_biblioteca;
    var $ref_cod_instituicao;
    var $ref_cod_escola;

    var $ref_cod_acervo_colecao;
    var $ref_cod_acervo_editora;

    var $titulo_livro;

    function Gerar()
    {
        $this->titulo = "Exemplar - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $lista_busca = array(
            "Tombo",
            "Obra",
            "Tipo"
        );

        // Filtros de Foreign Keys
        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = "lista_busca";
        include("include/pmieducar/educar_campo_lista.php");

        $this->addCabecalhos($lista_busca);


        $opcoes = array( "" => "Selecione" );
        $opcoes_colecao = array();
        $opcoes_colecao[""] = "Selecione";
        $opcoes_editora = array();
        $opcoes_editora[""] = "Selecione";
        $opcoes_fonte = array();
        $opcoes_fonte[""] = "Selecione";
        if( $this->ref_cod_biblioteca )
        {
            $objTemp = new clsPmieducarExemplarTipo();
            $lista = $objTemp->lista(null,$this->ref_cod_biblioteca);
            if ( is_array( $lista ) && count( $lista ) )
            {
                foreach ( $lista as $registro )
                {
                    $opcoes["{$registro['cod_exemplar_tipo']}"] = "{$registro['nm_tipo']}";

                }
            }

            $obj_colecao = new clsPmieducarAcervoColecao();
            $obj_colecao->setOrderby("nm_colecao ASC");
            $obj_colecao->setCamposLista("cod_acervo_colecao, nm_colecao");
            $lst_colecao = $obj_colecao->lista(null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_biblioteca);
            if (is_array($lst_colecao))
            {
                foreach ($lst_colecao as $colecao)
                {
                    $opcoes_colecao[$colecao["cod_acervo_colecao"]] = $colecao["nm_colecao"];
                }
            }

            $obj_editora = new clsPmieducarAcervoEditora();
            $obj_editora->setCamposLista("cod_acervo_editora, nm_editora");
            $obj_editora->setOrderby("nm_editora ASC");
            $lst_editora = $obj_editora->lista(null, null, null, null, null, null, null, null, null, null, null, null, null,
                                                null, null, null, null, 1, $this->ref_cod_biblioteca);
            if (is_array($lst_editora))
            {
                foreach ($lst_editora as $editora)
                {
                    $opcoes_editora[$editora["cod_acervo_editora"]] = $editora["nm_editora"];
                }
            }

            $obj_fonte = new clsPmieducarFonte();
            $obj_fonte->setOrderby("nm_fonte ASC");
            $obj_fonte->setCamposLista("cod_fonte, nm_fonte");
            $lst_fonte = $obj_fonte->lista(null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_biblioteca);
            if (is_array($lst_fonte))
            {
                foreach ($lst_fonte as $fonte)
                {
                    $opcoes_fonte[$fonte["cod_fonte"]] = $fonte["nm_fonte"];
                }
            }
        }

        $this->campoLista( "ref_cod_exemplar_tipo", "Exemplar Tipo", $opcoes, $this->ref_cod_exemplar_tipo,null,null,null,null,null,false );

        $this->campoLista("ref_cod_acervo_colecao", "Acervo Coleção", $opcoes_colecao, $this->ref_cod_acervo_colecao, "", false, "", "", false, false);
        $this->campoLista("ref_cod_acervo_editora", "Editora", $opcoes_editora, $this->ref_cod_acervo_editora, "", false, "", "", false, false);
        $this->campoLista("ref_cod_fonte", "Fonte", $opcoes_fonte, $this->ref_cod_fonte, "", false, "", "", false, false);


        $this->campoTexto("titulo_livro","T&iacute;tulo da Obra",$this->titulo_livro,25,255,false);
        $this->campoNumero("cod_exemplar","Tombo",$this->cod_exemplar,10,50,false);


        $opcoes = array( "NULL" => "Selecione" );

        if( $this->ref_cod_acervo && $this->ref_cod_acervo != "NULL")
        {
            $objTemp = new clsPmieducarAcervo($this->ref_cod_acervo);
            $detalhe = $objTemp->detalhe();
            if ( $detalhe )
            {
                $opcoes["{$detalhe['cod_acervo']}"] = "{$detalhe['titulo']}";
            }
        }

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_exemplar = new clsPmieducarExemplar();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_exemplar->codUsuario = $this->pessoa_logada;
        }

        $obj_exemplar->setOrderby( "tombo ASC" );
        $obj_exemplar->setLimite( $this->limite, $this->offset );

        $lista = $obj_exemplar->lista_com_acervos(
            null,
            $this->ref_cod_fonte,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            null,
            $this->ref_cod_exemplar_tipo,
            $this->titulo_livro,
            $this->ref_cod_biblioteca,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola,
            $this->ref_cod_acervo_colecao,
            $this->ref_cod_acervo_editora,
            $this->cod_exemplar
        );

        $total = $obj_exemplar->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                // muda os campos data

                $registro["data_aquisicao_time"] = strtotime( substr( $registro["data_aquisicao"], 0, 16 ) );
                $registro["data_aquisicao_br"] = date( "d/m/Y H:i", $registro["data_aquisicao_time"] );

                $obj_ref_cod_fonte = new clsPmieducarFonte( $registro["ref_cod_fonte"] );
                $det_ref_cod_fonte = $obj_ref_cod_fonte->detalhe();
                $registro["ref_cod_fonte"] = $det_ref_cod_fonte["nm_fonte"];

                $obj_ref_cod_motivo_baixa = new clsPmieducarMotivoBaixa( $registro["ref_cod_motivo_baixa"] );
                $det_ref_cod_motivo_baixa = $obj_ref_cod_motivo_baixa->detalhe();
                $registro["ref_cod_motivo_baixa"] = $det_ref_cod_motivo_baixa["nm_motivo_baixa"];

                $obj_ref_cod_acervo = new clsPmieducarAcervo( $registro["ref_cod_acervo"] );
                $det_ref_cod_acervo = $obj_ref_cod_acervo->detalhe();
                $registro["ref_cod_acervo"] = $det_ref_cod_acervo["titulo"] . " " . $det_ref_cod_acervo["sub_titulo"];

                $obj_ref_cod_tipo = new clsPmieducarExemplarTipo( $det_ref_cod_acervo["ref_cod_exemplar_tipo"] );
                $det_ref_cod_tipo = $obj_ref_cod_tipo->detalhe();
                $registro["ref_cod_tipo"] = $det_ref_cod_tipo["nm_tipo"];

                $obj_ref_cod_situacao = new clsPmieducarSituacao( $registro["ref_cod_situacao"] );
                $det_ref_cod_situacao = $obj_ref_cod_situacao->detalhe();
                $registro["ref_cod_situacao"] = $det_ref_cod_situacao["nm_situacao"];

                $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
                $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
                $registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];
                $registro["ref_cod_instituicao"] = $det_ref_cod_biblioteca["ref_cod_instituicao"];
                $registro["ref_cod_escola"] = $det_ref_cod_biblioteca["ref_cod_escola"];
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
                    "<a href=\"educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}\">{$registro["tombo"]}</a>",
                    "<a href=\"educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}\">{$registro["ref_cod_acervo"]}</a>",
                    "<a href=\"educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}\">{$registro["ref_cod_tipo"]}</a>"
                );

                if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8))
                    $lista_busca[] = "<a href=\"educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}\">{$registro["ref_cod_biblioteca"]}</a>";
                else if ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4)
                    $lista_busca[] = "<a href=\"educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}\">{$registro["ref_cod_biblioteca"]}</a>";
                if ($nivel_usuario == 1 || $nivel_usuario == 2)
                    $lista_busca[] = "<a href=\"educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}\">{$registro["ref_cod_escola"]}</a>";
                if ($nivel_usuario == 1)
                    $lista_busca[] = "<a href=\"educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}\">{$registro["ref_cod_instituicao"]}</a>";

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_exemplar_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11 ) )
        {
        $this->acao = "go(\"educar_exemplar_cad.php\")";
        $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $this->breadcrumb('Listagem de exemplares', [
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
/*
var before_getTipo = function(){}
var after_getTipo = function(){}
*/
function getExemplarTipo(xml_exemplar_tipo)
{
    /*
    before_getTipo();

    var campoTipo = document.getElementById('ref_cod_exemplar_tipo');
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca');

    campoTipo.length = 1;

    for (var j = 0; j < tipos.length; j++)
    {
        if (tipos[j][2] == campoBiblioteca.value)
        {
            campoTipo.options[campoTipo.options.length] = new Option( tipos[j][1], tipos[j][0],false,false);
        }
    }

    after_getTipo();
    */
    var campoTipo = document.getElementById('ref_cod_exemplar_tipo');
    var DOM_array = xml_exemplar_tipo.getElementsByTagName( "exemplar_tipo" );

    if(DOM_array.length)
    {
        campoTipo.length = 1;
        campoTipo.options[0].text = 'Selecione um tipo de exemplar';
        campoTipo.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoTipo.options[campoTipo.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_exemplar_tipo"),false,false);
        }
    }
    else
        campoTipo.options[0].text = 'A biblioteca não possui nenhum tipo de exemplar';
}


function getAcervoColecao(xml_acervo_colecao)
{
    var campoColecao = document.getElementById('ref_cod_acervo_colecao');
    var DOM_array = xml_acervo_colecao.getElementsByTagName( "acervo_colecao" );
    if(DOM_array.length)
    {
        campoColecao.length = 1;
        campoColecao.options[0].text = 'Selecione uma coleção';
        campoColecao.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoColecao.options[campoColecao.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_acervo_colecao"),false,false);
        }
    }
    else
        campoColecao.options[0].text = 'A biblioteca não possui nenhuma coleção';
}

function getAcervoEditora(xml_acervo_editora)
{
    var campoEditora = document.getElementById('ref_cod_acervo_editora');
    var DOM_array = xml_acervo_editora.getElementsByTagName( "acervo_editora" );
    if(DOM_array.length)
    {
        campoEditora.length = 1;
        campoEditora.options[0].text = 'Selecione uma editora';
        campoEditora.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoEditora.options[campoEditora.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_acervo_editora"),false,false);
        }
    }
    else
        campoEditora.options[0].text = 'A biblioteca não possui nenhuma editora';
}


function getFonte(xml_fonte)
{
    var campoFonte = document.getElementById('ref_cod_fonte');
    var DOM_array = xml_fonte.getElementsByTagName( "fonte" );
    if(DOM_array.length)
    {
        campoFonte.length = 1;
        campoFonte.options[0].text = 'Selecione uma fonte';
        campoFonte.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoFonte.options[campoFonte.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_acervo_editora"),false,false);
        }
    }
    else
        campoFonte.options[0].text = 'A biblioteca não possui nenhuma editora';
}

document.getElementById('ref_cod_biblioteca').onchange = function()
{
//  getTipo();

    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
    var campoTipo = document.getElementById('ref_cod_exemplar_tipo');

    campoTipo.length = 1;
    campoTipo.disabled = true;
    campoTipo.options[0].text = 'Carregando tipo de exemplar';

    var xml_exemplar_tipo = new ajax( getExemplarTipo );
    xml_exemplar_tipo.envia( "educar_exemplar_tipo_xml.php?bib="+campoBiblioteca );


    var campoColecao = document.getElementById('ref_cod_acervo_colecao');
    campoColecao.length = 1;
    campoColecao.disabled = true;
    campoColecao.options[0].text = 'Carregando coleção';
    var xml_acervo_colecao = new ajax(getAcervoColecao);
    xml_acervo_colecao.envia("educar_acervo_colecao_xml.php?bib="+campoBiblioteca);

    var campoEditora = document.getElementById('ref_cod_acervo_editora');
    campoEditora.length = 1;
    campoEditora.disabled = true;
    campoEditora.options[0].text = 'Carregando editora';
    var xml_acervo_editora = new ajax(getAcervoEditora);
    xml_acervo_editora.envia("educar_acervo_editora_xml.php?bib="+campoBiblioteca);

    var campoFonte = document.getElementById('ref_cod_fonte');
    campoFonte.length = 1;
    campoFonte.disabled = true;
    campoFonte.options[0].text = 'Carregando fonte';
    var xml_acervo_fonte = new ajax(getFonte);
    xml_acervo_fonte.envia("educar_fonte_xml.php?bib="+campoBiblioteca);

};

function pesquisa()
{
    var biblioteca = document.getElementById('ref_cod_biblioteca').value;
    if(!biblioteca)
    {
        alert('Por favor,\nselecione uma biblioteca!');
        return;
    }
    pesquisa_valores_popless('educar_pesquisa_acervo_lst.php?campo1=ref_cod_acervo&ref_cod_bilioteca=' + biblioteca , 'ref_cod_acervo')
}

//pesquisa_valores_popless('educar_pesquisa_acervo_lst.php?campo1=ref_cod_acervo', 'ref_cod_acervo')
</script>
