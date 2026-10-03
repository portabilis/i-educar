$j(document).ready(function () {
  const optional = $j('#search-postal-code').data('optional') === 1;
  const CEP_FORMAT_REGEX = /[0-9]{5}-[0-9]{3}/;
  const REPEATED_DIGITS_REGEX = /^(.)\1+$/;
  const INVALID_CEPS = ['00000000', '99999999'];

  const ERROR_MESSAGES = {
    INVALID_FORMAT: 'Formato de CEP inválido. Use o formato: 00000-000',
    INVALID_CEP: 'CEP inválido. Por favor, verifique o número digitado.',
    NOT_FOUND: 'CEP não encontrado. Verifique se o número está correto ou preencha o endereço manualmente.',
    SERVER_ERROR: 'Erro ao buscar CEP. Tente novamente ou preencha o endereço manualmente.'
  };

  const disableAddressing = (flag) => {
    $j('#search-postal-code').css('opacity', flag ? 0.5 : 1);
    $j('#address, #number, #complement, #neighborhood, #city_city').attr('disabled', flag);
  };

  const clearPostalCodeError = () => {
    $j('#postal_code').removeClass('error');
    $j('.postal-code-error-message').remove();
  };

  const showPostalCodeError = (message) => {
    $j('#postal_code').addClass('error');
    $j('.postal-code-error-message').remove();

    $j('<span>')
      .addClass('postal-code-error-message error')
      .css({
        'color': '#b94a48',
        'display': 'block',
        'margin-top': '5px',
        'font-size': '12px'
      })
      .text(message)
      .appendTo($j('#postal_code').parent());
  };

  const isInvalidCep = (cleanCep) => {
    return INVALID_CEPS.includes(cleanCep) || REPEATED_DIGITS_REGEX.test(cleanCep);
  };

  const fillAddressFields = (data) => {
    $j('#address').val(data.address);
    $j('#complement').val(data.complement);
    $j('#neighborhood').val(data.neighborhood);
    $j('#city_id').val(data.city.id);
    $j('#city_city').val(`${data.city.id} - ${data.city.name} (${data.state_abbreviation})`);
    clearPostalCodeError();
  };

  const handleApiError = (xhr) => {
    const message = xhr.status === 404 ? ERROR_MESSAGES.NOT_FOUND : ERROR_MESSAGES.SERVER_ERROR;
    showPostalCodeError(message);
    !optional && disableAddressing(true);
  };

  const fetchPostalCode = (cleanCep) => {
    $j('#postal_code_search_loading').css('visibility', 'visible');

    $j.get(`/api/postal-code/${cleanCep}`)
      .done(fillAddressFields)
      .fail(handleApiError)
      .always(() => {
        $j('#postal_code_search_loading').css('visibility', 'hidden');
        !optional && disableAddressing(false);
      });
  };

  const searchCep = () => {
    const postalCode = $j('#postal_code').val();
    clearPostalCodeError();

    if (!CEP_FORMAT_REGEX.test(postalCode)) {
      if (postalCode) {
        showPostalCodeError(ERROR_MESSAGES.INVALID_FORMAT);
      }
      !optional && disableAddressing(true);
      return;
    }

    const cleanCep = postalCode.replace(/\D/g, '');

    if (isInvalidCep(cleanCep)) {
      showPostalCodeError(ERROR_MESSAGES.INVALID_CEP);
      !optional && disableAddressing(true);
      return;
    }

    fetchPostalCode(cleanCep);
  };

  const changePostalCode = () => {
    const postalCode = $j('#postal_code').val();
    const isValid = CEP_FORMAT_REGEX.test(postalCode);

    !postalCode && clearPostalCodeError();
    !optional && disableAddressing(!isValid);
  };

  $j('#search-postal-code').click(searchCep);
  $j('#postal_code').change(changePostalCode);
  $j('#postal_code').keyup(changePostalCode);
});
