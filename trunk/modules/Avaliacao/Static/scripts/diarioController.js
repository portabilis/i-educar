var $j = jQuery.noConflict();

(function($) {

  $(function(){

    //global ajax events
    $('.change-visibility-on-ajax-change')
      .ajaxStart(function() { $(this).show(); })
      .ajaxStop(function() { $(this).hide(); });

    console.log('pronto');

    //ao submeter form carregar matriculas, notas, faltas e pareceres
        //ao mudar nota, falta ou parecer enviar postar, e tratar retorno


  function handleMatriculasSearch(dataResponse) { 
    $.each(dataResponse, function(index, value){
      var $table = $('.tablelistagem');
      var $linha = $('<tr />');
      
      $('<td />').html(index +": "+ value).appendTo($linha);

      $linha.appendTo($table);

    });
  }

  var matriculasSearchOptions = {
    url : 'module/Avaliacao/diarioAjax',
    dataType : 'json',
    success : handleMatriculasSearch
  };

  $('#ajaxForm').ajaxForm(matriculasSearchOptions);

  });

})(jQuery);
