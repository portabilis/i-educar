(function($){
  $(document).ready(function(){

    var $escolaField = getElementFor('escola');
    var $cursoField  = getElementFor('curso');

    var handleGetEscolaCurso = function(response) {
      var selectOptions = response['options'];
      updateChozen($escolaField, selectOptions);
    }

    var updateEscolaCurso = function(){
      clearValues($escolaField);
      if ($cursoField.val()) {

        var urlForGetEscolaCurso = getResourceUrlBuilder.buildUrl('/module/Api/Escola', 'escolas-curso', {
          curso_id : $cursoField.val()
        });

        var options = {
          url : urlForGetEscolaCurso,
          dataType : 'json',
          success  : handleGetEscolaCurso
        };

        getResources(options);
      }
    };

    // bind onchange event
    $cursoField.change(updateEscolaCurso);

  }); // ready
})(jQuery);
