<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Obras" );
        $this->processoAp = "598";
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

    var $cod_acervo;
    var $ref_cod_exemplar_tipo;
    var $ref_cod_acervo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_cod_acervo_colecao;
    var $ref_cod_acervo_idioma;
    var $ref_cod_acervo_editora;
    var $titulo_livro;
    var $sub_titulo;
    var $cdu;
    var $cutter;
    var $cdd;
    var $volume;
    var $num_edicao;
    var $ano;
    var $num_paginas;
    var $isbn;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_biblioteca;
    var $ref_cod_assunto_acervo;
    var $ref_cod_acervo_autor;
    var $nm_autor;

    function Gerar()
    {
        $this->titulo = "Obras - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;

        $lista_busca = array(
            "Obra",
            "Autor(es)",
            "CDD - Cutter",
            "ISBN"
        );

        // Filtros de Foreign Keys
        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = "lista_busca";

    $this->inputsHelper()->dynamic('ano', array('required' => false));
    $this->inputsHelper()->dynamic('instituicao', array('required' => false));
    $this->inputsHelper()->dynamic('escola', array('required' => false));
    $this->inputsHelper()->dynamic('biblioteca', array('required' => false));

        //retira escola e instituição do cabeçalho
        unset($lista_busca[5], $lista_busca[6]);

        $this->addCabecalhos($lista_busca);

        $opcoes_colecao = array();
        $opcoes_colecao[""] = "Selecione";
        $opcoes_exemplar = array();
        $opcoes_exemplar[""] = "Selecione";
        $opcoes_editora = array();
        $opcoes_editora[""] = "Selecione";
        $opcoes_autor = array();
        $opcoes_autor[""] = "Selecione";

        if (is_numeric($this->ref_cod_biblioteca))
        {
            $obj_colecao = new clsPmieducarAcervoColecao();
            $obj_colecao->setOrderby("nm_colecao ASC");
            $obj_colecao->setCamposLista("cod_acervo_colecao, nm_colecao");
            $lst_colecao = $obj_colecao->lista(null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_biblioteca);
            if (is_array($opcoes))
            {
                foreach ($lst_colecao as $colecao)
                {
                    $opcoes_colecao[$colecao["cod_acervo_colecao"]] = $colecao["nm_colecao"];
                }
            }

            $obj_tp_exemplar = new clsPmieducarExemplarTipo();
            $obj_tp_exemplar->setCamposLista("cod_exemplar_tipo, nm_tipo");
            $obj_tp_exemplar->setOrderby("nm_tipo ASC");
            $lst_tp_exemplar = $obj_tp_exemplar->lista(null, $this->ref_cod_biblioteca, null, null, null, null, null, null, null, null, 1);
            if (is_array($lst_tp_exemplar))
            {
                foreach ($lst_tp_exemplar as $tp_exemplar)
                {
                    $opcoes_exemplar[$tp_exemplar["cod_exemplar_tipo"]] = $tp_exemplar["nm_tipo"];
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
        }

        $this->campoLista("ref_cod_acervo_colecao", "Acervo Coleção", $opcoes_colecao, $this->ref_cod_acervo_colecao, "", false, "", "", false, false);
        $this->campoLista("ref_cod_exemplar_tipo", "Tipo Exemplar", $opcoes_exemplar, $this->ref_cod_exemplar_tipo, "", false, "", "", false, false);
        $this->campoLista("ref_cod_acervo_editora", "Editora", $opcoes_editora, $this->ref_cod_acervo_editora, "", false, "", "", false, false);

        $objTemp = new clsPmieducarAcervoAssunto();
        $lista = $objTemp->lista();

        $opcoes = array(null => 'Selecione' );

        if (is_array($lista) && count($lista)) {
          foreach ($lista as $registro) {
            $opcoes[$registro['cod_acervo_assunto']] = $registro['nm_assunto'];
          }
        }

        $this->campoLista('ref_cod_assunto_acervo', 'Assunto', $opcoes, $this->ref_cod_assunto_acervo, '', FALSE, '',
          '', FALSE, FALSE);

        $this->campoTexto( "titulo_livro", "Titulo", $this->titulo_livro, 30, 255, false );
        $this->campoTexto( "sub_titulo", "Subtítulo", $this->sub_titulo, 30, 255, false );
        $this->campoTexto( "cdd", "CDD", $this->cdd, 30, 255, false );
        $this->campoTexto( "cutter", "Cutter", $this->cutter, 30, 255, false );
        $this->campoTexto( "isbn", "ISBN", $this->isbn, 30, 255, false );
        $this->campoTexto( "nm_autor", "Autor", $this->nm_autor, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        if(!is_numeric($this->ref_cod_biblioteca))
        {
            $obj_bib_user = new clsPmieducarBibliotecaUsuario();
            $this->ref_cod_biblioteca = $obj_bib_user->listaBibliotecas($this->pessoa_logada);
        }

        $obj_acervo = new clsPmieducarAcervo();
        $obj_acervo->setOrderby( "titulo ASC" );
        $obj_acervo->setLimite( $this->limite, $this->offset );
        $obj_acervo->ref_cod_acervo_assunto = $this->ref_cod_assunto_acervo;


        $lista = $obj_acervo->listaAcervoBiblioteca($this->ref_cod_biblioteca, $this->titulo_livro, 1, $this->ref_cod_acervo_colecao, $this->ref_cod_exemplar_tipo, $this->ref_cod_acervo_editora, $this->sub_titulo, $this->cdd, $this->cutter, $this->isbn, $this->nm_autor);

        $total = $obj_acervo->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
                $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
                $registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];

                $lista_busca = array(
                    "<a href=\"educar_acervo_det.php?cod_acervo={$registro["cod_acervo"]}\">{$registro["titulo"]} {$registro["sub_titulo"]}</a>",
                    "<a href=\"educar_acervo_det.php?cod_acervo={$registro["cod_acervo"]}\">{$registro["nm_autor"]}</a>",
                    "<a href=\"educar_acervo_det.php?cod_acervo={$registro["cod_acervo"]}\">{$registro["cdd"]} {$registro["cutter"]}</a>",
                    "<a href=\"educar_acervo_det.php?cod_acervo={$registro["cod_acervo"]}\">{$registro["isbn"]}</a>"
                );

                if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8))
                    $lista_busca[] = "<a href=\"educar_acervo_det.php?cod_acervo={$registro["cod_acervo"]}\">{$registro["ref_cod_biblioteca"]}</a>";
                else if ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4)
                    $lista_busca[] = "<a href=\"educar_acervo_det.php?cod_acervo={$registro["cod_acervo"]}\">{$registro["ref_cod_biblioteca"]}</a>";
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_acervo_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 598, $this->pessoa_logada, 11 ) )
        {
        $this->acao = "go(\"educar_acervo_cad.php\")";
        $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $this->breadcrumb('Listagem de obras', [
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

function getExemplarTipo(xml_exemplar_tipo)
{
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

document.getElementById('ref_cod_biblioteca').onchange = function()
{
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

};

</script>
