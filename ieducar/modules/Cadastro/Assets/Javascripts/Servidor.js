$j('#btn_enviar').removeAttr('onclick');
$j('#btn_enviar').on('click', () => {
  if (!validaServidor() || !validaPosGraduacao() || !validaCursoFormacaoContinuada() || !validationUtils.validatesFields(false) || !validateGraduations()) {
    return false;
  }

  let block = false;

  $j('.ref_cod_funcao select').each(function () {
    const $this = $j(this);
    const original = $this.data('valor-original');
    const value = $this.val();

    if (original != '' && original != value) {
      block = true;
    }
  });

  if (block) {
    confirmaEnvio();
  } else {
    acao();
  }
});

function confirmaEnvio() {
  const dialogId = 'dialog-confirma';
  let dialogElm = $j('#' + dialogId);

  if (dialogElm.length < 1) {
    $j('body')
      .append('<div id="' + dialogId + '">Se deseja alterar a função de alguma alocação, adicione uma nova função ao cadastro do servidor; caso contrário, estará atualizando também todas as alocações marcadas com esta função. Deseja prosseguir mesmo assim?</div>');

    dialogElm = $j('#' + dialogId);
  }

  if (dialogElm.is(':ui-dialog')) {
    dialogElm.dialog('destroy');
  }

  dialogElm.dialog({
    width: 600,
    title: 'Atenção!',
    buttons: [
      {
        text: 'Sim',
        click: () => {
          acao();
        }
      }, {
        text: 'Não',
        click: () => {
          dialogElm.dialog('close');
        }
      }
    ]
  });
}

let obrigarCamposCenso = $j('#obrigar_campos_censo').val() == '1';

function validaServidor() {
  var inepServidor = $j('#cod_docente_inep').val();

  if (inepServidor.length > 0 && inepServidor.length != 12) {
    messageUtils.error('O código INEP deve conter 12 dígitos');
    return false;
  }

  return true
}

function validaPosGraduacao() {
  posGraduacao = $j('#pos_graduacao').val() || [];
  possuiOpcaoNenhuma = $j.inArray('4', posGraduacao) != -1;
  possuiMaisDeUmaOpcao = posGraduacao.length > 1;

  if (possuiOpcaoNenhuma && possuiMaisDeUmaOpcao) {
    messageUtils.error('Não é possível informar mais de uma opção no campo: <b>Possui pós-graduação</b>, quando a opção: <b>Nenhuma</b> estiver selecionada.');
    return false;
  }

  return true;
}

function validaCursoFormacaoContinuada() {
  cursoFormacaoContinuada = $j('#curso_formacao_continuada').val() || [];
  possuiOpcaoNenhum = $j.inArray('16', cursoFormacaoContinuada) != -1;
  possuiMaisDeUmaOpcao = cursoFormacaoContinuada.length > 1;

  if (possuiOpcaoNenhum && possuiMaisDeUmaOpcao) {
    messageUtils.error('Não é possível informar mais de uma opção no campo: <b>Possui cursos de formação continuada</b>, quando a opção: <b>Nenhum</b> estiver selecionada.');
    return false;
  }

  return true;
}

verificaCamposObrigatorio(1);
verificaCamposObrigatorio(2);
verificaCamposObrigatorio(3);
habilitaCampoPosGraduacao();


$j('#ref_idesco').on('change', ()=> {
  verificaCamposObrigatorio(1);
});

function verificaCamposObrigatorio(seq) {
  $j(`#situacao_curso_superior_${seq}`).makeUnrequired();
  $j(`#codigo_curso_superior_${seq}`).makeUnrequired();
  $j(`#ano_inicio_curso_superior_${seq}`).makeUnrequired();
  $j(`#ano_conclusao_curso_superior_${seq}`).makeUnrequired();
  $j(`#instituicao_curso_superior_${seq}`).makeUnrequired();

  if($j('#ref_idesco').val() && seq == 1) {
    var options = {
      dataType : 'json',
      url : getResourceUrlBuilder.buildUrl(
        '/module/Api/Servidor',
        'escolaridade',
        {idesco : $j('#ref_idesco').val()}
      ),
      success : function(dataResponse) {
        if(obrigarCamposCenso && dataResponse.escolaridade.escolaridade == '6'){
          $j(`#situacao_curso_superior_${seq}`).makeRequired();
          $j(`#codigo_curso_superior_${seq}`).makeRequired();
          $j(`#ano_inicio_curso_superior_${seq}`).makeRequired();
          $j(`#ano_conclusao_curso_superior_${seq}`).makeRequired();
          $j(`#instituicao_curso_superior_${seq}`).makeRequired();
        }
      }
    }
    getResource(options);
  } else if(seq >=2 && obrigarCamposCenso && $j(`#situacao_curso_superior_${seq}`).val()) {
    $j(`#situacao_curso_superior_${seq}`).makeRequired();
    $j(`#codigo_curso_superior_${seq}`).makeRequired();
    $j(`#ano_inicio_curso_superior_${seq}`).makeRequired();
    $j(`#ano_conclusao_curso_superior_${seq}`).makeRequired();
    $j(`#instituicao_curso_superior_${seq}`).makeRequired();
  }
}

