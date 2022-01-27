var modulosDisponiveis = #modulos;

function getComodo() {
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoComodo = document.getElementById('ref_cod_infra_predio_comodo');
  campoComodo.disabled = true;

  campoComodo.length = 1;
  campoComodo.options[0] = new Option('Selecione uma sala', '', false, false);

  var xml1 = new ajax(atualizaTurmaCad_TipoComodo);
  strURL = 'educar_escola_comodo_xml.php?esc=' + campoEscola;
  xml1.envia(strURL);
}

function atualizaTurmaCad_TipoComodo(xml) {
  var campoComodo = document.getElementById('ref_cod_infra_predio_comodo');
  campoComodo.disabled = false;

  var tipo_comodo = xml.getElementsByTagName('item');

  if (tipo_comodo.length) {
    for (var i = 0; i < tipo_comodo.length; i += 2) {
      campoComodo.options[campoComodo.options.length] = new Option(
        tipo_comodo[i + 1].firstChild.data, tipo_comodo[i].firstChild.data, false, false,
      );
    }
  } else {
    campoComodo.length = 1;
    campoComodo.options[0] = new Option('A escola n\u00e3o possui nenhuma sala', '', false, false);
  }
}

function getTipoTurma() {
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoTipoTurma = document.getElementById('ref_cod_turma_tipo');
  campoTipoTurma.disabled = true;

  campoTipoTurma.length = 1;
  campoTipoTurma.options[0] = new Option('Selecione um tipo de turma', '', false, false);

  var xml1 = new ajax(atualizaTurmaCad_TipoTurma);
  strURL = 'educar_tipo_turma_xml.php?ins=' + campoInstituicao;
  xml1.envia(strURL);
}

function atualizaTurmaCad_TipoTurma(xml) {
  var tipo_turma = xml.getElementsByTagName('item');
  var campoTipoTurma = document.getElementById('ref_cod_turma_tipo');
  campoTipoTurma.disabled = false;

  if (tipo_turma.length) {
    for (var i = 0; i < tipo_turma.length; i += 2) {
      campoTipoTurma.options[campoTipoTurma.options.length] = new Option(
        tipo_turma[i + 1].firstChild.data, tipo_turma[i].firstChild.data, false, false,
      );
    }
  } else {
    campoTipoTurma.length = 1;
    campoTipoTurma.options[0] = new Option(
      'A institui\u00e7\u00e3o n\u00e3o possui nenhum tipo de turma', '', false, false,
    );
  }
}

function getModulo() {
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoEscola = document.getElementById('ref_cod_instituicao').value;
  var campoModulo = document.getElementById('ref_cod_modulo');

  var url = 'educar_modulo_instituicao_xml.php';
  var pars = '?inst=' + campoInstituicao;

  var xml1 = new ajax(getModulo_xml);
  strURL = url + pars;
  xml1.envia(strURL);
}

function getModulo_xml(xml) {
  var campoModulo = document.getElementById('ref_cod_modulo');
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

  campoModulo.length = 1;
  campoModulo.options[0] = new Option('Selecione um m\u00f3dulo', '', false, false);

  var DOM_modulos = xml.getElementsByTagName('item');

  for (var j = 0; j < DOM_modulos.length; j += 2) {
    campoModulo.options[campoModulo.options.length] = new Option(
      DOM_modulos[j + 1].firstChild.nodeValue, DOM_modulos[j].firstChild.nodeValue,
      false, false,
    );
  }

  if (campoModulo.length == 1 && campoInstituicao != '') {
    campoModulo.options[0] = new Option(
      'A institui\u00e7\u00e3o n\u00e3o possui nenhum m\u00f3dulo', '', false, false,
    );
  }
}

