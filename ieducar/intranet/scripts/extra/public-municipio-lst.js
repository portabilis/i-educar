
  document.getElementById('idpais').onchange = function () {
  var campoPais = document.getElementById('idpais').value;

  var campoUf = document.getElementById('iduf');
  campoUf.length = 1;
  campoUf.disabled = true;
  campoUf.options[0].text = 'Carregando estado...';

  var xml_uf = new ajax(getUf);
  xml_uf.envia("public_uf_xml.php?pais=" + campoPais);
}

  function getUf (xml_uf) {
  var campoUf = document.getElementById('iduf');
  var DOM_array = xml_uf.getElementsByTagName("estado");

  if (DOM_array.length) {
  campoUf.length = 1;
  campoUf.options[0].text = 'Selecione um estado';
  campoUf.disabled = false;

  for (var i = 0; i < DOM_array.length; i++) {
  campoUf.options[campoUf.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("id"), false, false);
}
} else
  campoUf.options[0].text = 'O pais não possui nenhum estado';
}

