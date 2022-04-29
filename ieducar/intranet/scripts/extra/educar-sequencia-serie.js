function getCurso(xml_curso) {
  const campoCurso = document.getElementById('ref_curso_origem');
  const campoCurso_ = document.getElementById('ref_curso_destino');
  const DOM_array = xml_curso.getElementsByTagName("curso");

  if(DOM_array.length) {
    campoCurso.length = 1;
    campoCurso.options[0].text = 'Selecione um curso origem';
    campoCurso.disabled = false;

    campoCurso_.length = 1;
    campoCurso_.options[0].text = 'Selecione um curso destino';
    campoCurso_.disabled = false;

    for(let i = 0; i < DOM_array.length; i++ ) {
      campoCurso.options[campoCurso.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
      campoCurso_.options[campoCurso_.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
    }
  } else {
    campoCurso.options[0].text = 'A instituição não possui nenhum curso';
    campoCurso_.options[0].text = 'A instituição não possui nenhum curso';
  }
}

function getSerie(xml_serie) {
  const campoSerie = document.getElementById('ref_serie_origem');
  const DOM_array = xml_serie.getElementsByTagName("serie");

  if(DOM_array.length) {
    campoSerie.length = 1;
    campoSerie.options[0].text = 'Selecione uma série origem';
    campoSerie.disabled = false;

    for(let i = 0; i < DOM_array.length; i++ ) {
      campoSerie.options[campoSerie.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_serie"),false,false);
    }
  } else {
    campoSerie.options[0].text = 'O curso origem não possui nenhuma série';
  }
}

function getSerie_(xml_serie_) {
  const campoSerie_ = document.getElementById('ref_serie_destino');
  const DOM_array = xml_serie_.getElementsByTagName("serie");

  if(DOM_array.length) {
    campoSerie_.length = 1;
    campoSerie_.options[0].text = 'Selecione uma série destino';
    campoSerie_.disabled = false;

    for(let i = 0; i < DOM_array.length; i++ ) {
      campoSerie_.options[campoSerie_.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_serie"),false,false);
    }
  } else {
    campoSerie_.options[0].text = 'O curso origem não possui nenhuma série';
  }
}

document.getElementById('ref_cod_instituicao').onchange = function() {
  const campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  const campoCurso = document.getElementById('ref_curso_origem');

  campoCurso.length = 1;
  campoCurso.disabled = true;
  campoCurso.options[0].text = 'Carregando curso origem';

  const campoCurso_ = document.getElementById('ref_curso_destino');
  campoCurso_.length = 1;
  campoCurso_.disabled = true;
  campoCurso_.options[0].text = 'Carregando curso destino';

  const xml_curso = new ajax(getCurso);
  xml_curso.envia( "educar_curso_xml2.php?ins="+campoInstituicao + '&showDescription=1');
};

document.getElementById('ref_curso_origem').onchange = function() {
  const campoCurso = document.getElementById('ref_curso_origem').value;
  const campoSerie = document.getElementById('ref_serie_origem');

  campoSerie.length = 1;
  campoSerie.disabled = true;
  campoSerie.options[0].text = 'Carregando série origem';

  const xml_serie = new ajax(getSerie);
  xml_serie.envia( "educar_serie_xml.php?cur="+campoCurso + '&showDescription=1')
};

document.getElementById('ref_curso_destino').onchange = function() {
  const campoCurso_ = document.getElementById('ref_curso_destino').value;
  const campoSerie_ = document.getElementById('ref_serie_destino');

  campoSerie_.length = 1;
  campoSerie_.disabled = true;
  campoSerie_.options[0].text = 'Carregando série destino';

  const xml_serie_ = new ajax(getSerie_);
  xml_serie_.envia( "educar_serie_xml.php?cur="+campoCurso_ + '&showDescription=1')
};
