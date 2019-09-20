<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once "include/clsBase.inc.php";
require_once "include/clsCadastro.inc.php";
require_once "include/clsBanco.inc.php";
require_once "include/pmieducar/geral.inc.php";
require_once "include/pmieducar/clsPmieducarCategoriaObra.inc.php";
require_once "include/pmieducar/clsPmieducarCategoriaAcervo.inc.php";
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Obras" );
        $this->processoAp = "598";
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

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
    var $volume;
    var $num_edicao;
    var $ano;
    var $num_paginas;
    var $isbn;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_biblioteca;
    var $dimencao;
    var $ref_cod_tipo_autor;
    var $tipo_autor;
    var $material_ilustrativo;
    var $dimencao_ilustrativo;
    var $local;
    var $ref_cod_instituicao;
    var $ref_cod_escola;

    var $checked;

    var $acervo_autor;
    var $ref_cod_acervo_autor;
    var $principal;
    var $incluir_autor;
    var $excluir_autor;

    var $colecao;
    var $editora;
    var $idioma;
    var $autor;

  protected function setSelectionFields()
  {

  }

    function Inicializar()
    {
        $retorno = "Novo";

        $this->cod_acervo=$_GET["cod_acervo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 598, $this->pessoa_logada, 11,  "educar_acervo_lst.php" );

        if( is_numeric( $this->cod_acervo ) )
        {

            $obj = new clsPmieducarAcervo( $this->cod_acervo );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
                $obj_det = $obj_biblioteca->detalhe();

                $this->ref_cod_instituicao = $obj_det["ref_cod_instituicao"];
                $this->ref_cod_escola = $obj_det["ref_cod_escola"];


                $obj_permissoes = new clsPermissoes();
                if( $obj_permissoes->permissao_excluir( 598, $this->pessoa_logada, 11 ) )
                {
                    $this->fexcluir = true;
                }

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_acervo_det.php?cod_acervo={$registro["cod_acervo"]}" : "educar_acervo_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' obra', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        if( $_POST )
        {
            foreach( $_POST AS $campo => $val )
                $this->$campo = ( $this->$campo ) ? $this->$campo : $val;
        }
        if(is_numeric($this->colecao))
        {
            $this->ref_cod_acervo_colecao = $this->colecao;
        }
        if(is_numeric($this->editora))
        {
            $this->ref_cod_acervo_editora = $this->editora;
        }
        if(is_numeric($this->idioma))
        {
            $this->ref_cod_acervo_idioma = $this->idioma;
        }
        if(is_numeric($this->autor))
        {
            $this->ref_cod_acervo_autor = $this->autor;
        }

        // primary keys
        $this->campoOculto( "cod_acervo", $this->cod_acervo );
        $this->campoOculto( "colecao", "" );
        $this->campoOculto( "editora", "" );
        $this->campoOculto( "idioma", "" );
        $this->campoOculto( "autor", "" );

    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'biblioteca', 'bibliotecaTipoExemplar'));

    // Obra referência
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

        $this->campoLista("ref_cod_acervo","Obra Refer&ecirc;ncia",$opcoes,$this->ref_cod_acervo,"",false,"","<img border=\"0\" onclick=\"pesquisa();\" id=\"ref_cod_acervo_lupa\" name=\"ref_cod_acervo_lupa\" src=\"imagens/lupa.png\"\/>",false,false);

    // Coleção
        $opcoes = array( "" => "Selecione" );

        $objTemp = new clsPmieducarAcervoColecao();
        $lista = $objTemp->lista();
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $opcoes["{$registro['cod_acervo_colecao']}"] = "{$registro['nm_colecao']}";
            }
        }
        $this->campoLista( "ref_cod_acervo_colecao", "Cole&ccedil;&atilde;o", $opcoes, $this->ref_cod_acervo_colecao,"",false,"","<img id='img_colecao' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"showExpansivelImprimir(500, 200,'educar_acervo_colecao_cad_pop.php',[], 'Coleção')\" />",false,false );

    // Idioma
        $opcoes = array( "" => "Selecione" );

        $objTemp = new clsPmieducarAcervoIdioma();
        $lista = $objTemp->lista();
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $opcoes["{$registro['cod_acervo_idioma']}"] = "{$registro['nm_idioma']}";
            }
        }

        $this->campoLista( "ref_cod_acervo_idioma", "Idioma", $opcoes, $this->ref_cod_acervo_idioma, "", false, "", "<img id='img_idioma' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"showExpansivelImprimir(400, 150,'educar_acervo_idioma_cad_pop.php',[], 'Idioma')\" />" );

        $opcoes = array( "" => "Selecione" );

        $objTemp = new clsPmieducarAcervoEditora();
        $lista = $objTemp->lista();
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $opcoes["{$registro['cod_acervo_editora']}"] = "{$registro['nm_editora']}";
            }
        }

        $this->campoLista( "ref_cod_acervo_editora", "Editora", $opcoes, $this->ref_cod_acervo_editora, "", false, "", "<img id='img_editora' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"showExpansivelImprimir(400, 320,'educar_acervo_editora_cad_pop.php',[], 'Editora')\" />" );


        //-----------------------INCLUI AUTOR------------------------//

        $opcoes = array( "" => "Selecione", 1 => "Autor - Nome pessoal", 2 => "Autor - Evento", 3 => "Autor - Entidade coletiva", 4 => "Obra anônima");
        $this->campoLista( "ref_cod_tipo_autor", "Tipo de autor", $opcoes, $this->ref_cod_tipo_autor, false, true , false , false  , false, false  );
        $this->campoTexto( "tipo_autor", "", $this->tipo_autor, 40, 255, false);

    $options       = array('label' => 'Autores', 'multiple' => true, 'required' => false, 'options' => array('value' => null));

    $this->inputsHelper()->select('autores[]', $options);
        //$this->inputsHelper()->multipleSearchAutores('', $options, $helperOptions);

        // text
        $this->campoTexto( "titulo", "T&iacute;tulo", $this->titulo, 40, 255, true );
        $this->campoTexto( "sub_titulo", "Subt&iacute;tulo", $this->sub_titulo, 40, 255, false );
        $this->campoTexto( "estante", "Estante", $this->estante, 20, 15, false );
        $this->campoTexto( "dimencao", "Dimensão", $this->dimencao, 20, 255, false );
        $this->campoTexto( "material_ilustrativo", "Material ilustrativo", $this->material_ilustrativo, 20, 255, false );
        //$this->campoTexto( "dimencao_ilustrativo", "Dimensão da ilustração", $this->dimencao_ilustrativo, 20, 255, false );
        $this->campoTexto( "local", "Local", $this->local, 20, 255, false );

        $helperOptions = array('objectName' => 'assuntos');
        $options       = array('label' => 'Assuntos', 'size' => 50, 'required' => false, 'options' => array('value' => null));
        $this->inputsHelper()->multipleSearchAssuntos('', $options, $helperOptions);

        $helperOptions = array('objectName' => 'categorias');
        $options       = array('label' => 'Categorias', 'size' => 50, 'required' => false, 'options' => array('value' => null));
        $this->inputsHelper()->multipleSearchCategoriaObra('', $options, $helperOptions);

        $this->campoTexto( "cdd", "CDD", $this->cdd, 20, 15, false );
        $this->campoTexto( "cdu", "CDU", $this->cdu, 20, 15, false );
        $this->campoTexto( "cutter", "Cutter", $this->cutter, 20, 15, false );
        $this->campoNumero( "volume", "Volume", $this->volume, 20, 255, false );
        $this->campoNumero( "num_edicao", "N&uacute;mero Edic&atilde;o", $this->num_edicao, 20, 255, false );
        $this->campotexto( "ano", "Ano", $this->ano, 25, 25, false );
        $this->campoNumero( "num_paginas", "N&uacute;mero P&aacute;ginas", $this->num_paginas, 5, 255, false );
        $this->campoTexto( "isbn", "ISBN", $this->isbn, 20, 13, false );

    }

    function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 598, $this->pessoa_logada, 11,  "educar_acervo_lst.php" );



        $obj = new clsPmieducarAcervo( null, $this->ref_cod_exemplar_tipo, $this->ref_cod_acervo, null, $this->pessoa_logada, $this->ref_cod_acervo_colecao, $this->ref_cod_acervo_idioma, $this->ref_cod_acervo_editora, $this->titulo, $this->sub_titulo, $this->cdu, $this->cutter, $this->volume, $this->num_edicao, $this->ano, $this->num_paginas, $this->isbn, null, null, 1, $this->ref_cod_biblioteca, $this->cdd, $this->estante, $this->dimencao, $this->material_ilustrativo, null ,$this->local , $this->ref_cod_tipo_autor , $this->tipo_autor );
        $this->cod_acervo = $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
      $obj->cod_acervo = $this->cod_acervo;
      $acervo = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("acervo", $this->pessoa_logada, $this->cod_acervo);
      $auditoria->inclusao($acervo);
            #cadastra assuntos para a obra
            $this->gravaAssuntos($cadastrou);
            $this->gravaAutores($cadastrou);
            $this->gravaCategorias($cadastrou);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";

            $this->simpleRedirect('educar_acervo_lst.php');
        }
        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 598, $this->pessoa_logada, 11,  "educar_acervo_lst.php" );



        $obj = new clsPmieducarAcervo($this->cod_acervo, $this->ref_cod_exemplar_tipo, $this->ref_cod_acervo, $this->pessoa_logada, null, $this->ref_cod_acervo_colecao, $this->ref_cod_acervo_idioma, $this->ref_cod_acervo_editora, $this->titulo, $this->sub_titulo, $this->cdu, $this->cutter, $this->volume, $this->num_edicao, $this->ano, $this->num_paginas, $this->isbn, null, null, 1, $this->ref_cod_biblioteca, $this->cdd, $this->estante, $this->dimencao, $this->material_ilustrativo, null, $this->local, $this->ref_cod_tipo_autor , $this->tipo_autor);
    $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();
        if( $editou )
        {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("acervo", $this->pessoa_logada, $this->cod_acervo);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);

            #cadastra assuntos para a obra
            $this->gravaAssuntos($this->cod_acervo);
            $this->gravaAutores($this->cod_acervo);
            $this->gravaCategorias($this->cod_acervo);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";

            $this->simpleRedirect('educar_acervo_lst.php');
        }
        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 598, $this->pessoa_logada, 11,  "educar_acervo_lst.php" );


        $obj = new clsPmieducarAcervo($this->cod_acervo, null, null, $this->pessoa_logada, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 0, $this->ref_cod_biblioteca);
        $detalheAntigo = $obj->detalhe();
    $excluiu = $obj->excluir();
        if( $excluiu )
        {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("acervo", $this->pessoa_logada, $this->cod_acervo);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);

            $objCategoria = new clsPmieducarCategoriaAcervo();
            $objCategoria->deletaCategoriaDaObra($this->cod_acervo);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";

            $this->simpleRedirect('educar_acervo_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function gravaAssuntos($cod_acervo){
        $objAssunto = new clsPmieducarAcervoAssunto();
        $objAssunto->deletaAssuntosDaObra($cod_acervo);
        foreach ($this->getRequest()->assuntos as $assuntoId) {
            if (! empty($assuntoId)) {
                $objAssunto = new clsPmieducarAcervoAssunto();
                $objAssunto->cadastraAssuntoParaObra($cod_acervo, $assuntoId);
            }
        }
    }

    function gravaCategorias($cod_acervo){
        $objCategoria = new clsPmieducarCategoriaAcervo();
        $objCategoria->deletaCategoriaDaObra($cod_acervo);
        foreach ($this->getRequest()->categorias as $categoriaId) {
            if (!empty($categoriaId)){
                $objCategoria = new clsPmieducarCategoriaAcervo();
                $objCategoria->cadastraCategoriaParaObra($cod_acervo, $categoriaId);
            }
        }
    }

    function gravaAutores($cod_acervo){
        $objAutor = new clsPmieducarAcervoAcervoAutor();
        $objAutor->deletaAutoresDaObra($cod_acervo);

        $principal = 0;

        foreach ($this->getRequest()->autores as $autorId) {
            if (! empty($autorId)) {
                $principal += 1;
                $objAutor = new clsPmieducarAcervoAcervoAutor();
                $objAutor->cadastraAutorParaObra($cod_acervo, $autorId, $principal);
            }
        }
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

var $autores = $j(document.getElementById('autores[]'));
console.log($autores);
if($j('#ref_cod_tipo_autor').val() == 2 || $j('#ref_cod_tipo_autor').val() == 3){

$j('#tipo_autor').show();
$autores.closest('tr').hide();
$autores.val("");

}else if($j('#ref_cod_tipo_autor').val() == 1){

$j('#tipo_autor').hide();
$j('#tipo_autor').val("");
$autores.closest('tr').show();

}else{
$j('#tipo_autor').hide();
$j('#tipo_autor').val("");
$autores.closest('tr').hide();
$autores.val("");
}
$j('#ref_cod_tipo_autor').click(abriCampo);




function abriCampo(){
if($j('#ref_cod_tipo_autor').val() == 2 || $j('#ref_cod_tipo_autor').val() == 3){

$j('#tipo_autor').show();
$autores.closest('tr').hide();
$autores.val("");

}else if($j('#ref_cod_tipo_autor').val() == 1){

$j('#tipo_autor').hide();
$j('#tipo_autor').val("");
$autores.closest('tr').show();

}else{
$j('#tipo_autor').hide();
$j('#tipo_autor').val("");
$autores.closest('tr').hide();
$autores.val("");
}
}

document.getElementById('ref_cod_acervo_colecao').disabled = true;
document.getElementById('ref_cod_acervo_colecao').options[0].text = 'Selecione uma biblioteca';

document.getElementById('ref_cod_acervo_editora').disabled = true;
document.getElementById('ref_cod_acervo_editora').options[0].text = 'Selecione uma biblioteca';

document.getElementById('ref_cod_acervo_idioma').disabled = true;
document.getElementById('ref_cod_acervo_idioma').options[0].text = 'Selecione uma biblioteca';

var tempExemplarTipo;
var tempColecao;
var tempIdioma;
var tempEditora;

if(document.getElementById('ref_cod_biblioteca').value == "")
{
    setVisibility(document.getElementById('img_colecao'), false);
    setVisibility(document.getElementById('img_editora'), false);
    setVisibility(document.getElementById('img_idioma'), false);
    //tempExemplarTipo = null;
    tempColecao = null;
    tempIdioma = null;
    tempEditora = null;
}
else
{
    ajaxBiblioteca('novo');
}

function getColecao( xml_acervo_colecao )
{
    var campoColecao = document.getElementById('ref_cod_acervo_colecao');
    var DOM_array = xml_acervo_colecao.getElementsByTagName( "acervo_colecao" );

    if(DOM_array.length)
    {
        campoColecao.length = 1;
        campoColecao.options[0].text = 'Selecione uma coleção';
        campoColecao.disabled = false;

        for( var i=0; i<DOM_array.length; i++)
        {
            campoColecao.options[campoColecao.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_colecao"), false, false);
        }
        setVisibility(document.getElementById('img_colecao'), true);
        if(tempColecao != null)
            campoColecao.value = tempColecao;
    }
    else
    {
        if(document.getElementById('ref_cod_biblioteca').value == "")
        {
            campoColecao.options[0].text = 'Selecione uma biblioteca';
            setVisibility(document.getElementById('img_colecao'), false);
        }
        else
        {
            campoColecao.options[0].text = 'A biblioteca não possui coleções';
            setVisibility(document.getElementById('img_colecao'), true);
        }
    }
}

function getEditora( xml_acervo_editora )
{
    var campoEditora = document.getElementById('ref_cod_acervo_editora');
    var DOM_array = xml_acervo_editora.getElementsByTagName( "acervo_editora" );

    if(DOM_array.length)
    {
        campoEditora.length = 1;
        campoEditora.options[0].text = 'Selecione uma editora';
        campoEditora.disabled = false;

        for( var i=0; i<DOM_array.length; i++)
        {
            campoEditora.options[campoEditora.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_editora"), false, false);
        }
        setVisibility(document.getElementById('img_editora'), true);
        if(tempEditora != null)
            campoEditora.value = tempEditora;
    }
    else
    {
        if(document.getElementById('ref_cod_biblioteca').value == "")
        {
            campoEditora.options[0].text = 'Selecione uma biblioteca';
            setVisibility(document.getElementById('img_editora'), false);
        }
        else
        {
            campoEditora.options[0].text = 'A biblioteca não possui editoras';
            setVisibility(document.getElementById('img_editora'), true);
        }
    }
}

function getIdioma( xml_acervo_idioma )
{
    var campoIdioma = document.getElementById('ref_cod_acervo_idioma');
    var DOM_array = xml_acervo_idioma.getElementsByTagName( "acervo_idioma" );

    if(DOM_array.length)
    {
        campoIdioma.length = 1;
        campoIdioma.options[0].text = 'Selecione uma idioma';
        campoIdioma.disabled = false;

        for( var i=0; i<DOM_array.length; i++)
        {
            campoIdioma.options[campoIdioma.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_idioma"), false, false);
        }
        setVisibility(document.getElementById('img_idioma'), true);
        if(tempIdioma != null)
            campoIdioma.value = tempIdioma;
    }
    else
    {
        if(document.getElementById('ref_cod_biblioteca').value == "")
        {
            campoIdioma.options[0].text = 'Selecione uma biblioteca';
            setVisibility(document.getElementById('img_idioma'), false);
        }
        else
        {
            campoIdioma.options[0].text = 'A biblioteca não possui idiomas';
            setVisibility(document.getElementById('img_idioma'), true);
        }
    }
}

document.getElementById('ref_cod_biblioteca').onchange = function()
{
    ajaxBiblioteca();
};

function ajaxBiblioteca(acao)
{
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

    var campoExemplarTipo = document.getElementById('ref_cod_exemplar_tipo');

    var campoColecao = document.getElementById('ref_cod_acervo_colecao');
    if(acao == 'novo')
    {
        tempColecao = campoColecao.value;
    }
    campoColecao.length = 1;
    campoColecao.disabled = true;
    campoColecao.options[0].text = 'Carregando coleções';

    var xml_colecao = new ajax( getColecao );
    xml_colecao.envia( "educar_colecao_xml.php?bib="+campoBiblioteca );

    var campoEditora = document.getElementById('ref_cod_acervo_editora');
    if(acao == 'novo')
    {
        tempEditora = campoEditora.value;
    }
    campoEditora.length = 1;
    campoEditora.disabled = true;
    campoEditora.options[0].text = 'Carregando editoras';

    var xml_editora = new ajax( getEditora );
    xml_editora.envia( "educar_editora_xml.php?bib="+campoBiblioteca );

    var campoIdioma = document.getElementById('ref_cod_acervo_idioma');
    if(acao == 'novo')
    {
        tempIdioma = campoIdioma.value;
    }
    campoIdioma.length = 1;
    campoIdioma.disabled = true;
    campoIdioma.options[0].text = 'Carregando idiomas';

    var xml_idioma = new ajax( getIdioma );
    xml_idioma.envia( "educar_idioma_xml.php?bib="+campoBiblioteca );

}

function pesquisa()
{
    var biblioteca = document.getElementById('ref_cod_biblioteca').value;
    if(!biblioteca)
    {
        alert('Por favor,\nselecione uma biblioteca!');
        return;
    }
    pesquisa_valores_popless('educar_pesquisa_acervo_lst.php?campo1=ref_cod_acervo&ref_cod_biblioteca=' + biblioteca , 'ref_cod_acervo')
}


function fixupPrincipalCheckboxes() {
  $j('#principal').hide();

  var $checkboxes = $j("input[type='checkbox']").filter("input[id^='principal_']");

  $checkboxes.change(function(){
    $checkboxes.not(this).removeAttr('checked');
  });
}

fixupPrincipalCheckboxes();

function fixupAssuntosSize(){

    $j('#assuntos_chzn ul').css('width', '307px');

}

fixupAssuntosSize();

$assuntos = $j('#assuntos');

$assuntos.trigger('chosen:updated');

var handleGetAssuntos = function(dataResponse) {

  $j.each(dataResponse['assuntos'], function(id, value) {

    $assuntos.children("[value=" + value + "]").attr('selected', '');
  });

  $assuntos.trigger('chosen:updated');
}

var getAssuntos = function() {

  var $cod_acervo = $j('#cod_acervo').val();

  if ($j('#cod_acervo').val()!='') {

    var additionalVars = {
      id : $j('#cod_acervo').val(),
    };

    var options = {
      url      : getResourceUrlBuilder.buildUrl('/module/Api/assunto', 'assunto', additionalVars),
      dataType : 'json',
      data     : {},
      success  : handleGetAssuntos,
    };

    getResource(options);
  }
}

getAssuntos();

function fixupCategoriasSize(){
    $j('#categorias_chzn ul').css('width', '307px');
}
fixupCategoriasSize();

$categorias = $j('#categorias');

$categorias.trigger('chosen:updated');

var handleGetCategorias = function(dataResponse) {
  $j.each(dataResponse['categorias'] || [], function(id, value) {
    $categorias.children("[value=" + value + "]").attr('selected', '');
  });

  $categorias.trigger('chosen:updated');
}

var getCategorias = function() {
  var $cod_acervo = $j('#cod_acervo').val();

  if ($j('#cod_acervo').val()!='') {
    var additionalVars = {
      id : $j('#cod_acervo').val(),
    };

    var options = {
      url      : getResourceUrlBuilder.buildUrl('/module/Api/categoria', 'categorias', additionalVars),
      dataType : 'json',
      data     : {},
      success  : handleGetCategorias,
    };

    getResource(options);
  }
}

getCategorias();

var makeSelect2Autores = function(){
  $autores.select2({
    ajax: {
      url: "/module/Api/Autor",
      dataType: 'json',
      delay: 300,
      data: function (params) {
        var query = {
          query: params.term,
          page: params.page,
          oper: 'get',
          resource: 'autor-search'
        }
        return query;
      },
      processResults: function (data) {
        return {
            results: $j.map(data.result, function(value, key){
              return { id: key, text: value  };
            })
        }
      },

    },
    language: "pt-BR",
    multiple: true,
    width: "379px",
    minimumInputLength: 2
  });
}

var handleGetAutores = function(dataResponse) {
  var autores = dataResponse['autores'] || [];

  $autores.attr('multiple', 'true');

  $j.each(autores, function(){
    $autores.append($j("<option/>", { value: this.id, text: this.text, selected: true }));
  });
  makeSelect2Autores();
}

var getAutores = function() {

  var $cod_acervo = $j('#cod_acervo').val();

  if ($j('#cod_acervo').val()!='') {

    var additionalVars = {
      id : $j('#cod_acervo').val(),
    };

    var options = {
      url      : getResourceUrlBuilder.buildUrl('/module/Api/autor', 'autor', additionalVars),
      dataType : 'json',
      data     : {},
      success  : handleGetAutores,
    };

    getResource(options);
  }
}

getAutores();
makeSelect2Autores();

</script>