var evtOnLoad = function () {
  setVisibility('tr_hora_inicial', false);
  setVisibility('tr_hora_final', false);
  setVisibility('tr_hora_inicio_intervalo', false);
  setVisibility('tr_hora_fim_intervalo', false);

  // HIDE quebra de linha
  var hr_tag = document.getElementsByTagName('hr');

  for (var ct = 0; ct < hr_tag.length; ct++) {
    setVisibility(hr_tag[ct].parentNode.parentNode, false);
  }

  setVisibility('tr_hora_inicial', true);
  setVisibility('tr_hora_final', true);
  setVisibility('tr_hora_inicio_intervalo', true);
  setVisibility('tr_hora_fim_intervalo', true);
  if (document.getElementById('padrao_ano_escolar').value == '') {
    setModuleAndPhasesVisibility(false);
  } else if (document.getElementById('padrao_ano_escolar').value == 0) {
    setModuleAndPhasesVisibility(true);

    var hr_tag = document.getElementsByTagName('hr');
    for (var ct = 0; ct < hr_tag.length; ct++) {
      setVisibility(hr_tag[ct].parentNode.parentNode, true);
    }
  } else {
    setModuleAndPhasesVisibility(false);
  }
};

if (window.addEventListener) {
  // Mozilla
  window.addEventListener('load', evtOnLoad, false);
} else if (window.attachEvent) {
  // IE
  window.attachEvent('onload', evtOnLoad);
}

document.getElementById('ref_cod_curso').onchange = function () {
  getEscolaCursoSerie();

  PadraoAnoEscolar_xml();
};

function PadraoAnoEscolar_xml() {
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var xml1 = new ajax(PadraoAnoEscolar);
  strURL = 'educar_curso_xml.php?ins=' + campoInstituicao;
  xml1.envia(strURL);
}

document.getElementById('ref_cod_serie').onchange = function () {
  if (this.value) {
    codEscola = document.getElementById('ref_cod_escola').value;
    getHoraEscolaSerie();
  }
};

function PadraoAnoEscolar(xml) {
  var escola_curso_ = new Array();

  if (xml != null) {
    escola_curso_ = xml.getElementsByTagName('curso');
  }

  campoCurso = document.getElementById('ref_cod_curso').value;

  for (var j = 0; j < escola_curso_.length; j++) {
    if (escola_curso_[j].getAttribute('cod_curso') == campoCurso) {
      document.getElementById('padrao_ano_escolar').value =
        escola_curso_[j].getAttribute('padrao_ano_escolar');
    }
  }

  setModuleAndPhasesVisibility(false);

  setVisibility('tr_hora_inicial', true);
  setVisibility('tr_hora_final', true);
  setVisibility('tr_hora_inicio_intervalo', true);
  setVisibility('tr_hora_fim_intervalo', true);

  if (campoCurso == '') {
    return;
  }

  var campoCurso = document.getElementById('ref_cod_curso').value;

  if (document.getElementById('padrao_ano_escolar').value == 0) {
    setModuleAndPhasesVisibility(true);
    buscaEtapasDaEscola();
  }
}

function setModuleAndPhasesVisibility(show) {
  setVisibility('tr_etapas_cabecalho', show);
  setVisibility('tr_ref_cod_modulo', show);
  setVisibility('tr_turma_modulo', show);
}

function getHoraEscolaSerie() {
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoSerie = document.getElementById('ref_cod_serie').value;

  var xml1 = new ajax(atualizaTurmaCad_EscolaSerie);
  strURL = 'educar_escola_serie_hora_xml.php?esc=' + campoEscola + '&ser=' + campoSerie;
  xml1.envia(strURL);
}

function atualizaTurmaCad_EscolaSerie(xml) {
  var campoHoraInicial = document.getElementById('hora_inicial');
  var campoHoraFinal = document.getElementById('hora_final');
  var campoHoraInicioIntervalo = document.getElementById('hora_inicio_intervalo');
  var campoHoraFimIntervalo = document.getElementById('hora_fim_intervalo');

  var DOM_escola_serie_hora = xml.getElementsByTagName('item');

  if (DOM_escola_serie_hora.length) {
    horaInicial = (DOM_escola_serie_hora[0].firstChild || {}).data;
    horaFinal = (DOM_escola_serie_hora[1].firstChild || {}).data;
    horaInicioIntervalo = (DOM_escola_serie_hora[2].firstChild || {}).data;
    horaFimIntervalo = (DOM_escola_serie_hora[3].firstChild || {}).data;
    campoHoraInicial.value = typeof (horaInicial) != 'undefined' ? horaInicial : null;
    campoHoraFinal.value = typeof (horaFinal) != 'undefined' ? horaFinal : null;
    campoHoraInicioIntervalo.value = typeof (horaInicioIntervalo) != 'undefined' ? horaInicioIntervalo : null;
    campoHoraFimIntervalo.value = typeof (horaFimIntervalo) != 'undefined' ? horaFimIntervalo : null;
  }
}

