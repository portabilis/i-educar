  const campoInstituicao = document.getElementById('ref_cod_instituicao');
  const campoEscola = document.getElementById('ref_cod_escola');
  const campoCurso = document.getElementById('ref_cod_curso');
  const campoSerie = document.getElementById('ref_cod_serie');
  const campoTurma = document.getElementById('ref_cod_turma');
  const campoAno = document.getElementById('ano');

  $j(function () {
    if (campoInstituicao.length === 2) {
    $j('#ref_cod_instituicao option:eq(1)').prop('selected', true).change();
    }
  });

  campoInstituicao.onchange = function()
  {
    setAttributes(campoEscola,'Carregando escola');
    setAttributes(campoCurso,'Selecione uma escola antes');
    setAttributes(campoSerie,'Selecione um curso antes');
    setAttributes(campoTurma,'Selecione uma Série antes');
    getApiResource("/api/resource/school",getEscola,{institution:campoInstituicao.value});
  };

  campoEscola.onchange = function()
  {
    setAttributes(campoAno,'Selecione uma escola antes');
    setAttributes(campoCurso,'Carregando curso');
    setAttributes(campoSerie,'Selecione um curso antes');
    setAttributes(campoTurma,'Selecione uma série antes');
    getApiResource("/api/resource/course",getCurso,{school:$j(this).val()});
    getApiResource("/api/resource/school-academic-year",getAnoLetivo,{school:campoEscola.value});
  };

  campoCurso.onchange = function()
  {
    setAttributes(campoSerie,'Carregando série');
    setAttributes(campoSerie,'Selecione uma Série antes');
    getApiResource("/api/resource/grade",getSerie,{school:campoEscola.value,course:campoCurso.value});
  };

  campoAno.onchange = function()
  {
    setAttributes(campoSerie,'Carregando série');
    setAttributes(campoTurma,'Selecione uma Série antes');
    getApiResource("/api/resource/course", getCurso,{school:campoEscola.value, year_eq:campoAno.value});
    getApiResource("/api/resource/grade",getSerie,{school:campoEscola.value,course:campoCurso.value});
  };

  campoSerie.onchange = function()
  {
    setAttributes(campoTurma,'Carregando turma');
    getApiResource("/api/resource/school-class",getTurma,{school:campoEscola.value,grade:campoSerie.value,in_progress_year:campoAno.value});
  };

  if (document.getElementById('botao_busca')) {
  obj_botao_busca = document.getElementById('botao_busca');
  obj_botao_busca.onclick = function()
{
  document.formcadastro.action = 'educar_quadro_horario_lst.php?busca=S';
  acao();
};
}

  function envia(obj, var1, var2, var3, var4, var5, var6, var7, var8)
  {
    var identificador = Math.round(1000000000 * Math.random());

    document.formcadastro.action = 'educar_quadro_horario_horarios_cad.php?ref_cod_turma=' + var1 + '&ref_cod_serie=' + var2 + '&ref_cod_curso=' + var3 + '&ref_cod_escola=' + var4 + '&ref_cod_instituicao=' + var5 + '&ref_cod_quadro_horario=' + var6 + '&dia_semana=' + var7 + '&ano=' + var8 + '&identificador=' + identificador;
    document.formcadastro.submit();
  }

  if (document.createStyleSheet) {
  document.createStyleSheet('styles/calendario.css');
}
  else {
  var objHead = document.getElementsByTagName('head');
  var objCSS = objHead[0].appendChild(document.createElement('link'));
  objCSS.rel = 'stylesheet';
  objCSS.href = 'styles/calendario.css';
  objCSS.type = 'text/css';
}

