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
  document.getElementById('ano').value = ano;
};

function getAluno(xml_aluno) {
    var campoAlunos = document.getElementById('alunos');
    var DOM_array = xml_aluno.getElementsByTagName("aluno");

    var conteudo = '';

    if (DOM_array.length) {
        conteudo += '<div style="margin-bottom: 10px; float: left">';
        conteudo += '  <span style="display: block; float: left; width: 400px;">Nome</span>';
        conteudo += '  <span style="display: block; float: left; width: 180px;">Presença?</span>';
        conteudo += '  <span style="display: block; float: left; width: 300px;">' + "Justificativa (" + maxCaracteresObservacao + " caracteres são permitidos)" + '</span>';
        conteudo += '</div>';

        for (var i = 0; i < DOM_array.length; i++) {
            id = DOM_array[i].getAttribute("cod_aluno");

            conteudo += '<div style="margin-bottom: 10px; float: left">';
            conteudo += '  <label style="display: block; float: left; width: 400px;">' + DOM_array[i].firstChild.data + '</label>';
            conteudo += ` <label style="display: block; float: left; width: 180px;"> \
                            <input type="checkbox" onchange="presencaMudou(this)" id="alunos[]" name='alunos[${id}]' Checked> \
                          </label>`;
            conteudo += `<input type='text' name='justificativa[${id}][]' style='width: 300px;' maxlength=${maxCaracteresObservacao} disabled></input>`;
            conteudo += '</div>';
            conteudo += '<br style="clear: left" />';
        }
    } else {
        campoAlunos.innerHTML = 'Faltam informações obrigatórias.';
    }

    if (conteudo) {
        campoAlunos.innerHTML = '<table cellspacing="0" cellpadding="0" border="0">';
        campoAlunos.innerHTML += '<tr align="left"><td>' + conteudo + '</td></tr>';
        campoAlunos.innerHTML += '</table>';
    }
}

function getTipoPresenca(xml_tipo_presenca) {
  tipoPresenca = xml_tipo_presenca.getElementById("tipoPresenca").firstChild.data;
  console.log(tipoPresenca);

  document.getElementById('tr_ref_cod_componente_curricular').childNodes(0).innerHTML('<span class="campo_obrigatorio">*</span>')
}

document.getElementById('ref_cod_serie').onchange = function () {
  var campoSerie = document.getElementById('ref_cod_serie').value;

  var xml_tipo_presenca = new ajax(getTipoPresenca);
  xml_tipo_presenca.envia("educar_tipo_presenca_xml.php?ser=" + campoSerie);
};

document.getElementById('ref_cod_componente_curricular').onchange = function () {
    var campoTurma = document.getElementById('ref_cod_turma').value;
    var campoComponenteCurricular = document.getElementById('ref_cod_componente_curricular').value;

    var campoAlunos = document.getElementById('alunos');
    campoAlunos.innerHTML = "Carregando alunos...";

    var xml_disciplina = new ajax(getAluno);
    xml_disciplina.envia("educar_aluno_xml.php?tur=" + campoTurma + "&ccur=" + campoComponenteCurricular);
};

after_getEscola = function () {
    getEscolaCurso();

    var campoAlunos = document.getElementById('alunos');
    campoAlunos.innerHTML = "Nenhuma turma selecionada.";
};

var submitButton = $j('#btn_enviar');
submitButton.removeAttr('onclick');

submitButton.click(function(){
    var componentesInput = $j('[name*=disciplinas]');
    var arrayComponentes = [];

    componentesInput.each(function(i, input) {
        id = input.name.replace(/\D/g, '');
        check = $j('[name="disciplinas[' + id + ']"]').is(':checked');

        if (check) {
            arrayComponentes.push(id);
        }
    });

    acao();
});

(function($){
    $(document).ready(function(){
      let ajustaDisciplinas = () => {
        let $tr = $('<tr/>');
        $tr.insertBefore($('#tr_disciplinas_'));
        $('#tr_disciplinas_ td').attr('colspan', '2');
        $('#tr_disciplinas_ td:first').appendTo($tr);
      };
      ajustaDisciplinas();
  
      $('#ref_cod_curso').on('change', function(){
        let codCurso = $(this).val();
        let codEscola = $('#ref_cod_escola').val();
        if (!codCurso || !codEscola) {
          $('#anos_letivos').val([]).empty().trigger('chosen:updated');
          return false;
        }
        let url = getResourceUrlBuilder.buildUrl(
          '/module/Api/EscolaCurso',
          'anos-letivos',
          { cod_curso : codCurso, cod_escola: codEscola }
        );
  
        var options = {
          url      : url,
          dataType : 'json',
          success  : function (dataResponse) {
            $('#anos_letivos').html(
              (dataResponse.anos_letivos||[]).map(ano => `<option value='${ano}'>${ano}</option>`).join()
            ).trigger('chosen:updated');
          }
        };
        getResource(options);
      });
  
      let reloadChosenAnosLetivos = ($element) => {
        if ($element.parent().find('.chosen-container').length > 0) {
            $element.chosen('destroy');
        }
        $element.chosen({
          no_results_text: "Sem resultados para ",
          width: '231px',
          placeholder_text_multiple: "Selecione as opções",
          placeholder_text_single: "Selecione uma opção"
        });
      }
  
      reloadChosenAnosLetivos($('select[name^=justificativa]'));
      $('#anos_letivos').on('change', function(){
        $.each($('select[name^=justificativa]'), function(){
          let oldValues = $(this).val();
          $(this).empty();
          $('#anos_letivos option:selected').clone().appendTo($(this));
          $(this).val(oldValues).trigger('chosen:updated');
        });
      });
  
      rebuildAllChosenAnosLetivos = function(){
        reloadChosenAnosLetivos($('select[name^=justificativa]'));
        $('#anos_letivos').trigger('change');
      }
    });
  })(jQuery);

function presencaMudou (presenca) {
  console.log(presenca);
  document.getElementsByName("justificativa[" + pegarIdPresenca(presenca) + "][]")[0].disabled = presenca.checked;
}

function pegarIdPresenca (presenca) {
  let id = presenca.name;
  id = id.substring(id.indexOf('[') + 1, id.indexOf(']'));

  return id;
}