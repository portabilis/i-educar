
  function addSel1( campo, valor, texto )
  {
    obj = window.parent.document.getElementById( campo );
    novoIndice = obj.options.length;
    obj.options[novoIndice] = new Option( texto );
    opcao = obj.options[novoIndice];
    opcao.value = valor;
    opcao.selected = true;
    setTimeout( "obj.onchange", 100 );
  }

  function addVal1( campo,valor )
  {

    obj =  window.parent.document.getElementById( campo );
    obj.value = valor;
  }

  function fecha()
  {
    window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
    //window.parent.document.forms[0].submit();
  }

