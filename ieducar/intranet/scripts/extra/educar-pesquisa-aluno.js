

  function addVal1( campo, valor )
  {
    if( !window.parent.document.getElementById( campo ) )
    return;
    obj       =  window.parent.document.getElementById( campo );
    obj.value = valor;
  }

  function fecha()
  {
    window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
    if( window.parent.document.getElementById('passo') ) {
        window.parent.document.getElementById('passo').value = 2;
        window.parent.document.forms[0].submit();
     }
  }

