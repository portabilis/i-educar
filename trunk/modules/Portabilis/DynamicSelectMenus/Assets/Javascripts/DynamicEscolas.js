(function($){
  $(document).ready(function(){
    var instituicaoId = '#ref_cod_instituicao';
    var $instituicaoField = $(instituicaoId);

    var updateEscolas = function(){
      var $escolaField = $('#ref_cod_escola');
      disableElement($escolaField);

      // TODO load escolas via ajax
      var escolas = [{id : 1, value : 'Escola 1', checked : false},
                     {id : 2, value : 'Escola 2', checked : true}];

      updateSelect($escolaField, escolas);
    };

    $instituicaoField.change(updateEscolas);
  });

})(jQuery);
