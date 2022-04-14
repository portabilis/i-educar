document.getElementById('ref_cod_biblioteca').onchange = function () {
  ajaxBiblioteca();
};

if (document.getElementById('ref_cod_biblioteca').value != '') {
  ajaxBiblioteca();
}

function ajaxBiblioteca() {
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
  var xml_biblioteca = new ajax(requisitaSenha);
  xml_biblioteca.envia("educar_biblioteca_xml.php?bib=" + campoBiblioteca);
}

setVisibility('tr_login_', false);
setVisibility('tr_senha_', false);

function requisitaSenha(xml) {
  var DOM_array = xml.getElementsByTagName("biblioteca");
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

  if (campoBiblioteca == '') {
    setVisibility('tr_login_', false);
    setVisibility('tr_senha_', false);
  } else {
    for (var i = 0; i < DOM_array.length; i++) {
      if (DOM_array[i].getAttribute("requisita_senha") == 0) {
        setVisibility('tr_login_', false);
        setVisibility('tr_senha_', false);
        document.getElementById('login_').setAttribute('class', 'geral');
        document.getElementById('senha_').setAttribute('class', 'geral');
        document.getElementById('requisita_senha').value = '0';
      } else if (DOM_array[i].getAttribute("requisita_senha") == 1) {
        setVisibility('tr_login_', true);
        setVisibility('tr_senha_', true);
        document.getElementById('login_').setAttribute('class', 'obrigatorio');
        document.getElementById('senha_').setAttribute('class', 'obrigatorio');
        document.getElementById('requisita_senha').value = '1';
      }
    }

  }
}
