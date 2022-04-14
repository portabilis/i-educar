document.getElementById('data').disabled = document.getElementById('ref_cod_turma').value != '';

const maxCaracteresObservacao = 256;

var rebuildAllChosenAnosLetivos = undefined;
function existeComponente(){
    if ($j('input[name^="disciplinas["]:checked').length <= 0) {
        alert('É necessário adicionar pelo menos um componente curricular.');
        return false;
    }
    return true;
}

document.getElementById('data').onchange = function () {
  const ano = document.getElementById('data').value.split('/')[2];
  const anoElement = document.getElementById('ano');
  anoElement.value = ano;

  var evt = document.createEvent('HTMLEvents');
  evt.initEvent('change', false, true);
  anoElement.dispatchEvent(evt);
};

function getAluno(xml_aluno) {
    var campoAlunos = document.getElementById('alunos');
    var DOM_array = xml_aluno.getElementsByTagName("aluno");

    var conteudo = '';

    if (DOM_array.length) {
        conteudo += '<td class="tableDetalheLinhaSeparador" colspan="3"></td><tr><td><div class="scroll"><table class="tableDetalhe tableDetalheMobile" width="100%"><tr class="tableHeader">';
        conteudo += '  <th><span style="display: block; float: left; width: auto; font-weight: bold">' + "Nome" + '</span></th>';
        conteudo += '  <th><span style="display: block; float: left; width: auto; font-weight: bold">' + "Presença" + '</span></th>';
        conteudo += '  <th><span style="display: block; float: left; width: auto; font-weight: bold">' + "Justificativa" + '</span></th>';
        conteudo += '</tr>';
        conteudo += '<tr><td class="tableDetalheLinhaSeparador" colspan="3"></td></tr>';
        
        for (var i = 0; i < DOM_array.length; i++) {
            id = DOM_array[i].getAttribute("cod_aluno");

            conteudo += ' <td class="sizeFont colorFont"><p>' + DOM_array[i].firstChild.data + '</p></td>';
            conteudo += ` <td class="sizeFont colorFont" > \
                            <input type="checkbox" onchange="presencaMudou(this)" id="alunos[]" name='alunos[${id}]' Checked> \
                          </td>`;
            conteudo += ` <td><input type='text' name='justificativa[${id}][]' style="display: flex;" maxlength=${maxCaracteresObservacao} disabled></input></td>`;
            conteudo += ' </tr>';
        }
    } else {
        campoAlunos.innerHTML = 'Faltam informações obrigatórias.';
    }

    if (conteudo) {
        campoAlunos.innerHTML = '<table cellspacing="0" cellpadding="0" border="0">';
        campoAlunos.innerHTML += '<tr align="left"><td><p>' + conteudo + '</p></td></tr>';
        campoAlunos.innerHTML += '</table>';
    }
}

document.getElementById('ref_cod_componente_curricular').onchange = function () {
    var campoTurma = document.getElementById('ref_cod_turma').value;
    var campoComponenteCurricular = document.getElementById('ref_cod_componente_curricular').value;

    var campoAlunos = document.getElementById('alunos');
    campoAlunos.innerHTML = "Carregando alunos...";

    var xml_disciplina = new ajax(getAluno);
    xml_disciplina.envia("educar_aluno_xml.php?tur=" + campoTurma + "&ccur=" + campoComponenteCurricular);
};

function presencaMudou (presenca) {
  document.getElementsByName("justificativa[" + pegarIdPresenca(presenca) + "][]")[0].disabled = presenca.checked;
}

function pegarIdPresenca (presenca) {
  let id = presenca.name;
  id = id.substring(id.indexOf('[') + 1, id.indexOf(']'));

  return id;
}