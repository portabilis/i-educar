function getSerie() {
  const campoCurso = document.getElementById('ref_cod_curso').value;
  const campoSerie = document.getElementById('serie_matricula').value;
  getApiResource("/api/resource/grade",getSeries,{course:campoCurso,grade_exclude:campoSerie});
}

function getSeries(series) {
  const campoSerie = document.getElementById('ref_ref_cod_serie');
  campoSerie.length = 1;

  $j.each(series, function(i, item) {
    campoSerie[campoSerie.length] = new Option(item.name, item.id, false, false);
  });
}

getSerie();
