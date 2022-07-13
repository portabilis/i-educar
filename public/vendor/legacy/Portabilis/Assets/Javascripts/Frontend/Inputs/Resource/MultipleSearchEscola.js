(function($){
  $(document).ready(function(){

    var $escolaField = getElementFor('escola');
    var $cursoField  = getElementFor('curso');
    var $instituicaoField  = getElementFor('instituicao');

    var handleGetEscolaMultipleSearch = function(response) {
      var selectOptions = response['options'];
      updateChozen($escolaField, selectOptions);
    }

    var updateEscolaMultipleSearch = function(){
      clearValues($escolaField);

        var urlForGetEscolaMultipleSearch = getResourceUrlBuilder.buildUrl('/module/Api/Escola', 'escolas-multiple-search', {
          curso_id : $cursoField.val()
        });

        var options = {
          url : urlForGetEscolaMultipleSearch,
          dataType : 'json',
          success  : handleGetEscolaMultipleSearch
        };

        getResources(options);
    };

    if ($cursoField.length ) {
      $cursoField.change(updateEscolaMultipleSearch);
    } else {
      updateEscolaMultipleSearch();
    }

  }); // ready
})(jQuery);
