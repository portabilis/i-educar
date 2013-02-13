(function($) {
  $(document).ready(function() {

    var $form         = $j('#formcadastro');
    var $submitButton = $j('#btn_enviar');
    var $cpfField     = $j('#id_federal');
    var $cpfNotice    = $j('<span>').html('')
                                    .addClass('error resource-notice')
                                    .hide()
                                    .width($j('#nm_pessoa').outerWidth() - 12)
                                    .appendTo($cpfField.parent());


    var handleGetPersonByCpf = function(dataResponse) {
      handleMessages(dataResponse.msgs);
      $cpfNotice.hide();

      var pessoaId = dataResponse.id;

      if (pessoaId && pessoaId != $j('#cod_pessoa_fj').val()) {
        $cpfNotice.html(stringUtils.toUtf8('CPF já utilizado pela pessoa código ' + pessoaId + ', ')).slideDown('fast');

        $j('<a>').addClass('decorated')
                 .attr('href', '/intranet/atendidos_cad.php?cod_pessoa_fj=' + pessoaId)
                 .attr('target', '_blank')
                 .html('acessar cadastro.')
                 .appendTo($cpfNotice);

        $j('body').animate({ scrollTop: $j('body').offset().top }, 'fast');
      }

      else if ($j(document).data('submit_form_after_ajax_validation'))
        formUtils.submit();
    }


    var getPersonByCpf = function(cpf) {
      var options = {
        url      : getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa'),
        dataType : 'json',
        data     : { cpf : cpf },
        success  : handleGetPersonByCpf,

        // forçado requisições sincronas, evitando erro com requisições ainda não concluidas,
        // como no caso, onde o usuário pressiona cancelar por exemplo.
        async    : false
      };

      getResource(options);
    }


    // hide or show #pais_origem_nome by #tipo_nacionalidade
    var checkTipoNacionalidade = function() {
      if ($j.inArray($j('#tipo_nacionalidade').val(), ['2', '3']) > -1)
        $j('#pais_origem_nome').show();
      else
        $j('#pais_origem_nome').hide();
    }


    var validatesCpf = function() {
      var valid = true;
      var cpf   = $cpfField.val();

      $cpfNotice.hide();

      if (cpf && ! validationUtils.validatesCpf(cpf)) {
        $cpfNotice.html(stringUtils.toUtf8('O CPF informado é inválido')).slideDown('fast');

        // não usado $cpfField.focus(), pois isto prenderia o usuário a página,
        // caso o mesmo tenha informado um cpf invalido e clique em cancelar
        $j('body').animate({ scrollTop: $j('body').offset().top }, 'fast');

        valid = false;
      }

      return valid;
    }


    var validatesUniquenessOfCpf = function() {
      var cpf = $cpfField.val();

      if(cpf && validatesCpf())
        getPersonByCpf(cpf);
    }


    var submitForm = function(event) {

      if ($cpfField.val()) {
        $j(document).data('submit_form_after_ajax_validation', true);
        validatesUniquenessOfCpf();
      }

      else
        formUtils.submit();
    }


    // style fixup

    $('#pais_origem_nome').css('width', '150px');


    // bind events

    checkTipoNacionalidade();
    $j('#tipo_nacionalidade').change(checkTipoNacionalidade);

    $cpfField.focusout(function() {
      $j(document).removeData('submit_form_after_ajax_validation');
      validatesUniquenessOfCpf();
    });

    $submitButton.removeAttr('onclick');
    $submitButton.click(submitForm);

  }); // ready
})(jQuery);