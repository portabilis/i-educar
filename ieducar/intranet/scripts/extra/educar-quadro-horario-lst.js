
  var campoInstituicao = document.getElementById('ref_cod_instituicao');
  var campoEscola = document.getElementById('ref_cod_escola');
  var campoCurso = document.getElementById('ref_cod_curso');
  var campoSerie = document.getElementById('ref_cod_serie');
  var campoTurma = document.getElementById('ref_cod_turma');
  var campoAno   = document.getElementById('ano');

  campoInstituicao.onchange = function()
  {
    var campoInstituicao_ = document.getElementById('ref_cod_instituicao').value;

    campoEscola.length = 1;
    campoEscola.disabled = true;
    campoEscola.options[0].text = 'Carregando escola';

    campoCurso.length = 1;
    campoCurso.disabled = true;
    campoCurso.options[0].text = 'Selecione uma escola antes';

    campoSerie.length = 1;
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Selecione um curso antes';

    campoTurma.length = 1;
    campoTurma.disabled = true;
    campoTurma.options[0].text = 'Selecione uma Série antes';

    var xml_escola = new ajax(getEscola);
    xml_escola.envia('educar_escola_xml2.php?ins=' + campoInstituicao_);
  };

  campoEscola.onchange = function()
  {
    var campoEscola_ = document.getElementById( 'ref_cod_escola' ).value;

    campoAno.length = 1;
    campoAno.disabled = true;
    campoAno.options[0].text = 'Selecione uma escola antes';

    campoCurso.length = 1;
    campoCurso.disabled = true;
    campoCurso.options[0].text = 'Carregando curso';

    campoSerie.length = 1;
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Selecione um curso antes';

    campoTurma.length = 1;
    campoTurma.disabled = true;
    campoTurma.options[0].text = 'Selecione uma série antes';

    var xml_curso = new ajax(getCurso);
    xml_curso.envia('educar_curso_xml.php?esc=' + campoEscola_);

    var xml_ano = new ajax(getAnoLetivo);
    xml_ano.envia('educar_escola_ano_letivo_xml.php?esc=' + campoEscola_);
  };

  campoCurso.onchange = function()
  {
    var campoEscola_ = document.getElementById('ref_cod_escola').value;
    var campoCurso_ = document.getElementById('ref_cod_curso').value;

    campoSerie.length = 1;
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Carregando série';

    campoTurma.length = 1;
    campoTurma.disabled = true;
    campoTurma.options[0].text = 'Selecione uma Série antes';

    var xml_serie = ajax(getSerie);
    xml_serie.envia('educar_escola_curso_serie_xml.php?esc=' + campoEscola_ + '&cur=' + campoCurso_);
  };

  campoAno.onchange = function()
  {
    var campoEscola_ = document.getElementById('ref_cod_escola').value;
    var campoCurso_ = document.getElementById('ref_cod_curso').value;

    campoSerie.length = 1;
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Carregando série';

    campoTurma.length = 1;
    campoTurma.disabled = true;
    campoTurma.options[0].text = 'Selecione uma Série antes';

    var xml_serie = ajax(getSerie);
    xml_serie.envia('educar_escola_curso_serie_xml.php?esc=' + campoEscola_ + '&cur=' + campoCurso_);
  };

  campoSerie.onchange = function()
  {
    var campoEscola_ = document.getElementById('ref_cod_escola').value;
    var campoSerie_ = document.getElementById('ref_cod_serie').value;
    var campoAno_ = document.getElementById('ano').value;

    campoTurma.length = 1;
    campoTurma.disabled = true;
    campoTurma.options[0].text = 'Carregando turma';

    var xml_turma = new ajax(getTurma);
    xml_turma.envia('educar_turma_xml.php?esc=' + campoEscola_ + '&ser=' + campoSerie_ + '&ano=' + campoAno_);
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

    if (obj.innerHTML) {
    document.formcadastro.action = 'educar_quadro_horario_horarios_cad.php?ref_cod_turma=' + var1 + '&ref_cod_serie=' + var2 + '&ref_cod_curso=' + var3 + '&ref_cod_escola=' + var4 + '&ref_cod_instituicao=' + var5 + '&ref_cod_quadro_horario=' + var6 + '&dia_semana=' + var7 + '&ano=' + var8 + '&identificador=' + identificador;
    document.formcadastro.submit();
  }
    else {
    document.formcadastro.action = 'educar_quadro_horario_horarios_cad.php?ref_cod_turma=' + var1 + '&ref_cod_serie=' + var2 + '&ref_cod_curso=' + var3 + '&ref_cod_escola=' + var4 + '&ref_cod_instituicao=' + var5 + '&ref_cod_quadro_horario=' + var6 + '&dia_semana=' + var7 + '&ano=' + var8 + '&identificador=' + identificador;
    document.formcadastro.submit();
  }
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

