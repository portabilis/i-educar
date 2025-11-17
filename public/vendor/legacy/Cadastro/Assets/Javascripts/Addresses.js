$j(document).ready(function () {
  const optional = $j('#search-postal-code').data('optional') === 1;

  var disableAddressing = function (flag) {
    $j('#search-postal-code').css('opacity', flag ? 0.5 : 1);
    $j('#address').attr('disabled', flag);
    $j('#number').attr('disabled', flag);
    $j('#complement').attr('disabled', flag);
    $j('#neighborhood').attr('disabled', flag);
    $j('#city_city').attr('disabled', flag);
  };

  var searchCep = function () {
    var postalCode = $j('#postal_code').val();
    var regexp = /[0-9]{5}\-[0-9]{3}/;
    var valid = regexp.test(postalCode);

    // Remove mensagem de erro anterior, se existir
    $j('#postal_code').removeClass('error');
    $j('.postal-code-error-message').remove();

    if (valid) {
      var cleanPostalCode = postalCode.replace(/\D/g, '');

      // Validar se não é um CEP obviamente inválido
      if (cleanPostalCode === '00000000' || cleanPostalCode === '99999999' || /^(.)\1+$/.test(cleanPostalCode)) {
        showPostalCodeError('CEP inválido. Por favor, verifique o número digitado.');
        if (!optional) {
          disableAddressing(true);
        }
        return;
      }

      $j('#postal_code_search_loading').css('visibility', 'visible');

      $j.get('/api/postal-code/' + cleanPostalCode)
        .done(function (res) {
          $j('#address').val(res.address);
          $j('#complement').val(res.complement);
          $j('#neighborhood').val(res.neighborhood);
          $j('#city_id').val(res.city.id);
          $j('#city_city').val(res.city.id + ' - ' + res.city.name + ' (' + res.state_abbreviation + ')');

          // Remove classe de erro em caso de sucesso
          $j('#postal_code').removeClass('error');
        })
        .fail(function (xhr) {
          // Exibir mensagem de erro quando CEP não é encontrado
          if (xhr.status === 404) {
            showPostalCodeError('CEP não encontrado. Verifique se o número está correto ou preencha o endereço manualmente.');
          } else {
            showPostalCodeError('Erro ao buscar CEP. Tente novamente ou preencha o endereço manualmente.');
          }

          if (!optional) {
            disableAddressing(true);
          }
        })
        .always(function() {
          $j('#postal_code_search_loading').css('visibility', 'hidden');
          if (!optional) {
            disableAddressing(false);
          }
        });
    } else {
      // Formato de CEP inválido
      if (postalCode && postalCode.length > 0) {
        showPostalCodeError('Formato de CEP inválido. Use o formato: 00000-000');
      }
      if (!optional) {
        disableAddressing(true);
      }
    }
  };

  var showPostalCodeError = function(message) {
    $j('#postal_code').addClass('error');

    // Remove mensagem anterior se existir
    $j('.postal-code-error-message').remove();

    // Adiciona nova mensagem de erro
    var errorMessage = $j('<span>')
      .addClass('postal-code-error-message error')
      .css({
        'color': '#b94a48',
        'display': 'block',
        'margin-top': '5px',
        'font-size': '12px'
      })
      .text(message);

    $j('#postal_code').parent().append(errorMessage);
  };

  var changePostalCode = function () {
    var postalCode = $j('#postal_code').val();
    var regexp = /[0-9]{5}\-[0-9]{3}/;
    var valid = regexp.test(postalCode);

    // Limpar mensagem de erro ao digitar
    if (postalCode.length === 0) {
      $j('#postal_code').removeClass('error');
      $j('.postal-code-error-message').remove();
    }

    if (!optional) {
      disableAddressing(!valid);
    }
  };

  $j('#search-postal-code').click(searchCep);
  $j('#postal_code').change(changePostalCode);
  $j('#postal_code').keyup(changePostalCode);
});
