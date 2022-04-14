var instituicaoId = document.getElementById('ref_cod_instituicao').value;
if (instituicaoId != '') {
  var selectRelatorio = document.getElementById('relatorio');
  selectRelatorio.length = 1;
  getDocumento(instituicaoId);
}

document.getElementById('btn_enviar').style.display = 'none';

document.getElementById('ref_cod_instituicao').onchange = function () {
  var selectRelatorio = document.getElementById('relatorio');
  if (this.selectedIndex !== 0) {
    selectRelatorio.length = 1;
    selectRelatorio.disabled = true;
    selectRelatorio.options[0].text = 'Carregando Relatorios';
    var instituicaoId = document.getElementById('ref_cod_instituicao').value;
    getDocumento(instituicaoId);
  } else {
    selectRelatorio.length = 1;
    selectRelatorio.options[0].text = 'Selecione';
  }
};

document.getElementById('relatorio').onchange = function () {
  if (this.selectedIndex !== 0) {
    window.open(linkUrlPrivada(this.value), '_blank');
  }
};

function getDocumento(instituicaoId) {
  var searchPath = '../module/Api/InstituicaoDocumentacao?oper=get&resource=getDocuments';
  var params = { instituicao_id: instituicaoId };
  var id = '';
  var titulo = '';
  var url = '';

  $j.get(searchPath, params, function (data) {

    var documentos = data.documentos;

    for (var i = 0; i < documentos.length; i++) {
      var selectRelatorio = document.getElementById('relatorio');
      var option = document.createElement('option');
      selectRelatorio.options[0].text = 'Selecione um relatório';
      selectRelatorio.disabled = false;
      option.text = documentos[i].titulo_documento;
      option.value = documentos[i].url_documento;
      selectRelatorio.add(option);
    }
  });
}
