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
