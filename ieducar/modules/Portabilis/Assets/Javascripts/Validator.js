validatesIfValueIsInSet
// #TODO rename this file to Validation.js and move functions validates* to object validationUtils

var validationUtils = {
  validatesDate : function(date) {
    return /(^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$)/.test(date);
  },

  validatesLeapYear: function (year) {
    return ((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0);
  },

  validatesDateFieldAlt: function (elm) {
    var $elm = $j(elm),
        val = $elm.val(),
        regex = /[0-3]{1}[0-9]{1}\/[0-1]{1}[0-9]{1}\/[0-9]{4}/,
        match = val.match(regex);

    if (match === null) {
      messageUtils.error('Adicione uma data no seguinte formato: dd/mm/aaaa.', elm);
      return false;
    }

    var parts = val.split('/'),
        dateParts = {
          day: parseInt(parts[0], 10),
          month: parseInt(parts[1], 10),
          year: parseInt(parts[2], 10)
        },
        isLeapYear = this.validatesLeapYear(dateParts.year);

    if (dateParts.month > 12) {
      messageUtils.error('O mês "' + dateParts.month + '" informado não é valido.', elm);

      return false;
    }

    if (dateParts.day > 31) {
      messageUtils.error('O dia "' + dateParts.day + '" não é válido.', elm);

      return false;
    }

    if (
      dateParts.month === 2
      && dateParts.day > 29
      && isLeapYear === true
    ) {
      messageUtils.error('O dia "' + dateParts.day + '" não é válido.', elm);

      return false;
    }

    if (
      dateParts.month === 2
      && dateParts.day > 28
      && isLeapYear === false
    ) {
      messageUtils.error('O dia "' + dateParts.day + '" não é válido em anos não bissextos.', elm);

      return false;
    }

    var module = dateParts.month % 2;

    if (
      dateParts.month <= 7
      && dateParts.month !== 2
      && dateParts.day > 30
      && module === 0
    ) {
      messageUtils.error('O dia "' + dateParts.day + '" não é válido.', elm);

      return false;
    }

    if (
      dateParts.month >= 8
      && dateParts.day > 30
      && module !== 0
    ) {
      messageUtils.error('O dia "' + dateParts.day + '" não é válido.', elm);

      return false;
    }

    return true;
  },

  validatesDateFields : function() {
    var allValid = true;
    var fields = $j("input[id^='data_'][value!=''], input[id^='dt_'][value!='']");

    $j.each(fields, function(index, field) {
      if (! validationUtils.validatesDate(field.value)) {
        messageUtils.error('Informe a data corretamente.', field);
        allValid = false;

        // break jquery loop
        return false;
      }
    });

    return allValid;
  },

  validatesFields : function (validateHiddenFields) {
    return validatesPresenseOfValueInRequiredFields(undefined, undefined, validateHiddenFields) &&
           validationUtils.validatesDateFields();
  },

  validatesCpf : function(cpf) {
    cpf = cpf.replace(/[^0-9]/g, '');

    if (cpf.length != 11)
      return false;

    var cpfsInvalidos = [
      '00000000000',
      '11111111111',
      '22222222222',
      '33333333333',
      '44444444444',
      '55555555555',
      '66666666666',
      '77777777777',
      '88888888888',
      '99999999999'
    ];

    if ($j.inArray(cpf,cpfsInvalidos) != -1) {
      return false;
    }

    var soma;
    var resto;

    // validacao primeiro digito verificador

    soma = 0;
    for (i=1; i<=9; i++)
      soma = soma + parseInt(cpf.substring(i-1, i)) * (11 - i);

    resto = (soma * 10) % 11;

    if ((resto == 10) || (resto == 11))
      resto = 0;

    if (resto != parseInt(cpf.substring(9, 10)) )
      return false;


    // validacao segundo digito verificador

    soma = 0;
    for (i = 1; i <= 10; i++)
      soma = soma + parseInt(cpf.substring(i-1, i)) * (12 - i);

    resto = (soma * 10) % 11;

    if ((resto == 10) || (resto == 11))
      resto = 0;

    if (resto != parseInt(cpf.substring(10, 11) ) )
      return false;

    return true;
  }
};

function validatesPresenseOfValueInRequiredFields(additionalFields, exceptFields, validateHiddenFields) {
  var $emptyFields = [];
  requiredFields = $j('.obrigatorio:not(.skip-presence-validation)');

  if (additionalFields)
    requiredFields = $j.merge(requiredFields, additionalFields);

  if (typeof(simpleSearch) != 'undefined' && typeof(simpleSearch.fixupRequiredFieldsValidation) == 'function')
    simpleSearch.fixupRequiredFieldsValidation();

  for (var i = 0; i < requiredFields.length; i++) {
    var $requiredField = $j(requiredFields[i]);

    if ($requiredField.length > 0 &&
        /*$requiredField.css('display') != 'none' &&*/
        ($requiredField.is(':visible') || validateHiddenFields) &&
        $requiredField.is(':enabled')           &&
        ($requiredField.val() == '' || $requiredField.val() == null)               &&
        $j.inArray($requiredField[0], exceptFields) < 0) {

      $emptyFields.push($requiredField);

      if (! $requiredField.hasClass('error'))
        $requiredField.addClass('error');

      if ($requiredField.is(':hidden,select')) {
        $requiredField.parent().find('ul.chosen-choices').addClass('error');
      }
    }
    else if ($requiredField.length > 0)
      $requiredField.removeClass('error');
  }

  if ($emptyFields.length == 0)
    return true;

  let label = ($emptyFields[0].hasClass('simple-search-id') ? $j('#'+$emptyFields[0].attr('data-for')) : $emptyFields[0]).closest('tr').find('td:first span.form:first').text() || "";
  if (label.length) {
    alert(`Preencha o campo '${label}' corretamente`);
  } else {
    alert('Preencha os campos obrigat\u00F3rios, antes de continuar.');
  }
  $emptyFields.first().focus();
  return false;
}


function validatesIfValueIsInSet(value, targetId, set) {
  /*if (objectUtils.length(set) > 0 && set[value] == undefined) {
    var s = [];

    $j.each(set, function(index, value) {
      s.push(value);
    });

    s = safeSort(s);
    messageUtils.error('Informe um valor que pertença ao conjunto: ' + s.join(', '), targetId);

    return false;
  }*/
  if (value<0 || value>10){
    messageUtils.error('Informe um valor entre 0 à 10', targetId);
    return false;
  }else
    return true;
}


function validatesIfValueIsNumeric(value, targetId) {
  if (! $j.isNumeric(value)) {
    messageUtils.error('Informe um número válido.', targetId);
    return false;
  }

  return true;
}


function validatesIfNumericValueIsInRange(value, targetId, initialRange, finalRange) {
  if (! $j.isNumeric(value) || value < initialRange || value > finalRange) {
    messageUtils.error('Informe um valor entre ' + initialRange + ' e ' + finalRange, targetId);
    return false;
  }

  return true;
}

function validatesIfDecimalPlacesInRange(value, targetId, initialRange, finalRange){
  if (! $j.isNumeric(value) || decimalPlaces(value) < initialRange || decimalPlaces(value) > finalRange) {
    messageUtils.error('Informe um valor com número de casas decimais entre ' + initialRange + ' e ' + finalRange, targetId);
    return false;
  }

  return true;
}

function decimalPlaces(num) {
  var match = (''+num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
  if (!match) { return 0; }
  return Math.max(
       0,
       // Number of digits right of decimal point.
       (match[1] ? match[1].length : 0)
       // Adjust for scientific notation.
       - (match[2] ? +match[2] : 0));
}