function habilitaCampoPosGraduacao() {
  var possuiSuperiorConcuido = true;

  $j('#tr_pos_graduacao').hide();
  $j('#pos_graduacao').makeUnrequired();
  if (possuiSuperiorConcuido) {
    $j('#tr_pos_graduacao').show();
    if (obrigarCamposCenso) {
      $j('#pos_graduacao').makeRequired();
    }
  }
}

//abas

$j('.tablecadastro').children().children('tr:first').children('td:first').append('<div id="tabControl"><ul><li><div id="tab1" class="servidorTab"> <span class="tabText">Dados gerais</span></div></li><li><div id="tab2" class="servidorTab"> <span class="tabText">Dados adicionais</span></div></li></ul></div>');
$j('.tablecadastro').children().children('tr:first').children('td:first').find('b').remove();
$j('#tab1').addClass('servidorTab-active').removeClass('servidorTab');

// Adiciona um ID à linha que termina o formulário para parar de esconder os campos
$j('.tableDetalheLinhaSeparador').closest('tr').attr('id','stop');

// Pega o número dessa linha
linha_inicial_escolaridade = $j('#tr_ref_idesco').index()-1;

// hide nos campos das outras abas (deixando só os campos da primeira aba)
$j('.tablecadastro >tbody  > tr').each(function(index, row) {
  if (index>=linha_inicial_escolaridade - 1){
    if (row.id!='stop')
      row.hide();
    else{
      return false;
    }
  }
});

$j(document).ready(function() {

  // on click das abas

  // DADOS GERAIS
  $j('#tab1').click(
    function(){

      $j('.servidorTab-active').toggleClass('servidorTab-active servidorTab');
      $j('#tab1').toggleClass('servidorTab servidorTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (index>=linha_inicial_escolaridade -1){
          if (row.id!='stop')
            row.hide();
          else
            return false;
        }else{
          if ($j('#cod_servidor').val() != '' || $j.inArray(row.id, ['tr_deficiencias', 'tr_cod_docente_inep']) == -1)
            row.show();
        }
      });
    }
  );

  // Adicionais
  $j('#tab2').click(
    function(){
      $j('.servidorTab-active').toggleClass('servidorTab-active servidorTab');
      $j('#tab2').toggleClass('servidorTab servidorTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_escolaridade -1){
            if ((index - linha_inicial_escolaridade + 1) % 2 == 0){
              $j('#'+row.id).find('td').removeClass('formlttd');
              $j('#'+row.id).find('td').addClass('formmdtd');
            }else{
              $j('#'+row.id).find('td').removeClass('formmdtd');
              $j('#'+row.id).find('td').addClass('formlttd');

            }
            row.show();
          }else if (index>0){
            row.hide();
          }
        }else
          return false;
      });
      habilitaCampoPosGraduacao();
    });

  // fix checkboxs
  $j('.tablecadastro >tbody  > tr').each(function(index, row) {
    if (index>=linha_inicial_escolaridade){
      $j('#'+row.id).find('input:checked').val('on');
    }
  });
});

var searchCourse = function (request, response) {
  var searchPath = '/module/Api/CursoSuperior?oper=get&resource=cursosuperior-search',
      params = {
        query: request.term
      };

  $j.get(searchPath, params, function (dataResponse) {
    simpleSearch.handleSearch(dataResponse, response);
  });
};

var handleSelectCourse = function (event, ui) {
  var target = $j(event.target),
      id = target.attr('id'),
      idNum = id.match(/\[(\d+)\]/),
      refIdCourse = $j('input[id="employee_course_id[' + idNum[1] + ']"]');

  target.val(ui.item.label);
  refIdCourse.val(ui.item.value);

  return false;
};

