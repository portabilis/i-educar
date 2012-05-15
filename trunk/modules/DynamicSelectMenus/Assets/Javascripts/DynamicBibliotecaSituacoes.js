(function($){
  $(document).ready(function(){
    var $bibliotecaField = $('#ref_cod_biblioteca');
    var $situacaoField   = $('#ref_cod_situacao');

    var handleGetSituacoes = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_situacao');
      updateSelect($situacaoField, selectOptions, "Selecione uma situa&ccedil;&atilde;o");
    }

    var updateSituacoes = function(){
      resetSelect($situacaoField);

      if ($bibliotecaField.val() && $bibliotecaField.is(':enabled')) {
        var urlForGetSituacoes = getResourceUrlBuilder.buildUrl('educar_situacao_xml.php', '', {
                                                       bib : $bibliotecaField.attr('value') });

        var options = {
          url : urlForGetSituacoes,
          dataType : 'xml',
          success  : handleGetSituacoes
        };

        getResources(options, handleGetSituacoes);
      }

      $situacaoField.change();
    };

    // bind onchange event
    $bibliotecaField.change(updateSituacoes);

  }); // ready
})(jQuery);
