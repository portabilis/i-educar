
  if (document.getElementById('btn_enviar')) {
  document.getElementById('btn_enviar').onclick = function() { validaFormulario(); }
}

  function validaFormulario() {
  var c    = 0;
  var loop = true;

  do {
  if (document.getElementById('ref_cod_servidor_substituto_' + c + '_')) {
  if (document.getElementById('ref_cod_servidor_substituto_' + c + '_').value == '') {
  alert('Você deve informar um substituto para cada horário.');

  return;
}
}
  else {
  loop = false;
}

  c++;
} while (loop);

  acao();
}

