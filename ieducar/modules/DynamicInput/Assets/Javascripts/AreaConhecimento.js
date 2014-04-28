(function($){
  $(document).ready(function(){

    // area reconhecimento necessita do numero da instituicao e da turma
    var $instituicaoField = getElementFor('instituicao');
    var $areaConhecimentoField = getElementFor('area_conhecimento');
    var $turmaField = getElementFor('turma');

    var handleGetAreasConhecimento = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($areaConhecimentoField, selectOptions, "Todas");
    }

    var updateAreasConhecimento = function(){
      resetSelect($areaConhecimentoField);

      if ($turmaField.val()) {
        $areaConhecimentoField.children().first().html('Aguarde carregando...');

        var urlForGetAreasConhecimento = getResourceUrlBuilder.buildUrl('/module/DynamicInput/AreaConhecimento', 'area_conhecimento', {
          instituicao_id   : $instituicaoField.val(),
          turma_id         : $turmaField.val()
        });

        var options = {
          url : urlForGetAreasConhecimento,
          dataType : 'json',
          success  : handleGetAreasConhecimento
        };

        getResources(options);
      }

      $turmaField.change();
    };

    // bind onchange event
    $turmaField.change(updateAreasConhecimento);

  }); // ready
})(jQuery);
