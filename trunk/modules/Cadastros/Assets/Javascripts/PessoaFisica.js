// when page is ready

(function($) {
  $(document).ready(function() {

    // hide or show #pais_origem_nome by #tipo_nacionalidade

    var checkTipoNacionalidade = function(){
      if ($j.inArray($j('#tipo_nacionalidade').val(), ['naturalizado_brasileiro', 'estrangeiro']) > -1)
        $j('#pais_origem_nome').show();
      else
        $j('#pais_origem_nome').hide();
    }

    checkTipoNacionalidade();
    $j('#tipo_nacionalidade').change(checkTipoNacionalidade);

  }); // ready
})(jQuery);