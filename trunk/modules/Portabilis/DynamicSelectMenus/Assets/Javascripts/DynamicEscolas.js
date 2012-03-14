(function($){
  $(document).ready(function(){
    var $instituicaoField = $('#ref_cod_instituicao');
    var $escolaField      = $('#ref_cod_escola');

    var handleGetEscolas = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_escola');
      updateSelect($escolaField, selectOptions);
    }

    var updateEscolas = function(){
      $escolaField.attr('disabled', 'disabled');

      if ($instituicaoField.val() && $instituicaoField.is(':enabled')) {
        getEscolasUrl = getResourceUrlBuilder.buildUrl('educar_escola_xml2.php', '', {
                                                       ins : $instituicaoField.attr('value') });

        var options = {
          url : getEscolasUrl,
          dataType : 'xml',
          success  : handleGetEscolas
        };

        getResources(options, handleGetEscolas);
      }

      $escolaField.change();
    };

    // bind onchange event
    $instituicaoField.change(updateEscolas);

  }); // ready
})(jQuery);
