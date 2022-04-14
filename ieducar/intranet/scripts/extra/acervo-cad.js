
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
};

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


