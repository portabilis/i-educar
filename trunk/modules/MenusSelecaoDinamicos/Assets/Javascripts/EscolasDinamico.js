//var $j = jQuery.noConflict();

(function($){

  $(document).ready(function(){
    var $instituicaoField = $('#ref_cod_instituicao');
    var $targetField = $('#ref_cod_escola');

    var updateEscolas = function(){
      var instituicaoId = $instituicaoField.attr('id');

      // TODO load escolas
      var escolas = '';

      // TODO atualizar select com novas escolas
      // metodo updateSelect pode estar em um js generico na mesma pasta
      // updateSelect($targetField, escolas)
      console.log(instituicaoId);
    };

    $instituicaoField.change(updateEscolas);
  });

})(jQuery);
