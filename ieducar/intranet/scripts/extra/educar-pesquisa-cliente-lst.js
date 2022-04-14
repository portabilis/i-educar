function addVal1(campo, valor, opcao) {
  if (window.parent.document.getElementById(campo).type == "select-one") {
    obj = window.parent.document.getElementById(campo);
    novoIndice = obj.options.length;
    obj.options[novoIndice] = new Option(opcao);
    opcao = obj.options[novoIndice];
    opcao.value = valor;
    opcao.selected = true;
    obj.onchange();
  } else if (window.parent.document.getElementById(campo)) {
    obj = window.parent.document.getElementById(campo);
    obj.value = valor;

  }
}

function fecha() {
  window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
}
