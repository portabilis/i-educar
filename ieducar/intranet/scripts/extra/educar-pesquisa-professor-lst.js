
  function addVal1(campo,opcao, valor)
  {
    if (window.parent.document.getElementById(campo)) {
    if (window.parent.document.getElementById(campo).type == 'select-one') {
    obj                     = window.parent.document.getElementById(campo);
    novoIndice              = obj.options.length;
    obj.options[novoIndice] = new Option(valor);
    valor                   = obj.options[novoIndice];
    valor.value             = opcao.toString();
    valor.selected          = true;
    obj.onchange();
  }
    else if (window.parent.document.getElementById(campo)) {
    obj       =  window.parent.document.getElementById(campo);
    obj.value = valor;
  }
  }
  }
  function fecha()
  {
    window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
  }
  function setAll(field,value)
  {
    var elements = window.parent.document.getElementsByName(field);
    for (var ct = 0;ct < elements.length; ct++) {
    elements[ct].value = value;
  }
  }
  function clearAll()
  {
    var elements = window.parent.document.getElementsByName('ref_cod_servidor_substituto');
    for (var ct = 0;ct < elements.length;ct++) {
    elements[ct].value = '';
  }
    for (var ct =0;ct < num_alocacao;ct++) {
    var elements = window.parent.document.getElementById('ref_cod_servidor_substituto_' + ct).value='';
  }
  }
  function getArrayHora(hora)
  {
    var array_h;
    if(hora) {
    array_h = hora.split(':');
  }
    else {
    array_h = new Array(0,0);
  }
    return array_h;
  }

