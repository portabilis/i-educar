var $j = jQuery.noConflict();

(function($) {

  $(function(){

    //global ajax events
    $('.change-visibility-on-ajax-change')
      .ajaxStart(function() { $(this).show(); })
      .ajaxStop(function() { $(this).hide(); });

    $('.change-status-on-ajax-change')
      .ajaxStart(function() { $(this).attr('disabled', 'disabled'); })
      .ajaxStop(function() { $(this).removeAttr('disabled'); });

    var $formFilter = $('#formcadastro');
    var $resultTable = $('#form_resultado .tablelistagem');
    var $submitButton = $('#botao_busca');

    //ao submeter form carregar matriculas, notas, faltas e pareceres
    //ao mudar nota, falta ou parecer enviar postar, e tratar retorno

    var matriculasSearchBuilder = {
      urlBase : 'diarioAjax',
      buildUrl : function(urlBase){
        var vars = {
          att : 'matriculas',
          oper : 'get',
          instituicao_id : $j('#ref_cod_instituicao').val(),
          escola_id : $j('#ref_cod_escola').val(),
          curso_id : $j('#ref_cod_curso').val(),
          serie_id : $j('#ref_ref_cod_serie').val(),
          turma_id : $j('#ref_cod_turma').val(),
          ano_escolar : $j('#ano_escolar').val(),
          componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
          etapa : $j('#etapa').val()
        }

        _vars = '';
        for(varName in vars){
          _vars += '&'+varName+'='+vars[varName];
        }

        return (urlBase || this.urlBase) + '?' + _vars;
      }
    };


    var matriculasSearchOptions = {
      url : '',
      dataType : 'json',
      success : handleMatriculasSearch
    };


    function handleMessages(messages){

      for (var i = 0; i < messages.length; i++){
        console.log('#TODO show messages');
        console.log(messages[i].type + ' - '+ messages[i].msg);
      }
    }


    function handleMatriculasSearch(dataResponse) { 
        $submitButton.val('Carregar');

      handleMessages(dataResponse.msgs);

        var $linha = $('<tr />');
        $('<th />').html('Matricula').appendTo($linha);
        $('<th />').html('Aluno').appendTo($linha);
        $('<th />').html('Situação').appendTo($linha);
        $('<th />').html('Nota').appendTo($linha);
        $('<th />').html('Falta').appendTo($linha);
        $('<th />').html('Parecer').appendTo($linha);
        $linha.appendTo($resultTable);

      $.each(dataResponse.matriculas, function(index, value){

        var $linha = $('<tr />');
        
        $('<td />').html(value.matricula_id).appendTo($linha);
        $('<td />').html(value.aluno_id + ' - ' +value.nome).appendTo($linha);
        $('<td />').html(value.situacao).appendTo($linha);
        $('<td />').html(value.nota_atual).appendTo($linha);
        $('<td />').html(value.falta_atual).appendTo($linha);
        $('<td />').html(value.parecer_atual).appendTo($linha);

        $linha.appendTo($resultTable);
      });
      $resultTable.find('tr:even').addClass('even');
    }

    $submitButton.val('Carregar');
    $submitButton.addClass('change-status-on-ajax-change');
    $submitButton.attr('onclick', '');
    $submitButton.click(function(event){
      if (validatesPresenseOfValueInRequiredFields())
      {
        matriculasSearchOptions.url = matriculasSearchBuilder.buildUrl();

        if (window.history && window.history.pushState)
          window.history.pushState('', '', matriculasSearchBuilder.buildUrl('diario'));

        $submitButton.val('Carregando...');
        $resultTable.children().remove();
        $formFilter.submit();
      }
    });


    $formFilter.ajaxForm(matriculasSearchOptions);

  });

})(jQuery);
