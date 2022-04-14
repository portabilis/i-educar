function acao2() {
  if (!acao())
    return;
  if (document.getElementById('requisita_senha').value == 1) {
    if (document.getElementById("login_").value != '' && !((/^[0-9]+$/).test(document.getElementById("login_").value))) {
      mudaClassName('formdestaque', 'obrigatorio');
      document.getElementById("login_").className = "formdestaque";
      alert('Preencha o campo \'Login\' corretamente!');
      document.getElementById("login_").focus();
      return false;
    }

    if (!(/[^ ]/.test(document.getElementById("senha_").value))) {
      mudaClassName('formdestaque', 'obrigatorio');
      document.getElementById("senha_").className = "formdestaque";
      alert('Preencha o campo \'Senha\' corretamente!');
      document.getElementById("senha_").focus();
      return false;
    }
  }

  if (document.getElementById('btn_enviar')) {
    document.getElementById('btn_enviar').disabled = true;
  }

  document.formcadastro.submit();

}

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

function requisitaSenha(xml) {
  var DOM_array = xml.getElementsByTagName("biblioteca");
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

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
        document.getElementById('requisita_senha').value = '0';
      } else if (DOM_array[i].getAttribute("requisita_senha") == 1) {
        setVisibility('tr_nm_cliente', false);
        setVisibility('tr_login_', true);
        setVisibility('tr_senha_', true);
        document.getElementById('requisita_senha').value = '1';
      }
    }

  }
}

document.getElementById('ref_cod_biblioteca').onchange = function () {
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
  var xml_biblioteca = new ajax(requisitaSenha);
  xml_biblioteca.envia("educar_biblioteca_xml.php?bib=" + campoBiblioteca);

}


