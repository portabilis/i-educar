(function($){
  $(document).ready(function(){
    var $instituicaoField = getElementFor('instituicao');
    var $escolaField = getElementFor('escola');

    $escolaField.chosen({
      no_results_text: "Sem resultados para",
      placeholder_text_single: "Selecione uma escola",
      search_contains: true,
    });

    var handleGetEscolas = function(response) {
      $escolaField.empty();
      $escolaField.trigger("chosen:updated");

      options = '<option/>';

      $j.each(response['options'], function(id, value) {
        if (id.indexOf && id.substr && id.indexOf('__') == 0) {
          id = id.substr(2);
        }
        options += '<option value="' + id + '"> ' + value + '</option>';;
      });

      $escolaField.append(options);
      $escolaField.trigger("chosen:updated");
    }

    var updateEscolas = function() {
      var additionalVars = {
        instituicao : $instituicaoField.val()
      };
      var options = {
        url      : getResourceUrlBuilder.buildUrl('/module/Api/escola', 'escolas-para-selecao-sem-filtro-por-usuario', additionalVars),
        dataType : 'json',
        data     : {},
        success  : handleGetEscolas,
      };
      getResource(options);
    }

    $instituicaoField.change(updateEscolas);

  }); // ready
})(jQuery);
