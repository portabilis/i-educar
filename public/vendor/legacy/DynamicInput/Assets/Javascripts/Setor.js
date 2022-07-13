(function($){
  $(document).ready(function(){

    var $setorField = getElementFor('id_setor');
    var handleGetSetor = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($setorField, selectOptions, "Selecione um setor");
    }

    var montaSetor = function(){

        var urlForGetSetor = getResourceUrlBuilder.buildUrl('/module/DynamicInput/Setor',
                                                                 'setor');

        var options = {
          url      : urlForGetSetor,
          dataType : 'json',
          success  : handleGetSetor
        };

        getResources(options);

      $setorField.change();
    };

    montaSetor();

  }); // ready
})(jQuery);