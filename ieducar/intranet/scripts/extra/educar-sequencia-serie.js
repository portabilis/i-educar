function getCurso(cursos) {
  const campoCurso = document.getElementById('ref_curso_origem');
  const campoCurso_ = document.getElementById('ref_curso_destino');

  if(cursos.length) {
    setAttributes(campoCurso,'Selecione um curso origem',false);
    setAttributes(campoCurso_,'Selecione um curso destino',false);

    $j.each(cursos, function(i, item) {
      campoCurso.options[campoCurso.options.length] = new Option(item.name,item.id,false,false);
      campoCurso_.options[campoCurso_.options.length] = new Option(item.name,item.id,false,false);
    });
  } else {
    campoCurso.options[0].text = 'A instituição não possui nenhum curso';
    campoCurso_.options[0].text = 'A instituição não possui nenhum curso';
  }
}

function getSerie(series) {
  const campoSerie = document.getElementById('ref_serie_origem');

  if(series.length) {
    setAttributes(campoSerie,'Selecione uma série origem',false);
    $j.each(series, function(i, item) {
      campoSerie.options[campoSerie.options.length] = new Option(item.name,item.id,false,false);
    });
  } else {
    campoSerie.options[0].text = 'O curso origem não possui nenhuma série';
  }
}

function getSerie_(series) {
  const campoSerie_ = document.getElementById('ref_serie_destino');

  if(series.length) {
    setAttributes(campoSerie_,'Selecione uma série destino',false);

    $j.each(series, function(i, item) {
      campoSerie_.options[campoSerie_.options.length] = new Option(item.name,item.id,false,false);
    });
  } else {
    campoSerie_.options[0].text = 'O curso origem não possui nenhuma série';
  }
}

document.getElementById('ref_cod_instituicao').onchange = function() {
  const campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  const campoCurso = document.getElementById('ref_curso_origem');
  setAttributes(campoCurso,'Carregando curso origem',true);
  const campoCurso_ = document.getElementById('ref_curso_destino');
  setAttributes(campoCurso_,'Carregando curso destino',true);
  getApiResource('/api/resource/course',getCurso,{institution:campoInstituicao})
};

document.getElementById('ref_curso_origem').onchange = function() {
  const campoCurso = document.getElementById('ref_curso_origem').value;
  const campoSerie = document.getElementById('ref_serie_origem');
  setAttributes(campoSerie,'Carregando série origem',true);

  getApiResource('/api/resource/grade',getSerie,{course:campoCurso})
};

document.getElementById('ref_curso_destino').onchange = function() {
  const campoCurso_ = document.getElementById('ref_curso_destino').value;
  const campoSerie_ = document.getElementById('ref_serie_destino');
  setAttributes(campoSerie_,'Carregando série destino',true);
  getApiResource('/api/resource/grade',getSerie_,{course:campoCurso_})
};
