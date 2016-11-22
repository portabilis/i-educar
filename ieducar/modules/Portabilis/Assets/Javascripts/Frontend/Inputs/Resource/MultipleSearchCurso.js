(function($){
  $(document).ready(function(){

    var $cursoField  = getElementFor('curso');
    var $instituicaoField  = getElementFor('instituicao');

    var handleGetCursoMultipleSearch = function(response) {
      var selectOptions = response['options'];
      updateChozen($cursoField, selectOptions);
    }

    var updateCursoMultipleSearch = function(){
      clearValues($cursoField);

        var urlForGetCursoMultipleSearch = getResourceUrlBuilder.buildUrl('/module/Api/Curso', 'cursos-multiple-search', {
          instituicao_id : $instituicaoField.val()
        });

        var options = {
          url : urlForGetCursoMultipleSearch,
          dataType : 'json',
          success  : handleGetCursoMultipleSearch
        };

        getResources(options);
    };

    if ($instituicaoField.length ) {
      $instituicaoField.on('change', updateCursoMultipleSearch);
    } else {
      updateCursoMultipleSearch();
    }

  }); // ready
})(jQuery);
