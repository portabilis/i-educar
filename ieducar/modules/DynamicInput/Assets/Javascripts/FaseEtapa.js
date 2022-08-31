(function($){
  $(document).ready(function(){

    var $turmaField = getElementFor('turma');
    var $etapaField = getElementFor('fase_etapa');

    var handleGetEtapas = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      let optionSelected = response['selected'];
      updateSelect($etapaField, selectOptions, "Selecione uma etapa", optionSelected);
    }

    var updateEtapas = function(){
      resetSelect($etapaField);

      if ($turmaField.val() && $turmaField.is(':enabled')) {

        let dataInicialField = document.getElementById('data_inicial'); //plano de aula
        let dataField = document.getElementById('data'); //frequencia
        let valueData = '';

        if (dataInicialField) {
          valueData = dataInicialField.value;
        }

        if (dataField) {
          valueData = dataField.value;
        }

        $etapaField.children().first().html('Aguarde, carregando...');

        var data = {
          turma_id  : $turmaField.attr('value'),
          data_inicial : valueData
        };

        var urlForGetEtapas = getResourceUrlBuilder.buildUrl('/module/DynamicInput/FaseEtapa',
                                                             'etapas', data);
        var options = {
          url      : urlForGetEtapas,
          dataType : 'json',
          success  : handleGetEtapas
        };

        getResources(options);
      }

      $etapaField.change();
    };

    // bind onchange event
    $turmaField.change(updateEtapas);

  }); // ready
})(jQuery);
