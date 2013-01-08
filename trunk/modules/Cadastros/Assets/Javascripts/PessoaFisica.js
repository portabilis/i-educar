
// simple search options

var simpleSearchPaisOrigemOptions = {
  placeholder : safeUtf8Decode('Informe o código ou nome do pais de origem')
};

// when page is ready

(function($) {
  $(document).ready(function() {

    // hide or show #pais_origem_nome by #tipo_nacionalidade

    var checkTipoNacionalidade = function(){
      if ($j.inArray($j('#tipo_nacionalidade').val(), ['2', '3']) > -1)
        $j('#pais_origem_nome').show();
      else
        $j('#pais_origem_nome').hide();
    }

    checkTipoNacionalidade();
    $j('#tipo_nacionalidade').change(checkTipoNacionalidade);


    var onClickSubmitEvent = function(event) {
      simpleSearch.fixupRequiredFieldsValidation();

      if (validationUtils.validatesFields())
        document.formcadastro.submit();
    };

    // bind events
    $j('#btn_enviar').removeAttr('onclick');
    $j('#btn_enviar').click(onClickSubmitEvent);

  }); // ready
})(jQuery);

