(function($){
  $(document).ready(function(){

    var escolaField           = getElementFor('escola');
    var serieField            = getElementFor('serie');
    var turmaField            = getElementFor('turma');
    var areaConhecimentoField = getElementFor('areaconhecimento');

    var handleGetAreaConhecimento = function(response) {
      clearValues(areaConhecimentoField);
      var selectOptions = response['options'];
      updateChozen(areaConhecimentoField, selectOptions);
    }

    var updateAreaConhecimento = function(){
      clearValues(areaConhecimentoField);
      var urlForGetAreaConhecimento = null;

      if (turmaField.val()) {
        urlForGetAreaConhecimento = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areaconhecimento-turma', {
          turma_id  : turmaField.val()
        });
      }else if (escolaField.val() && serieField.val()) {
        urlForGetAreaConhecimento = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areaconhecimento-escolaserie', {
          escola_id : escolaField.val(),
          serie_id  : serieField.val()
        });
      }

      if (urlForGetAreaConhecimento != null){
        var options = {
          url : urlForGetAreaConhecimento,
          dataType : 'json',
          success  : handleGetAreaConhecimento
        };

        getResources(options);
      }
    };

    // bind onchange event
    serieField.change(updateAreaConhecimento);
    turmaField.change(updateAreaConhecimento);

  }); // ready
})(jQuery);
