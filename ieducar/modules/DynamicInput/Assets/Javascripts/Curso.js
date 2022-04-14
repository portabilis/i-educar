(function($){
  $(document).ready(function() {
    let $instituicaoField = getElementFor('instituicao');
    let $escolaField = getElementFor('escola');
    let $cursoField = getElementFor('curso');
    let $ano = getElementFor('ano');

    let handleGetCursos = function(response) {
      let selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($cursoField, selectOptions, "Selecione um curso");
    }

    let updateCursos = function() {
      resetSelect($cursoField);

      if ($instituicaoField.val() && $escolaField.val() && $escolaField.is(':enabled')) {
        $cursoField.children().first().html('Aguarde carregando...');
        let urlForGetCursos = getResourceUrlBuilder.buildUrl('/module/DynamicInput/Curso', 'cursos', {
          escola_id: $escolaField.attr('value'),
          instituicao_id: $instituicaoField.attr('value'),
          ano: ($ano.val() && $ano.val() != "NaN" ? $ano.val() : '')
        });

        let options = {
          url: urlForGetCursos,
          dataType: 'json',
          success: handleGetCursos
        };

        getResources(options);
      }

      $cursoField.change();
    };

    $escolaField.change(updateCursos);
    $ano.change(function () {

      // Evita que o select "curso" tenha seus valores limpos ao alterar o campo ano
      if ($cursoField.attr('data-refresh-ano') === 'false') {
        return;
      }

      updateCursos();
    });
  });
})(jQuery);