function valida() {
  if (validaHorarioInicialFinal() && validaHoras() && validaAtividadesComplementares()) {
    if (document.getElementById('padrao_ano_escolar').value == 1) {
      var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
      var campoEscola = document.getElementById('ref_cod_escola').value;
      var campoTurma = document.getElementById('cod_turma').value;
      var campoComodo = document.getElementById('ref_cod_infra_predio_comodo').value;
      var campoCurso = document.getElementById('ref_cod_curso').value;
      var campoSerie = document.getElementById('ref_cod_serie').value;

      var url = 'educar_turma_sala_xml.php';
      var pars = '?inst=' + campoInstituicao + '&esc=' + campoEscola + '&not_tur=' +
        campoTurma + '&com=' + campoComodo + '&cur=' + campoCurso + '&ser=' + campoSerie;

      var xml1 = new ajax(valida_xml);
      strURL = url + pars;

      xml1.envia(strURL);
    } else {
      valida_xml(null);
    }
  }
}

function valida_xml(xml) {
  var DOM_turma_sala = new Array();

  if (xml != null) {
    DOM_turma_sala = xml.getElementsByTagName('item');
  }

  var campoCurso = document.getElementById('ref_cod_curso').value;

  if (document.getElementById('ref_cod_escola').value) {
    if (!document.getElementById('ref_cod_serie').value) {
      alert('Preencha o campo \'Serie\' corretamente!');
      document.getElementById('ref_cod_serie').focus();
      return false;
    }
  }

  if (document.getElementById('padrao_ano_escolar').value == 1) {
    var campoHoraInicial = document.getElementById('hora_inicial').value;
    var campoHoraFinal = document.getElementById('hora_final').value;
    var campoHoraInicioIntervalo = document.getElementById('hora_inicio_intervalo').value;
    var campoHoraFimIntervalo = document.getElementById('hora_fim_intervalo').value;


  }

  if (document.getElementById('padrao_ano_escolar') == 1) {
    for (var j = 0; j < DOM_turma_sala.length; j += 2) {
      if (
        (DOM_turma_sala[j].firstChild.nodeValue <= document.getElementById('hora_inicial').value) &&
        (document.getElementById('hora_inicial').value <= DOM_turma_sala[j + 1].firstChild.nodeValue)
        ||
        (DOM_turma_sala[j].firstChild.nodeValue <= document.getElementById('hora_final').value) &&
        (document.getElementById('hora_final').value <= DOM_turma_sala[j + 1].firstChild.nodeValue)
      ) {
        alert('ATENÇÃO!\nA \'sala\' ja esta alocada nesse horario!\nPor favor, escolha outro horario ou sala.');
        return false;
      }
    }
  }

  if (!acao()) {
    return false;
  }

  document.forms[0].submit();
}

function excluir_turma_com_matriculas() {

  document.formcadastro.reset();
  alert(stringUtils.toUtf8('Não foi possível excluir a turma, pois a mesma possui matrículas vinculadas.'));
}

function validaCampoServidor() {
  if (document.getElementById('ref_cod_instituicao').value)
    ref_cod_instituicao = document.getElementById('ref_cod_instituicao').value;
  else {
    alert('Selecione uma instituicao');
    return false;
  }

  if (document.getElementById('ref_cod_escola').value) {
    ref_cod_escola = document.getElementById('ref_cod_escola').value;
  } else {
    alert('Selecione uma escola');
    return false;
  }

  pesquisa_valores_popless('educar_pesquisa_professor_lst.php?campo1=ref_cod_regente&professor=1&ref_cod_servidor=0&ref_cod_instituicao=' + ref_cod_instituicao + '&ref_cod_escola=' + ref_cod_escola, 'ref_cod_servidor');
}

document.getElementById('ref_cod_regente_lupa').onclick = function () {
  validaCampoServidor();
};

