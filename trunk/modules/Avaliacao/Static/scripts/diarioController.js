var $j = jQuery.noConflict();

(function($) {

  $(function(){

    var diarioUrlBase = 'diario';
    var diarioAjaxUrlBase = 'diarioAjax';

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

    var resourceUrlBuilder = {
      buildUrl : function(urlBase, vars){

        _vars = '';
        for(varName in vars){
          _vars += '&'+varName+'='+vars[varName];
        }

        return urlBase + '?' + _vars;
      }
    };

  
    var deleteResourceUrlBuilder = {
      buildUrl : function(urlBase, resourceName, additionalVars){

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

        return resourceUrlBuilder.buildUrl(urlBase, $.extend(vars, additionalVars));

      }
    };


    var postResourceUrlBuilder = {
      buildUrl : function(urlBase, resourceName, additionalVars){

        var vars = {
          att : resourceName,
          oper : 'post',
          instituicao_id : $j('#ref_cod_instituicao').val(),
          escola_id : $j('#ref_cod_escola').val(),
          curso_id : $j('#ref_cod_curso').val(),
          serie_id : $j('#ref_ref_cod_serie').val(),
          turma_id : $j('#ref_cod_turma').val(),
          ano_escolar : $j('#ano_escolar').val(),
          componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
          etapa : $j('#etapa').val(),
          matricula_id : $j('#etapa').val()
        };

        return resourceUrlBuilder.buildUrl(urlBase, $.extend(vars, additionalVars));

      }
    };


    var getResourceUrlBuilder = {
      buildUrl : function(urlBase, resourceName, additionalVars){

        var vars = {
          att : resourceName,
          oper : 'get',
          instituicao_id : $j('#ref_cod_instituicao').val(),
          escola_id : $j('#ref_cod_escola').val(),
          curso_id : $j('#ref_cod_curso').val(),
          serie_id : $j('#ref_ref_cod_serie').val(),
          turma_id : $j('#ref_cod_turma').val(),
          ano_escolar : $j('#ano_escolar').val(),
          componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
          etapa : $j('#etapa').val()
        };

        return resourceUrlBuilder.buildUrl(urlBase, $.extend(vars, additionalVars));

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
      var options = {
        url : postResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, 'nota', {matricula_id : $notaFieldElement.data('matricula_id'),}),
        dataType : 'json',
        data : {att_value : $notaFieldElement.val()},
        success : handlePostNota
      };

      $.ajax(options).error(handleErrorPost).complete(function(){console.log('post completado...')});
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


    function deleteNota($notaFieldElement){

      var resourceName = 'nota';

      var options = {
        url : deleteResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, resourceName),
        dataType : 'json',
        success : handleDeleteNota
      };

      deleteResource(resourceName, options);
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

    function handleErrorPost(request){
      console.log('#todo handleError');
      console.log(request);
    }

    function handleGetNota(nota){
      console.log('#todo handleGetNota');
      console.log(nota);
    }


    function handleGetFalta(falta){
      console.log('#todo handleGetFalta');
    }


    function handleGetParecer(parecer){
      console.log('#todo handleGetParecer');
    }


    function handlePostNota(dataResponse){
      //#TODO pintar campo de verde caso não tenha msgs de erro
      //#TODO pintar campo de vermelho caso tenha msgs de erro
      //console.log(dataResponse);
      handleMessages(dataResponse.msgs);
    }


    function handlePostFalta(falta){
      console.log('#todo handlePostFalta');
    }


    function handlePostParecer(parecer){
      console.log('#todo handlePostParecer');
    }


    function handleDeleteNota(nota){
      console.log('#todo handleDeleteNota');
      console.log(nota);
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
        var $notaField = $('<input />').addClass('nota-matricula').attr('id', 'nota-matricula-' + value.matricula_id).val(value.nota_atual).attr('maxlength', '4').attr('size', '4').data('matricula_id', value.matricula_id);
        $('<td />').html($notaField).appendTo($linha);
        
        //falta
        var $faltaField = $('<input />').addClass('falta-matricula').attr('id', 'falta-matricula-' + value.matricula_id).val(value.falta_atual).attr('maxlength', '4').attr('size', '4').data('matricula_id', value.matricula_id);
        $('<td />').html($faltaField).appendTo($linha);

        //parecer
        var $parecerField = $('<textarea />').attr('cols', '40').attr('rows', '2').addClass('parecer-matricula').attr('id', 'parecer-matricula-' + value.matricula_id).val(value.parecer_atual).data('matricula_id', value.matricula_id);
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
        matriculasSearchOptions.url = getResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, 'matriculas');

        if (window.history && window.history.pushState)
          window.history.pushState('', '', getResourceUrlBuilder.buildUrl(diarioUrlBase, 'matriculas'));

        $submitButton.val('Carregando...');
        $resultTable.children().remove();
        $formFilter.submit();
      }
    });

    $formFilter.ajaxForm(matriculasSearchOptions);

  });

})(jQuery);
