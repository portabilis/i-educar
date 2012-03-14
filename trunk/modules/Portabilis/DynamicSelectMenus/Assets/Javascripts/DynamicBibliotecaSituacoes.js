(function($){
  $(document).ready(function(){
    var $bibliotecaField = $('#ref_cod_biblioteca');
    var $situacaoField   = $('#ref_cod_situacao');

    var handleGetSituacoes = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_situacao');
      updateSelect($situacaoField, selectOptions);
    }

    var updateSituacoes = function(){
      $situacaoField.attr('disabled', 'disabled');

      if ($bibliotecaField.val() && $bibliotecaField.is(':enabled')) {
        getSituacoesUrl = getResourceUrlBuilder.buildUrl('educar_situacao_xml.php', '', {
                                                       bib : $bibliotecaField.attr('value') });

        var options = {
          url : getSituacoesUrl,
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
