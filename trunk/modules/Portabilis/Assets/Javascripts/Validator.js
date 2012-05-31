function validatesPresenseOfValueInRequiredFields(requiredFields, additionalFields) {
  var emptyFields = [];

  if (! requiredFields)
    requiredFields = document.getElementsByClassName('obrigatorio');

  if (additionalFields)
    requiredFields = requiredFields.concat(additionalFields);

  for (var i = 0; i < requiredFields.length; i++) {
    var requiredField = requiredFields[i];
    if (requiredField.style.display != 'none' && ! requiredField.getAttribute('disabled') && ! requiredField.value) {
      emptyFields.push(requiredField);

      if (requiredField.className.indexOf('error') < 0)
        requiredField.className = requiredField.className + " error";
    }
    else
      requiredField.className = requiredField.className.replace('error', '')
  }

  if (emptyFields.length == 0)
    return true;

  alert('Preencha todos campos obrigat\u00F3rios, antes de continuar.');
  emptyFields[0].focus();
  return false;
}
