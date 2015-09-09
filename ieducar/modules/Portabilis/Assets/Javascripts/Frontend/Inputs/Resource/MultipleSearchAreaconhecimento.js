(function($){
  $(document).ready(function(){

    var $escolaField           = getElementFor('escola');
    var $serieField            = getElementFor('serie');
    var $areaConhecimentoField = getElementFor('areaconhecimento');

    var handleGetAreaConhecimento = function(response) {
      var selectOptions = response['options'];
      updateChozen($areaConhecimentoField, selectOptions);
    }

    var updateAreaConhecimento = function(){
      clearValues($areaConhecimentoField);
      if ($escolaField.val() && $serieField.val()) {

        var urlForGetAreaConhecimento = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areaconhecimento-escolaserie', {
          escola_id : $escolaField.val(),
          serie_id  : $serieField.val()
        });

        var options = {
          url : urlForGetAreaConhecimento,
          dataType : 'json',
          success  : handleGetAreaConhecimento
        };

        getResources(options);
      }
    };

    // bind onchange event
    $serieField.change(updateAreaConhecimento);

  }); // ready
})(jQuery);
