<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Sequ&ecirc;ncia Enturma&ccedil;&atilde;o" );
        $this->processoAp = "587";
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

    var $ref_serie_origem;
    var $ref_serie_destino;
    var $ref_curso_origem;
    var $ref_curso_destino;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Sequ&ecirc;ncia Enturma&ccedil;&atilde;o - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $lista_busca = array(
            "Curso Origem",
            "S&eacute;rie Origem",
            "Curso Destino",
            "S&eacute;rie Destino"
        );

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
            $lista_busca[] = "Institui&ccedil;&atilde;o";
        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        if( $nivel_usuario == 1 )
        {
            $objInstituicao = new clsPmieducarInstituicao();
            $opcoes = array( "" => "Selecione" );
            $objInstituicao->setOrderby( "nm_instituicao ASC" );
            $lista = $objInstituicao->lista();
            if( is_array( $lista ) )
            {
                foreach ( $lista AS $linha )
                {
                    $opcoes[$linha["cod_instituicao"]] = $linha["nm_instituicao"];
                }
            }
            $this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao, "",null,null,null,null,false );
        }
        else
        {
            $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
            $obj_usuario_det = $obj_usuario->detalhe();
            $this->ref_cod_instituicao = $obj_usuario_det["ref_cod_instituicao"];
        }

        $opcoes = array( "" => "Selecione" );
        $opcoes_ = array( "" => "Selecione" );

            // EDITAR
            if ($this->ref_cod_instituicao)
            {
                $objTemp = new clsPmieducarCurso();
                $objTemp->setOrderby("nm_curso");
                $lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_instituicao );
                if ( is_array( $lista ) && count( $lista ) )
                {
                    foreach ( $lista as $registro )
                    {
                        $opcoes[$registro["cod_curso"]] = $registro["nm_curso"];
                        $opcoes_[$registro["cod_curso"]] = $registro["nm_curso"];
                    }
                }
            }

        $this->campoLista( "ref_curso_origem", "Curso Origem", $opcoes, $this->ref_curso_origem,"",true,"","",false,false);
        $this->campoLista( "ref_curso_destino", " Curso Destino", $opcoes_, $this->ref_curso_destino,"",false,"","",false,false);

        // primary keys

        $opcoes = array( "" => "Selecione" );
        $opcoes_ = array( "" => "Selecione" );

            if ($this->ref_curso_origem)
            {
                $objTemp = new clsPmieducarSerie();
                $lista = $objTemp->lista( null,null,null,$this->ref_curso_origem,null,null,null,null,null,null,null,null,1 );
                if ( is_array( $lista ) && count( $lista ) )
                {
                    foreach ( $lista as $registro )
                    {
                        $opcoes[$registro["cod_serie"]] = $registro["nm_serie"];
                    }
                }
            }
            if ($this->ref_curso_destino)
            {
                $objTemp = new clsPmieducarSerie();
                $lista = $objTemp->lista( null,null,null,$this->ref_curso_destino,null,null,null,null,null,null,null,null,1 );
                if ( is_array( $lista ) && count( $lista ) )
                {
                    foreach ( $lista as $registro )
                    {
                        $opcoes_[$registro["cod_serie"]] = $registro["nm_serie"];
                    }
                }
            }

        $this->campoLista( "ref_serie_origem", "S&eacute;rie Origem", $opcoes, $this->ref_serie_origem,null,true,"","",false,false);
        $this->campoLista( "ref_serie_destino", " S&eacute;rie Destino", $opcoes_, $this->ref_serie_destino,"",false,"","",false,false);


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_sequencia_serie = new clsPmieducarSequenciaSerie();
        $obj_sequencia_serie->setOrderby( "data_cadastro ASC" );
        $obj_sequencia_serie->setLimite( $this->limite, $this->offset );

        $lista = $obj_sequencia_serie->lista(
            $this->ref_serie_origem,
            $this->ref_serie_destino,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_curso_origem,
            $this->ref_curso_destino,
            $this->ref_cod_instituicao
        );

        $total = $obj_sequencia_serie->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                    $obj_ref_serie_origem = new clsPmieducarSerie( $registro["ref_serie_origem"] );
                    $det_ref_serie_origem = $obj_ref_serie_origem->detalhe();
                    $serie_origem = $det_ref_serie_origem["nm_serie"];
                    $registro["ref_curso_origem"] = $det_ref_serie_origem["ref_cod_curso"];

                        $obj_ref_curso_origem = new clsPmieducarCurso( $registro["ref_curso_origem"] );
                        $det_ref_curso_origem = $obj_ref_curso_origem->detalhe();
                        $registro["ref_curso_origem"] = $det_ref_curso_origem["nm_curso"];
                        $registro["ref_cod_instituicao"] = $det_ref_curso_origem["ref_cod_instituicao"];

                            $obj_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
                            $det_instituicao = $obj_instituicao->detalhe();
                            $registro["ref_cod_instituicao"] = $det_instituicao["nm_instituicao"];

                    $obj_ref_serie_destino = new clsPmieducarSerie( $registro["ref_serie_destino"] );
                    $det_ref_serie_destino = $obj_ref_serie_destino->detalhe();
                    $serie_destino = $det_ref_serie_destino["nm_serie"];
                    $registro["ref_curso_destino"] = $det_ref_serie_destino["ref_cod_curso"];

                        $obj_ref_curso_destino = new clsPmieducarCurso( $registro["ref_curso_destino"] );
                        $det_ref_curso_destino = $obj_ref_curso_destino->detalhe();
                        $registro["ref_curso_destino"] = $det_ref_curso_destino["nm_curso"];

                $lista_busca = array(
                    "<a href=\"educar_sequencia_serie_det.php?ref_serie_origem={$registro["ref_serie_origem"]}&ref_serie_destino={$registro["ref_serie_destino"]}\">{$registro["ref_curso_origem"]}</a>",
                    "<a href=\"educar_sequencia_serie_det.php?ref_serie_origem={$registro["ref_serie_origem"]}&ref_serie_destino={$registro["ref_serie_destino"]}\">{$serie_origem}</a>",
                    "<a href=\"educar_sequencia_serie_det.php?ref_serie_origem={$registro["ref_serie_origem"]}&ref_serie_destino={$registro["ref_serie_destino"]}\">{$registro["ref_curso_destino"]}</a>",
                    "<a href=\"educar_sequencia_serie_det.php?ref_serie_origem={$registro["ref_serie_origem"]}&ref_serie_destino={$registro["ref_serie_destino"]}\">{$serie_destino}</a>"
                );

                if ($nivel_usuario == 1)
                    $lista_busca[] = "<a href=\"educar_sequencia_serie_det.php?ref_serie_origem={$registro["ref_serie_origem"]}&ref_serie_destino={$registro["ref_serie_destino"]}\">{$registro["ref_cod_instituicao"]}</a>";
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_sequencia_serie_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 587, $this->pessoa_logada, 3 ) )
        {
            $this->acao = "go(\"educar_sequencia_serie_cad.php\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $this->breadcrumb('Listagem de sequências de enturmação', [
            url('intranet/educar_index.php') => 'Escola',
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

function getCurso(xml_curso)
{
    /*
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoCurso = document.getElementById('ref_curso_origem');
    var campoCurso_ = document.getElementById('ref_curso_destino');

    campoCurso.length = 1;
    campoCurso_.length = 1;
    for (var j = 0; j < curso.length; j++)
    {
        if (curso[j][2] == campoInstituicao)
        {
            campoCurso.options[campoCurso.options.length] = new Option( curso[j][1], curso[j][0],false,false);
            campoCurso_.options[campoCurso_.options.length] = new Option( curso[j][1], curso[j][0],false,false);
        }
    }
    */
    var campoCurso = document.getElementById('ref_curso_origem');
    var campoCurso_ = document.getElementById('ref_curso_destino');
    var DOM_array = xml_curso.getElementsByTagName( "curso" );

    if(DOM_array.length)
    {
        campoCurso.length = 1;
        campoCurso.options[0].text = 'Selecione um curso origem';
        campoCurso.disabled = false;

        campoCurso_.length = 1;
        campoCurso_.options[0].text = 'Selecione um curso destino';
        campoCurso_.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoCurso.options[campoCurso.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
            campoCurso_.options[campoCurso_.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
        }
    }
    else
    {
        campoCurso.options[0].text = 'A instituição não possui nenhum curso';
        campoCurso_.options[0].text = 'A instituição não possui nenhum curso';
    }
}

function getSerie(xml_serie)
{
    var campoSerie = document.getElementById('ref_serie_origem');
    var DOM_array = xml_serie.getElementsByTagName( "serie" );

    if(DOM_array.length)
    {
        campoSerie.length = 1;
        campoSerie.options[0].text = 'Selecione uma série origem';
        campoSerie.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoSerie.options[campoSerie.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_serie"),false,false);
        }
    }
    else
        campoSerie.options[0].text = 'O curso origem não possui nenhuma série';
}

function getSerie_(xml_serie_)
{
    var campoSerie_ = document.getElementById('ref_serie_destino');
    var DOM_array = xml_serie_.getElementsByTagName( "serie" );

    if(DOM_array.length)
    {
        campoSerie_.length = 1;
        campoSerie_.options[0].text = 'Selecione uma série destino';
        campoSerie_.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoSerie_.options[campoSerie_.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_serie"),false,false);
        }
    }
    else
        campoSerie_.options[0].text = 'O curso origem não possui nenhuma série';
}
/*
function getSerie( tipo )
{
    var campoCurso = document.getElementById('ref_curso_origem').value;
    var campoCurso_ = document.getElementById('ref_curso_destino').value;
    var campoSerie = document.getElementById('ref_serie_origem');
    var campoSerie_ = document.getElementById('ref_serie_destino');


    if (tipo == 1)
    {
        campoSerie.length = 1;
    }
    else if (tipo == 2)
    {
        campoSerie_.length = 1;
    }

    for (var j = 0; j < serie.length; j++)
    {
        if (tipo == 1)
        {
            if (serie[j][2] == campoCurso)
            {
                campoSerie.options[campoSerie.options.length] = new Option( serie[j][1], serie[j][0],false,false);
            }
        }
        else if (tipo == 2)
        {
            if (serie[j][2] == campoCurso_)
            {
                campoSerie_.options[campoSerie_.options.length] = new Option( serie[j][1], serie[j][0],false,false);
            }
        }
    }
}
*/

document.getElementById('ref_cod_instituicao').onchange = function()
{
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

    var campoCurso = document.getElementById('ref_curso_origem');
    campoCurso.length = 1;
    campoCurso.disabled = true;
    campoCurso.options[0].text = 'Carregando curso origem';

    var campoCurso_ = document.getElementById('ref_curso_destino');
    campoCurso_.length = 1;
    campoCurso_.disabled = true;
    campoCurso_.options[0].text = 'Carregando curso destino';

    var xml_curso = new ajax( getCurso );
    xml_curso.envia( "educar_curso_xml2.php?ins="+campoInstituicao );
};

document.getElementById('ref_curso_origem').onchange = function()
{
    var campoCurso = document.getElementById('ref_curso_origem').value;

    var campoSerie = document.getElementById('ref_serie_origem');
    campoSerie.length = 1;
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Carregando série origem';

    var xml_serie = new ajax( getSerie );
    xml_serie.envia( "educar_serie_xml.php?cur="+campoCurso )
};

document.getElementById('ref_curso_destino').onchange = function()
{
    var campoCurso_ = document.getElementById('ref_curso_destino').value;

    var campoSerie_ = document.getElementById('ref_serie_destino');
    campoSerie_.length = 1;
    campoSerie_.disabled = true;
    campoSerie_.options[0].text = 'Carregando série destino';

    var xml_serie_ = new ajax( getSerie_ );
    xml_serie_.envia( "educar_serie_xml.php?cur="+campoCurso_ )
};

</script>
