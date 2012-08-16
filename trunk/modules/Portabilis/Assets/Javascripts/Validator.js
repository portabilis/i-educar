function validatesPresenseOfValueInRequiredFields(additionalFields, exceptFields) {
  var $emptyFields = [];
  requiredFields = document.getElementsByClassName('obrigatorio');

  if (additionalFields)
    requiredFields = requiredFields.concat(additionalFields);

  for (var i = 0; i < requiredFields.length; i++) {
    var $requiredField     = $j(requiredFields[i]);

    if ($requiredField.length > 0 &&
        $requiredField.css('display') != 'none' &&
        $requiredField.is(':enabled')           &&
        $requiredField.val() == ''              &&
        $j.inArray($requiredField[0], exceptFields) < 0) {

      $emptyFields.push($requiredField);

      if (! $requiredField.hasClass('error'))
        $requiredField.addClass('error');
    }
    else if ($requiredField.length > 0)
      $requiredField.removeClass('error');
  }

  if ($emptyFields.length == 0)
    return true;

  alert('Preencha todos campos obrigat\u00F3rios, antes de continuar.');
  $emptyFields.first().focus();
  return false;
}


function validatesIfValueIsInSet(value, targetId, set) {
  if (set[value] == undefined) {
    var s = [];

    $j.each(set, function(index, value) {
      s.push(value);
    });

    s = safeSort(s);
    handleMessages([{type : 'error', msg : 'Informe um valor que pertença ao conjunto: ' + s.join(', ')}], targetId);

    return false;
  }

  return true;
}


function validatesIfValueIsNumeric(value, targetId) {
  if (! $j.isNumeric(value)) {
    handleMessages([{type : 'error', msg : safeUtf8Decode('Informe um numero válido.')}], targetId);
    return false;
  }

  return true;
}  


function validatesIfNumericValueIsInRange(value, targetId, initialRange, finalRange) {
  if (! $j.isNumeric(value) || value < initialRange || value > finalRange) {
    handleMessages([{type : 'error', msg : 'Informe um valor entre ' + initialRange + ' e ' + finalRange}], targetId);
    return false;
  }

  return true;
}
