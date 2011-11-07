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

    var matriculasSearchUrlBuilder = {
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


    function changeResource($resourceElement, postFunction, deleteFunction){
      if (! $resourceElement.val())
        deleteFunction($resourceElement);
      else
        postFunction($resourceElement);
    };


    var changeNota = function(event){
      changeResource($(this), postNota, deleteNota);
    };


    var changeFalta = function(event){
      changeResource($(this), postFalta, deleteFalta);
    };


    var changeParecer = function(event){
      changeResource($(this), postParecer, deleteParecer);
    };


    function postNota($notaFieldElement){
      console.log('post nota...');
    }


    function postFalta(vars){
      console.log('post falta...');
    }


    function postParecer(vars){
      console.log('post parecer...');
    }

    
    function confirmDelete(resourceName){
      return confirm('Confirma exclusão ' + resourceName + '?');
    }


    function deleteResource(resourceName, options){
      if (confirmDelete(resourceName))
      {
        console.log('#todo call deleteResource url');
      }
      else
      {
        console.log('#todo call getResource url');  
      }
    }
    

    //#TODO change matriculasSearchUrlBuilder to use this helper
    var resourceUrlBuilder = {
      urlBase : 'diarioAjax',
      buildUrl : function(vars, urlBase){

        _vars = '';
        for(varName in vars){
          _vars += '&'+varName+'='+vars[varName];
        }

        return (urlBase || this.urlBase) + '?' + _vars;
      }
    };

  
    var deleteResourceUrlBuilder = {
      urlBase : 'diarioAjax',
      buildUrl : function(resourceName, urlBase){

        var vars = {
          att : resourceName,
          oper : 'delete',
          instituicao_id : $j('#ref_cod_instituicao').val(),
          escola_id : $j('#ref_cod_escola').val(),
          curso_id : $j('#ref_cod_curso').val(),
          serie_id : $j('#ref_ref_cod_serie').val(),
          turma_id : $j('#ref_cod_turma').val(),
          ano_escolar : $j('#ano_escolar').val(),
          componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
          etapa : $j('#etapa').val()
        };

        return resourceUrlBuilder.buildUrl(vars, (urlBase || this.urlBase));

      };
    };


    function deleteNota($notaFieldElement){
      var options = {
        url : '',
        dataType : 'json',
        success : handleDeleteNota
      };

      deleteResource('nota', options);
    };


    function deleteFalta(vars){
      var options = {
        url : '',
        dataType : 'json',
        success : handleDeleteFalta
      };

      deleteResource('falta', options);
    }


    function deleteParecer(vars){
      var options = {
        url : '',
        dataType : 'json',
        success : handleDeleteParecer
      };

      deleteResource('parecer', options);
    }


    //callback handlers

    function handleDeleteNota(nota){
      console.log('#todo handleDeleteNota');
    }


    function handleDeleteFalta(falta){
      console.log('#todo handleDeleteFalta');
    }


    function handleDeleteParecer(parecer){
      console.log('#todo handleDeleteParecer');
    }


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

        //nota
        var $notaField = $('<input />').addClass('nota-matricula').attr('id', 'nota-matricula-' + value.matricula_id).val(value.nota_atual).attr('maxlength', '4').attr('size', '4');
        $('<td />').html($notaField).appendTo($linha);
        
        //falta
        var $faltaField = $('<input />').addClass('falta-matricula').attr('id', 'falta-matricula-' + value.matricula_id).val(value.falta_atual).attr('maxlength', '4').attr('size', '4');
        $('<td />').html($faltaField).appendTo($linha);

        //parecer
        var $parecerField = $('<textarea />').attr('cols', '40').attr('rows', '2').addClass('parecer-matricula').attr('id', 'parecer-matricula-' + value.matricula_id).val(value.parecer_atual);
        $('<td />').html($parecerField).appendTo($linha);

        $linha.appendTo($resultTable);
      });
      $resultTable.find('tr:even').addClass('even');

      //events
      $('.nota-matricula').on('change', changeNota);
      $('.falta-matricula').on('change', changeFalta);
      $('.parecer-matricula').on('change', changeParecer);

      $('.parecer-matricula').on('focusin', function(event){$(this).attr('rows', '10')});
      $('.parecer-matricula').on('focusout', function(event){$(this).attr('rows', '2')});

    }

    $submitButton.val('Carregar');
    $submitButton.addClass('change-status-on-ajax-change');
    $submitButton.attr('onclick', '');
    $submitButton.click(function(event){
      if (validatesPresenseOfValueInRequiredFields())
      {
        matriculasSearchOptions.url = matriculasSearchUrlBuilder.buildUrl();

        if (window.history && window.history.pushState)
          window.history.pushState('', '', matriculasSearchUrlBuilder.buildUrl('diario'));

        $submitButton.val('Carregando...');
        $resultTable.children().remove();
        $formFilter.submit();
      }
    });

    $formFilter.ajaxForm(matriculasSearchOptions);

  });

})(jQuery);
