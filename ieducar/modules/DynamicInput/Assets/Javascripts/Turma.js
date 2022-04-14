(function($){
  $(document).ready(function(){

    // turma search expect an id for escola
    var $instituicaoField = getElementFor('instituicao');
    var $escolaField      = getElementFor('escola');

    var $serieField = getElementFor('serie');
    var $turmaField = getElementFor('turma');
    var $ano        = getElementFor('ano');

    var naoFiltrarAno        = $j('#nao_filtrar_ano').length > 0 ? 1 : false;
    var anoEmAndamento       = $j('#ano_em_andamento').length > 0 ? 1 : false;

    var handleGetTurmas = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($turmaField, selectOptions, "Selecione uma turma");
    }

    var updateTurmas = function(){
      resetSelect($turmaField);

      if ($instituicaoField.val() && $escolaField.val() && $serieField.val() && $serieField.is(':enabled')) {
        $turmaField.children().first().html('Aguarde, carregando...');

        var urlForGetTurmas = getResourceUrlBuilder.buildUrl('/module/DynamicInput/turma', 'turmas', {
          instituicao_id   : $instituicaoField.val(),
          escola_id        : $escolaField.val(),
          serie_id         : $serieField.val(),
          ano              : naoFiltrarAno ? null : $ano.val(),
          nao_filtrar_ano  :  naoFiltrarAno ? 1 : null,
          ano_em_andamento :  anoEmAndamento ? 1 : null
        });

        var options = {
          url : urlForGetTurmas,
          dataType : 'json',
          success  : handleGetTurmas
        };

        getResources(options);
      }

      $turmaField.change();
    };

    // bind onchange event
    $serieField.change(updateTurmas);

  }); // ready
})(jQuery);
