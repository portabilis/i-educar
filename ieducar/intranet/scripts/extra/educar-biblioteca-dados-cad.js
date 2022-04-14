
  document.getElementById('event_incluir_feriado').onclick = incluirFeriado;

  function incluirFeriado(){
  document.getElementById('incluir_feriado').value = 'S';
  document.getElementById('tipoacao').value = '';
  acao();
}

  document.getElementById('event_incluir_dia_semana').onclick = incluirDiaSemana;

  function incluirDiaSemana(){
  document.getElementById('incluir_dia_semana').value = 'S';
  document.getElementById('tipoacao').value = '';
  acao();
}

