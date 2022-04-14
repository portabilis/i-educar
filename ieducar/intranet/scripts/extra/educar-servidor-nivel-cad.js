
  function trocaNiveis()
  {
    var campoCategoria = document.getElementById('ref_cod_categoria').value;
    var campoNivel     = document.getElementById('ref_cod_nivel');
    var campoSubNivel  = document.getElementById('ref_cod_subnivel');

    campoNivel.length = 1;
    campoSubNivel.length = 1;

    if (campoCategoria) {
    campoNivel.disabled        = true;
    campoNivel.options[0].text = 'Carregando Níveis';
    var xml = new ajax(atualizaLstNiveis);
    xml.envia('educar_niveis_servidor_xml.php?cod_cat=' + campoCategoria);
  }
    else {
    campoNivel.options[0].text    = 'Selecione uma Categoria';
    campoNivel.disabled           = false;
    campoSubNivel.options[0].text = 'Selecione um Nível';
    campoSubNivel.disabled        = false;
  }
  }

  function atualizaLstNiveis(xml)
  {
    var campoNivel  = document.getElementById('ref_cod_nivel');

    campoNivel.length          = 1;
    campoNivel.options[0].text = 'Selecione uma Categoria';
    campoNivel.disabled        = false;

    var niveis = xml.getElementsByTagName('nivel');

    if (niveis.length) {
    for (var i = 0; i < niveis.length; i++) {
    campoNivel.options[campoNivel.options.length] = new Option( niveis[i].firstChild.data, niveis[i].getAttribute('cod_nivel'),false,false);
  }
  }
    else {
    campoNivel.options[0].text = 'Categoria não possui níveis';
  }
  }

  function trocaSubniveis()
  {
    var campoNivel    = document.getElementById('ref_cod_nivel').value;
    var campoSubNivel = document.getElementById('ref_cod_subnivel');

    campoSubNivel.length = 1;

    if (campoNivel) {
    campoSubNivel.disabled = true;
    campoSubNivel.options[0].text = 'Carregando Subníveis';
    var xml = new ajax(atualizaLstSubiveis);
    xml.envia("educar_subniveis_servidor_xml.php?cod_nivel="+campoNivel);
  }
    else {
    campoSubNivel.options[0].text = 'Selecione uma Nível';
    campoSubNivel.disabled = false;
  }
  }

  function atualizaLstSubiveis(xml)
  {
    var campoSubNivel = document.getElementById('ref_cod_subnivel');

    campoSubNivel.length          = 1;
    campoSubNivel.options[0].text = 'Selecione um Subnível';
    campoSubNivel.disabled        = false;

    var subniveis = xml.getElementsByTagName('subnivel');

    if (subniveis.length) {
    for (var i = 0; i < subniveis.length; i++) {
    campoSubNivel.options[campoSubNivel.options.length] = new Option(
    subniveis[i].firstChild.data, subniveis[i].getAttribute('cod_subnivel'),
    false, false
    );
  }
  }
    else {
    campoNivel.options[0].text = 'Nível não possui subníveis';
  }
  }

  document.getElementById('ref_cod_categoria').onchange = function(){
  trocaNiveis();
}

  document.getElementById('ref_cod_nivel').onchange = function(){
  trocaSubniveis();
}

