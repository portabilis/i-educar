
  Event.observe(window, 'load', Init, false);

  function Init()
  {
    $('ref_cod_instituicao').value = parent.document.getElementById('ref_cod_instituicao').value;
    $('ref_cod_escola').value = parent.document.getElementById('ref_cod_escola').value;
  }