function getEscolaCursoSerie() {
  var campoCurso = document.getElementById('ref_cod_curso').value;

  if (document.getElementById('ref_cod_escola')) {
    var campoEscola = document.getElementById('ref_cod_escola').value;
  } else if (document.getElementById('ref_ref_cod_escola')) {
    var campoEscola = document.getElementById('ref_ref_cod_escola').value;
  }

  var campoSerie = document.getElementById('ref_cod_serie');
  campoSerie.length = 1;

  if (campoEscola && campoCurso) {
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Carregando series';

    var xml = new ajax(atualizaLstEscolaCursoSerie);
    xml.envia('educar_escola_curso_serie_xml.php?esc=' + campoEscola + '&cur=' + campoCurso);
  } else {
    campoSerie.options[0].text = 'Selecione';
  }
}

function atualizaLstEscolaCursoSerie(xml) {
  var campoSerie = document.getElementById('ref_cod_serie');
  campoSerie.length = 1;
  campoSerie.options[0].text = 'Selecione uma s\u00e9rie';
  campoSerie.disabled = false;

  series = xml.getElementsByTagName('serie');

  if (series.length) {
    for (var i = 0; i < series.length; i++) {
      campoSerie.options[campoSerie.options.length] = new Option(
        series[i].firstChild.data, series[i].getAttribute('cod_serie'), false, false,
      );
    }
  } else {
    campoSerie.options[0].text = 'A escola/curso n\u00e3o possui nenhuma s\u00e9rie';
  }
}


$j(document).ready(function () {
  $j('#scripts').closest('tr').hide();

  disableInputsDisciplinas();
});

$j('.etapas_utilizadas').mask('9,9,9,9', { placeholder: '1,2,3...' });

$j('#definir_componentes_diferenciados').on('click', function () {
  disableInputsDisciplinas();
});

$j('.check-disciplina').on('change', function () {
  var enabled = $j(this).prop('checked');
  $j(this).closest('.linha-disciplina').find('input:not(.check-disciplina)').attr('disabled', !enabled);
});

function disableInputsDisciplinas() {
  var disable = $j('#definir_componentes_diferenciados').prop('checked');

  $j('#disciplinas').find('input').attr('disabled', !disable);
  $j('#disciplinas').find('.check-disciplina').each(function () {
    $j(this).trigger('change');
  });
}


$j(document).ready(function () {
  $j('#scripts').closest('tr').hide();

  disableInputsDisciplinas();
});

$j('.etapas_utilizadas').mask('9,9,9,9', { placeholder: '1,2,3...' });

$j('#definir_componentes_diferenciados').on('click', function () {
  disableInputsDisciplinas();
});

$j('.check-disciplina').on('change', function () {
  var enabled = $j(this).prop('checked');
  $j(this).closest('.linha-disciplina').find('input:not(.check-disciplina)').attr('disabled', !enabled);
});

function disableInputsDisciplinas() {
  var disable = $j('#definir_componentes_diferenciados').prop('checked');

  $j('#disciplinas').find('input').attr('disabled', !disable);
  $j('#disciplinas').find('.check-disciplina').each(function () {
    $j(this).trigger('change');
  });
}

$j("#multiseriada").on('change', function () {
  let checked = $j(this).is(':checked') ? 1 : 0;
  $j(this).val(checked);
});

function excluirAjax() {
  const settings = {
    url: "/turma",
    data: $j("#formcadastro").serialize(),
    cache: false,
    type: 'delete',
    dataType: 'json',
    beforeSend: function () {
      buttonUtils.loading('btn_excluir');
    }
  };
  $j.ajax(settings)
    .done(function (){
      windowUtils.redirect('educar_turma_lst.php');
    })
    .fail(function ({responseText}) {
      let jsonResponse = JSON.parse(responseText);
      messageUtils.error(jsonResponse.msg);
    })
    .always(function (){
      buttonUtils.reset('btn_excluir', 'Excluir');
    });
}

function doAjax() {

  const settings = {
    url: "/turma",
    data: $j("#formcadastro").serialize(),
    cache: false,
    type: 'POST',
    dataType: 'json',
    beforeSend: function () {
      buttonUtils.loading('btn_enviar');
    }
  };
  $j.ajax(settings)
    .done(function (){
      windowUtils.redirect('educar_turma_lst.php');
    })
    .fail(function ({responseText}) {
      let jsonResponse = JSON.parse(responseText);
      messageUtils.error(jsonResponse.msg);
    })
    .always(function (){
      buttonUtils.reset('btn_enviar', 'Salvar');
    });
}