var searchCollege = function (request, response) {
  var searchPath = '/module/Api/Ies?oper=get&resource=ies-search',
      params = {
        query: request.term
      };

  $j.get(searchPath, params, function (dataResponse) {
    simpleSearch.handleSearch(dataResponse, response);
  });
};

var handleSelectCollege = function (event, ui) {
  var target = $j(event.target),
      id = target.attr('id'),
      idNum = id.match(/\[(\d+)\]/),
      refIdCourse = $j('input[id="employee_college_id[' + idNum[1] + ']"]');

  target.val(ui.item.label);
  refIdCourse.val(ui.item.value);

  return false;
};

function setAutoComplete() {
  $j.each($j('input[id^="employee_course"]'), function (index, field) {
    $j(field).autocomplete({
      source: searchCourse,
      select: handleSelectCourse,
      minLength: 1,
      autoFocus: true,
      autoSelect: true,
    });

    $j(field).attr('placeholder', 'Digite um nome para buscar');

    $j('input[id^="employee_course"]').keydown(function(event) {
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13' || keycode == '9') {
        return;
      }

      var id = $j(this).attr('id'),
          idNum = id.match(/\[(\d+)\]/),
          refIdCourse = $j('input[id="employee_course_id[' + idNum[1] + ']"]');

      refIdCourse.val('')
    });
  });

  $j.each($j('input[id^="employee_college"]'), function (index, field) {
    $j(field).autocomplete({
      source: searchCollege,
      select: handleSelectCollege,
      minLength: 1,
      autoFocus: true,
      autoSelect: true,
    });

    $j(field).attr('placeholder', 'Digite um nome para buscar');

    $j('input[id^="employee_college"]').keydown(function(event) {
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13' || keycode == '9') {
        return;
      }

      var id = $j(this).attr('id'),
          idNum = id.match(/\[(\d+)\]/),
          refIdCourse = $j('input[id="employee_college_id[' + idNum[1] + ']"]');

      refIdCourse.val('')
    });
  });
};

function setupInputs() {
  $j('input[id^="completion_year"]').keyup(function(){
    var oldValue = this.value;

    this.value = this.value.replace(/[^0-9\.]/g, '');
    this.value = this.value.replace('.', '');

    if (oldValue != this.value)
      messageUtils.error('Informe apenas números.', this);
  });
}

$j('#btn_add_tab_add_2').click(function () {
  setAutoComplete();
  setupInputs();
});

setAutoComplete();
setupInputs();

function validateGraduations() {
  var result = true;

  if ($j('input[id^="employee_course_id"]').length > 3) {
    messageUtils.error('Informe no máximo 3 cursos superiores realizados');
    return false;
  }

  if (!obrigarCamposCenso || $j('#ref_idesco').val() != 6) {
    return result;
  }

  $j.each($j('input[id^="employee_course_id"]'), function (index, field) {
    var id = $j(field).attr('id');
    var idNum = id.match(/\[(\d+)\]/);
    var courseId = $j(field),
        courseName = $j('input[id="employee_course[' + idNum[1] + ']"]'),
        completionYear = $j('input[id="employee_completion_year[' + idNum[1] + ']"]'),
        collegeId = $j('input[id="employee_college_id[' + idNum[1] + ']"]'),
        collegeName = $j('input[id="employee_college[' + idNum[1] + ']"]');

    if (courseId.val() == '' || courseName.val() == '') {
      messageUtils.error('O campo: curso é obrigatório', courseName);
      result = false;
    }

    if (completionYear.val() == '') {
      messageUtils.error('O campo: Ano de conclusão é obrigatório', completionYear);
      result = false;
    }

    if (completionYear.val().length != 4) {
      messageUtils.error('O campo: Ano de conclusão do curso superior deve conter 4 dígitos', completionYear);
      result = false;
    }

    if (parseInt(completionYear.val()) < 1940) {
      messageUtils.error('O campo: Ano de conclusão do curso superior deve ser maior que 1940', completionYear);
      result = false;
    }

    if (parseInt(completionYear.val()) > (new Date().getFullYear())) {
      messageUtils.error('O campo: Ano de conclusão do curso superior não deve ser maior que o ano atual', completionYear);
      result = false;
    }

    if (collegeId.val() == '' || collegeName.val() == '') {
      messageUtils.error('O campo: Instituição de Educação Superior é obrigatório', collegeName);
      result = false;
    }
  });

  return result;
}