var scriptValida = "";
var retorno = 1;
var divClientes = document.getElementById("tr_div_clientes");
setVisibility('tr_div_clientes', false);

function getClienteTipo() {
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
  var exemplarTipoId = document.getElementById('cod_exemplar_tipo').value;

  var xml1 = new ajax(getClienteTipo_XML);

  strURL = "educar_cliente_tipo_xml.php?bib=" + campoBiblioteca + "&exemplar_tipo_id=" + exemplarTipoId;
  xml1.envia(strURL);
}

function getClienteTipo_XML(xml) {
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
  var clientes = document.getElementById('clientes');
  var nm_tipo_cliente = document.createElement("input");
  var span = document.createElement("span");
  var dias_tipo_cliente = document.createElement("input");
  var br = document.createElement("br");
  var tipos = false;

  clientes.innerHTML = "";
  scriptValida = "";

  var tipo_cliente = xml.getElementsByTagName("cliente_tipo");

  var aux = clientes.innerHTML;

  if (tipo_cliente.length)
    setVisibility('tr_div_clientes', true);

  for (var j = 0; j < tipo_cliente.length; j++) {
    tipos = true;
    clientes.appendChild(nm_tipo_cliente);
    clientes.appendChild(span);
    clientes.appendChild(dias_tipo_cliente);
    clientes.appendChild(br);
    span.innerHTML = "Dias de Empréstimo";
    span.setAttribute("class", "dias");
    nm_tipo_cliente.setAttribute("id", "teste" + j);
    nm_tipo_cliente.setAttribute('type', 'text');
    nm_tipo_cliente.setAttribute('disabled', 'true');
    nm_tipo_cliente.setAttribute('class', 'obrigatorio');
    nm_tipo_cliente.setAttribute('style', 'margin: 2px;');
    nm_tipo_cliente.setAttribute('value', tipo_cliente[j].firstChild.data);

    dias_tipo_cliente.setAttribute("id", "tipo_" + tipo_cliente[j].getAttribute("cod_cliente_tipo"));
    dias_tipo_cliente.setAttribute('type', 'text');
    dias_tipo_cliente.setAttribute('size', '3');
    dias_tipo_cliente.setAttribute('autocomplete', 'off');
    dias_tipo_cliente.setAttribute('style', 'margin: 2px;');
    dias_tipo_cliente.setAttribute('maxlength', '3');
    if (tipo_cliente[j].getAttribute("dias_emprestimo"))
      dias_tipo_cliente.setAttribute('value', tipo_cliente[j].getAttribute("dias_emprestimo"));
    dias_tipo_cliente.setAttribute('class', 'obrigatorio');

    clientes.innerHTML += aux;

    scriptValida += "if (!(/[^ ]/.test( document.getElementById('tipo_" + tipo_cliente[j].getAttribute("cod_cliente_tipo") + "').value )) || !((/^[0-9]+$/).test( document.getElementById('tipo_" + tipo_cliente[j].getAttribute("cod_cliente_tipo") + "').value )))\n";
    scriptValida += "{\n";
    scriptValida += "retorno = 0;\n";
    scriptValida += "mudaClassName( 'formdestaque', 'formlttd' );\n";
    scriptValida += "document.getElementById('tipo_" + tipo_cliente[j].getAttribute("cod_cliente_tipo") + "').className = \"formdestaque\";\n";
    scriptValida += "alert( 'Preencha o campo \"" + tipo_cliente[j].firstChild.data + "\" corretamente!' );\n";
    scriptValida += "document.getElementById('tipo_" + tipo_cliente[j].getAttribute("cod_cliente_tipo") + "').focus();\n";
    scriptValida += "}\n\n";
    document.getElementById("tipo_" + tipo_cliente[j].getAttribute("cod_cliente_tipo")).name = dias_tipo_cliente.id;
  }
  if (!tipos) {
    setVisibility('tr_div_clientes', false);

  }
}

function Valida() {
  eval(scriptValida);
  if (retorno == 0) {
    retorno = 1;
    return false;
  }
  acao();
}

if (document.getElementById('ref_cod_biblioteca').type == 'hidden') {
  getClienteTipo();

} else {
  document.getElementById('ref_cod_biblioteca').onchange = function () {
    getClienteTipo();
  }

}

if (editar_) {
  getClienteTipo();
}
