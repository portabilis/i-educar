(function($){
  $(document).ready(function(){
    var refCodTurma = $('#ref_cod_turma').val();

    if (refCodTurma == '') {
      hideOrdensAulas();
    }

    function hideOrdensAulas() {
      for (let i = 1; i <= 5; i++) {
        $('#tr_ordens_aulas' + i)
          .hide();
        $('#ordens_aulas' + i).val('').empty().hide();
      }
    }

    document.getElementById('fase_etapa').onchange = function () {
      carregaConteudos();
    };

    document.getElementById('ref_cod_componente_curricular').onchange = function () {
      carregaConteudos();
    };

    function carregaConteudos() {
      const campoTurma = document.getElementById('ref_cod_turma').value;
      const tipoPresenca = $('#ref_cod_turma').attr('tipo_presenca');
      const campoFaseEtapa = document.getElementById('fase_etapa').value;
      const campoData = document.getElementById('data').value;
      const campoComponenteCurricular = document.getElementById('ref_cod_componente_curricular').value;

      if (!campoTurma || !campoFaseEtapa || !campoData) {
        $('#conteudos').val([]).empty().trigger('chosen:updated');
        getResource(false);
      }

      let url = getResourceUrlBuilder.buildUrl(
        '/module/Api/PlanejamentoAulaConteudo',
        'pacByFreq',
        { campoTurma : campoTurma,
          tipoPresenca : tipoPresenca,
          campoFaseEtapa : campoFaseEtapa,
          campoData: campoData,
          campoComponenteCurricular: campoComponenteCurricular}
      );

      var options = {
        url      : url,
        dataType : 'json',
        success  : function (dataResponse) {
          $('#conteudos').val([]).empty().trigger('chosen:updated');

          if (dataResponse.pac != null) {
            $('#conteudos').html(
              (Object.keys(dataResponse.pac[1] || [])
                .map(key => !dataResponse.pac[1][key][1]
                  ? `<option value='${key}'>${dataResponse.pac[1][key][0]}</option>`
                  : `<option value='${key}' style="color:blue">${dataResponse.pac[1][key][0]}</option>`)).join()
            ).trigger('chosen:updated');
          }
        }

      };

      getResource(options);
    }

    document.getElementById('ref_cod_turma').onchange = function () {
      const campoTurma = document.getElementById('ref_cod_turma').value;

      if (!campoTurma) {
        hideOrdensAulas();
        getResource(false);
      }

      let params = {id: campoTurma};
      let options = {
        url: getResourceUrlBuilder.buildUrl('/module/Api/Frequencia', 'getTipoPresenca', params),
        dataType: 'json',
        data: {},
        success: function (response) {
          $('#ref_cod_turma').attr('tipo_presenca', response.tipo_presenca);

          carregarAulas(response.tipo_presenca, campoTurma);
        },
      };

      getResource(options);

    }

    $('input[type="checkbox"]').change(function() {
      let name = $(this).attr('name');
      if (name.indexOf('ordens_aulas') > -1) {
        carregarAlunos();
      }
    });

    var id = $j('#id').val();
    var servidor_id = $j('#servidor_id').val();
    var auth_id = $j('#auth_id').val();
    var is_professor = $j('#is_professor').val();

    function addBtnEnviarMensagem() {
      if (servidor_id) {
        let html = "<a" +
          "          id='enviar_mensagem_btn["+id+"]'\n" +
          "          name='enviar_mensagem_btn[]'\n" +
          "          style='width: 80px;cursor: pointer;text-decoration: none;font-size: 13px;float: right;'\n" +
          "          class='btn btn-info'\n" +
          "          onClick='modalOpen(this, "+id+", 1, "+servidor_id+", null, "+auth_id+", "+Boolean(is_professor)+")'\n" +
          "        ><i class='fa fa-send' aria-hidden='true'></i>" +
          "        </a>";

        $('.tablelistagem').append(html);
      }
    }

    function carregarAulas(tipo_presenca, campoTurma) {
      if (tipo_presenca == 2 || tipo_presenca == '2') {
        const campoData = document.getElementById('data').value;

        let params = {
          id: campoTurma,
          data: campoData
        };

        let options = {
          url: getResourceUrlBuilder.buildUrl('/module/Api/Frequencia', 'getQtdAulasQuadroHorario', params),
          dataType: 'json',
          data: {},
          success: function (response) {
            let qtdAulas = 5;

            if (response.isProfessor) {
              qtdAulas = response.qtdAulas;
            }

            if (qtdAulas > 0) {
              for (let i = 1; i <= qtdAulas; i++) {
                $('#tr_ordens_aulas' + i).show();
                $('#ordens_aulas' + i).show();
              }
            } else {
              alert('A data não está alocada no quadro de horário');
              return false;
            }
          },
        };

        getResource(options);

      } else {
        carregarAlunos();
      }
    }


    addBtnEnviarMensagem();
  });
})(jQuery);

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

  let qtdAulas = 0;

  for (let i = 1; i <= 5; i++) {
    if (document.getElementById("ordens_aulas" + i).checked) {
      qtdAulas += 1;
    }
  }

  var conteudo = '';

  if (DOM_array.length) {
    conteudo += '<td class="tableDetalheLinhaSeparador" colspan="3"></td><tr><td><div class="scroll"><table class="tableDetalhe tableDetalheMobile" width="100%"><tr class="tableHeader">';
    conteudo += '  <th><span style="display: block; float: left; width: auto; font-weight: bold">' + "Nome" + '</span></th>';
    conteudo += '  <th><span style="display: block; float: left; width: auto; font-weight: bold">' + "FA" + '</span></th>';

    if (qtdAulas == 0) {
      conteudo += '  <th><span style="display: block; float: left; width: auto; font-weight: bold">' + "Presença" + '</span></th>';
    } else {
      for (let qtd = 1; qtd <= qtdAulas; qtd++) {
        conteudo += '  <th><span style="display: block; float: left; width: auto; font-weight: bold">' + "Aula " + qtd + '</span></th>';
      }
    }

    conteudo += '  <th><span style="display: block; float: left; width: auto; font-weight: bold">' + "Justificativa" + '</span></th>';
    conteudo += '</tr>';
    conteudo += '<tr><td class="tableDetalheLinhaSeparador" colspan="3"></td></tr>';

    for (var i = 0; i < DOM_array.length; i++) {
      id = DOM_array[i].getAttribute("cod_aluno");
      qtd_faltas = DOM_array[i].getAttribute("qtd_faltas");
      conteudo += ' <td class="sizeFont colorFont"><p>' + DOM_array[i].firstChild.data + '</p></td>';
      conteudo += ' <td class="sizeFont colorFont"><p>' + qtd_faltas + '</p></td>';

      if (qtdAulas == 0) {
        conteudo += ` <td class="sizeFont colorFont" > \
                            <input type="checkbox" onchange="presencaMudou(this)" id="alunos[]" name="alunos[]" value="${id}" Checked>
                          </td>`;
      } else {
        for (let qtd = 1; qtd <= qtdAulas; qtd++) {
          conteudo += ` <td class="sizeFont colorFont" > \
                            <input type="checkbox" onchange="presencaMudou(this)" id="alunos[]" name='alunos[]' data-aulaid="${qtd}" value="${id}" Checked>
                          </td>`;
        }
      }

      conteudo += ` <td><input type='text' name='justificativa[${id}][]' style="display: flex;" maxlength=${maxCaracteresObservacao} disabled></td>`;
      conteudo += ` <td><input type='hidden' name='justificativa[${id}][qtd]' style="display: flex;" value="0" readonly></td>`;
      conteudo += ` <td><input type='hidden' name='justificativa[${id}][aulas]' style="display: flex;" readonly></td>`;
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

function carregarAlunos() {
    var campoTurma = document.getElementById('ref_cod_turma').value;
    var campoComponenteCurricular = document.getElementById('ref_cod_componente_curricular').value;
    var campoData = document.getElementById('data').value;

    var campoAlunos = document.getElementById('alunos');
    campoAlunos.innerHTML = "Carregando alunos...";

    var xml_disciplina = new ajax(getAluno);
    xml_disciplina.envia("educar_aluno_xml.php?tur=" + campoTurma + "&ccur=" + campoComponenteCurricular + "&data=" + campoData);
}

function presencaMudou (presenca) {
  let elementJustificativa = document.getElementsByName("justificativa[" + presenca.value + "][]")[0];
  let elementJustificativaQtd = document.getElementsByName("justificativa[" + presenca.value + "][qtd]")[0];
  let elementJustificativaAulas = document.getElementsByName("justificativa[" + presenca.value + "][aulas]")[0];

  let aula_id = presenca.dataset.aulaid;
  let aulasValue = '';
  let qtdValue = '';

  if (elementJustificativaQtd) {
    qtdValue = elementJustificativaQtd.value;
  }

  if (elementJustificativaAulas) {
    aulasValue = elementJustificativaAulas.value;
  }

  if (presenca.checked) {
    elementJustificativaQtd.value = parseInt(elementJustificativaQtd.value) - 1;

    if (aulasValue != undefined && aulasValue.indexOf(aula_id + ',') > -1) {
      elementJustificativaAulas.value = aulasValue.replace(aula_id + ',', '');
    }

  } else if (qtdValue != '' || parseInt(qtdValue) >= 0) {
    elementJustificativaQtd.value = parseInt(qtdValue) + 1;
    if (aulasValue != undefined) {
      elementJustificativaAulas.value = aulasValue + aula_id + ',';
    }

  }

  if (presenca.checked && parseInt(qtdValue) > 0){
    elementJustificativa.disabled = !presenca.checked;
  } else {
    elementJustificativa.disabled = presenca.checked;
  }
}

function pegarIdPresenca (presenca) {
  let id = presenca.name;
  id = id.substring(id.indexOf('[') + 1, id.indexOf(']'));

  return id;
}
