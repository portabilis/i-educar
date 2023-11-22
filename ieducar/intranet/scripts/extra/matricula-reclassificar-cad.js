function getSerie() {
  const campoCurso = document.getElementById('ref_cod_curso').value;
  const campoSerie = document.getElementById('serie_matricula').value;
  getApiResource("/api/resource/grade", getSeries, {course: campoCurso, grade_exclude: campoSerie});
}

function getSeries(series) {
  const campoSerie = document.getElementById('ref_ref_cod_serie');
  campoSerie.length = 1;

  $j.each(series, function (i, item) {
    campoSerie[campoSerie.length] = new Option(item.name, item.id, false, false);
  });
}

getSerie();

function confirm() {
  if (!acao()) {
    return false;
  }
  makeDialog({
    content: 'Caso exista compatibilidade de regras e etapas entre as séries de origem e destino da reclassificação, os lançamentos serão migrados automaticamente. Deseja prosseguir com a reclassificação?',
    title: 'Atenção!',
    maxWidth: 600,
    width: 600,
    close: function () {
      $j('#dialog-container').dialog('destroy');
    },
    buttons: [{
      text: 'Reclassificar',
      click: function () {
        $j('#formcadastro').removeAttr('onsubmit');
        $j('#formcadastro').submit();
        $j('#dialog-container').dialog('destroy');
      }
    }, {
      text: 'Cancelar',
      click: function () {
        $j('#dialog-container').dialog('destroy');
      }
    }]
  });
}
