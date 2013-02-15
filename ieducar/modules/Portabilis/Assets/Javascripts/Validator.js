
// #TODO rename this file to Validation.js and move functions validates* to object validationUtils

var validationUtils = {
  validatesDate : function(date) {
    return /(((0[1-9]|[12][0-9])\/(02))|((0[1-9]|[12][0-9]|(30))\/(0[4689]|(11)))|((0[1-9]|[12][0-9]|3[01])\/(0[13578]|(10)|(12))))\/[1-2][0-9]{3}/.test(date);
  },

  validatesDateFields : function() {
    var allValid = true;
    var fields   = $j("input[id^='data_'][value!=''], input[id^='dt_'][value!='']");

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

  validatesFields : function () {
    return validatesPresenseOfValueInRequiredFields() &&
           validationUtils.validatesDateFields();
  },

  validatesCpf : function(cpf) {
    cpf = cpf.replace(/[^0-9]/g, '');

    if (cpf.length != 11)
      return false;

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

function validatesPresenseOfValueInRequiredFields(additionalFields, exceptFields) {
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
        $requiredField.is(':visible')           &&
        $requiredField.is(':enabled')           &&
        $requiredField.val() == ''              &&
        $j.inArray($requiredField[0], exceptFields) < 0) {

      $emptyFields.push($requiredField);

      if (! $requiredField.hasClass('error'))
        $requiredField.addClass('error');

      messageUtils.removeStyle($requiredField);
    }
    else if ($requiredField.length > 0)
      $requiredField.removeClass('error');
  }

  if ($emptyFields.length == 0)
    return true;

  alert('Preencha os campos obrigat\u00F3rios, antes de continuar.');
  $emptyFields.first().focus();
  return false;
}


function validatesIfValueIsInSet(value, targetId, set) {
  if (objectUtils.length(set) > 0 && set[value] == undefined) {
    var s = [];

    $j.each(set, function(index, value) {
      s.push(value);
    });

    s = safeSort(s);
    messageUtils.error('Informe um valor que pertença ao conjunto: ' + s.join(', '), targetId);

    return false;
  }

  return true;
}


function validatesIfValueIsNumeric(value, targetId) {
  if (! $j.isNumeric(value)) {
    messageUtils.error('Informe um numero válido.', targetId);
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
