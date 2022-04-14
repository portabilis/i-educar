document.getElementById('login_').setAttribute('autocomplete', 'off');
document.getElementById('login_').onkeypress = function (e) {
  //IE bug
  if (!e)
    e = window.event;
  if (e.keyCode == 13)
    document.getElementById('senha_').focus();
}

document.getElementById('senha_').onkeypress = function (e) {
  //IE bug
  if (!e)
    e = window.event;
  if (e.keyCode == 13)
    document.getElementById('btn_enviar').focus();
  else if (e.keyCode == 8)
    document.getElementById('senha_').value = null;
}

function pesquisa_cliente() {
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
  pesquisa_valores_popless('educar_pesquisa_cliente_lst.php?campo1=ref_cod_cliente&campo2=nm_cliente1&ref_cod_biblioteca=' + campoBiblioteca)
}

setVisibility('tr_login_', false);
setVisibility('tr_senha_', false);
setVisibility('tr_nm_cliente', false);

function requisitaSenha(xml_tipo_regime) {
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

  var DOM_array = xml_tipo_regime.getElementsByTagName("biblioteca");

  if (campoBiblioteca == '') {
    setVisibility('tr_login_', false);
    setVisibility('tr_senha_', false);
    setVisibility('tr_nm_cliente', false);
  } else {
    for (var i = 0; i < DOM_array.length; i++) {
      if (DOM_array[i].getAttribute("requisita_senha") == 0) {
        setVisibility('tr_login_', false);
        setVisibility('tr_senha_', false);
        setVisibility('tr_nm_cliente', true);
      } else if (DOM_array[i].getAttribute("requisita_senha") == 1) {
        setVisibility('tr_nm_cliente', false);
        setVisibility('tr_login_', true);
        setVisibility('tr_senha_', true);
      }
    }

  }

}

document.getElementById('ref_cod_biblioteca').onchange = function () {
//  requisitaSenha();
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

  var xml_biblioteca = new ajax(requisitaSenha);
  xml_biblioteca.envia("educar_biblioteca_xml.php?bib=" + campoBiblioteca);
}
