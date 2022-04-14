function trocaCurso(id_campo) {
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoCurso = document.getElementById(id_campo.id).value;
  var id = /[0-9]+/.exec(id_campo.id);
  var campoDisciplina = document.getElementById('ref_cod_disciplina[' + id + ']');
  campoDisciplina.length = 1;

  if (campoDisciplina) {
    campoDisciplina.disabled = true;
    campoDisciplina.options[0].text = 'Carregando Disciplinas';

    var xml = new ajax(atualizaLstDisciplina, 'ref_cod_disciplina[' + id + ']');
    xml.envia('educar_disciplina_xml.php?cur=' + campoCurso);
  } else {
    campoFuncao.options[0].text = 'Selecione';
  }
}

function atualizaLstDisciplina(xml) {
  var campoDisciplina = document.getElementById(arguments[1]);

  campoDisciplina.length = 1;
  campoDisciplina.options[0].text = 'Selecione uma Disciplina';
  campoDisciplina.disabled = false;

  var disciplinas = xml.getElementsByTagName('disciplina');

  if (disciplinas.length) {
    campoDisciplina.options[campoDisciplina.options.length] =
      new Option('Todas as disciplinas', 'todas_disciplinas', false, false);
    for (var i = 0; i < disciplinas.length; i++) {
      campoDisciplina.options[campoDisciplina.options.length] =
        new Option(disciplinas[i].firstChild.data, disciplinas[i].getAttribute('cod_disciplina'), false, false);
    }
  } else {
    campoDisciplina.options[0].text = 'A instituição não possui nenhuma disciplina';
  }
}

tab_add_1.afterAddRow = function () {
};

window.onload = function () {
};

function trocaTodasfuncoes() {
  for (var ct = 0; ct < tab_add_1.id; ct++) {
    getFuncao('ref_cod_funcao[' + ct + ']');
  }
}

function acao2() {
  var total_horas_alocadas = getArrayHora(document.getElementById('total_horas_alocadas').value);
  var carga_horaria = (document.getElementById('carga_horaria').value).replace(',', '.');

  if (parseFloat(total_horas_alocadas) > parseFloat(carga_horaria)) {
    alert('Atenção, carga horária deve ser maior que horas alocadas!');
    return false;
  } else {
    acao();
  }
}

if (document.getElementById('total_horas_alocadas')) {
  document.getElementById('total_horas_alocadas').style.textAlign = 'right';
}
