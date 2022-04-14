
  var obj_tipo = document.getElementById('tipo');

  obj_tipo.onchange = function()
  {
    if (document.getElementById('tipo').value == 1) {
    setVisibility('tr_qtd_horas', true);
    setVisibility('tr_qtd_min', true);
  }
    else if (document.getElementById( 'tipo' ).value == 2) {
    setVisibility('tr_qtd_horas', false);
    setVisibility('tr_qtd_min', false);
  }
  }

