var scriptValida = "";
var retorno = 1;
var divExemplares = document.getElementById( "tr_div_exemplares" );
setVisibility ('tr_div_exemplares', false);

function getExemplarTipo()
{
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
    var campoClienteTipo = document.getElementById('cod_cliente_tipo').value;
    var xml1 = new ajax(getExemplarTipo_XML);
    strURL = "educar_exemplar_tipo_xml.php?bib="+campoBiblioteca+"&cod_tipo_cliente="+campoClienteTipo;
//  strURL = "educar_exemplar_tipo_xml.php?bib="+campoBiblioteca;
    xml1.envia(strURL);
}

function getExemplarTipo_XML(xml) {
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
  var exemplares = document.getElementById('exemplares');
  var nm_tipo_exemplar = document.createElement("input");
  var span = document.createElement("span");
  var dias_tipo_exemplar = document.createElement("input");
  var br = document.createElement("br");
  var tipos = false;

  exemplares.innerHTML = "";
  scriptValida = "";

  var tipo_exemplar = xml.getElementsByTagName("exemplar_tipo");

  var aux = exemplares.innerHTML;

  if (tipo_exemplar.length)
    setVisibility('tr_div_exemplares', true);

  for (var j = 0; j < tipo_exemplar.length; j++) {
    //if (tipo_exemplar[j][2] == campoBiblioteca)
    //{
    tipos = true;
    exemplares.appendChild(nm_tipo_exemplar);
    exemplares.appendChild(span);
    exemplares.appendChild(dias_tipo_exemplar);
    exemplares.appendChild(br);
    span.innerHTML = "Dias de Empréstimo";
    span.setAttribute("class", "dias");
    nm_tipo_exemplar.setAttribute("id", "teste" + j);
    nm_tipo_exemplar.setAttribute('type', 'text');
    nm_tipo_exemplar.setAttribute('disabled', 'true');
    nm_tipo_exemplar.setAttribute('class', 'obrigatorio');
    nm_tipo_exemplar.setAttribute('style', 'margin: 2px;');
    nm_tipo_exemplar.setAttribute('value', tipo_exemplar[j].firstChild.nodeValue);
    dias_tipo_exemplar.setAttribute("id", "tipo_" + tipo_exemplar[j].getAttribute("cod_exemplar_tipo"));
    dias_tipo_exemplar.setAttribute('type', 'text');
    dias_tipo_exemplar.setAttribute('size', '3');
    dias_tipo_exemplar.setAttribute('autocomplete', 'off');
    dias_tipo_exemplar.setAttribute('style', 'margin: 2px;');
    dias_tipo_exemplar.setAttribute('maxlength', '3');
    if (tipo_exemplar[j].getAttribute("dias_emprestimo"))
      dias_tipo_exemplar.setAttribute('value', tipo_exemplar[j].getAttribute("dias_emprestimo"));
    else
      dias_tipo_exemplar.setAttribute('value', '');

    dias_tipo_exemplar.setAttribute('class', 'obrigatorio');

    exemplares.innerHTML += aux;

    scriptValida += "if (!(/[^ ]/.test( document.getElementById('tipo_" + tipo_exemplar[j].getAttribute("cod_exemplar_tipo") + "').value )) || !((/^[0-9]+$/).test( document.getElementById('tipo_" + tipo_exemplar[j].getAttribute("cod_exemplar_tipo") + "').value )))\n";
    scriptValida += "{\n";
    scriptValida += "retorno = 0;\n";
    scriptValida += "mudaClassName( 'formdestaque', 'formlttd' );\n";
    scriptValida += "document.getElementById('tipo_" + tipo_exemplar[j].getAttribute("cod_exemplar_tipo") + "').className = \"formdestaque\";\n";
    scriptValida += "alert( 'Preencha o campo \"" + tipo_exemplar[j].firstChild.nodeValue + "\" corretamente!' );\n";
    scriptValida += "document.getElementById('tipo_" + tipo_exemplar[j].getAttribute("cod_exemplar_tipo") + "').focus();\n";
    //scriptValida +=   "return retorno;\n";
    scriptValida += "}\n\n";
    document.getElementById("tipo_" + tipo_exemplar[j].getAttribute("cod_exemplar_tipo")).name = dias_tipo_exemplar.id;
    //}
  }

  if (!tipos) {
    setVisibility('tr_div_exemplares', false);

  }
}


function Valida()
{
    eval(scriptValida);
    if (retorno == 0)
    {
        retorno = 1;
        return false;
    }
    acao();
}

if(document.getElementById('ref_cod_biblioteca').type == 'hidden')
{
    getExemplarTipo();

}
else
{
    document.getElementById('ref_cod_biblioteca').onchange = function()
    {
        getExemplarTipo();
    }

}

if(editar_)
{
    getExemplarTipo();
}
