(function($){
  $(document).ready(function(){

    var instituicaoId = '#ref_cod_instituicao';
    var $instituicaoField = $(instituicaoId);

    var updateEscolas = function(){
      var ApiUrlBase = 'educar_escola_xml2.php';
      var $escolaField = $('#ref_cod_escola');
      disableElement($escolaField);

      if ($instituicaoField.val()) {
        // TODO load escolas via ajax
        getEscolasUrl = getResourceUrlBuilder.buildUrl(ApiUrlBase, 'matriculas', {ins : $instituicaoField.val()});
        console.log(getEscolasUrl);

        var escolas = [{id : 1, value : 'Escola 1', checked : false},
                       {id : 2, value : 'Escola 2', checked : true}];

        updateSelect($escolaField, escolas);
      }
    };

    $instituicaoField.change(updateEscolas);

  }); // ready
})(jQuery);
