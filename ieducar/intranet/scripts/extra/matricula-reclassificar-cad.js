function getSerie() {
  const campoCurso = document.getElementById('ref_cod_curso').value;
  const campoSerie = document.getElementById('serie_matricula').value;
  const xml1 = new ajax(getSerie_XML);
  let strURL = "educar_sequencia_serie_curso_xml.php?cur=" + campoCurso + "&ser_dif=" + campoSerie;
  xml1.envia(strURL);

}

function getSerie_XML(xml) {
  const seq_serie = xml.getElementsByTagName("serie");
  let campoSerie = document.getElementById('ref_ref_cod_serie');
  campoSerie.length = 1;

  for (let ct = 0; ct < seq_serie.length; ct++) {
    campoSerie[campoSerie.length] = new Option(seq_serie[ct].firstChild.nodeValue, seq_serie[ct].getAttribute("cod_serie"), false, false);
  }
}

getSerie();
