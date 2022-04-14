document.getElementById('ref_cod_biblioteca').onchange = function () {
  var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

  var xml_situacao = new ajax(getSituacao);
  xml_situacao.envia("educar_situacao_xml.php?bib=" + campoBiblioteca);
}

function getSituacao(xml_situacao) {
  setVisibility('tr_situacao_padrao', true);
  setVisibility('tr_situacao_emprestada', true);

  var DOM_array = xml_situacao.getElementsByTagName("situacao");

  if (DOM_array.length) {
    for (var i = 0; i < DOM_array.length; i++) {
      if (DOM_array[i].getAttribute("situacao_padrao") == 1) //jah existe uma situacao padrao
        setVisibility('tr_situacao_padrao', false);

      if (DOM_array[i].getAttribute("situacao_emprestada") == 1) //jah existe uma situacao emprestada
        setVisibility('tr_situacao_emprestada', false);
    }

  }

}

function valida() {
  var campoPadrao = document.getElementById('situacao_padrao').checked;
  var campoEmprestada = document.getElementById('situacao_emprestada').checked;

  if (campoPadrao == true && campoEmprestada == true) {
    alert("Não é permitido setar ao mesmo tempo os campos \n 'Situação Padrão' e 'Situação Emprestada'!");
    return false;
  }

  if (!acao())
    return;
  document.forms[0].submit();
}
