
  var anoAtual= new Date().getFullYear();

  after_getEscola = function() {
    var campoAno = document.getElementById('ano').length = 1;
  }

  document.getElementById('ref_cod_escola').onchange = (function(){geraAnos();});

  function geraAnos()
  {
    var campoEscola = document.getElementById('ref_cod_escola');

    var campoAno = document.getElementById('ano');
    campoAno.length = 1;
    campoAno.disabled = true;
    campoAno.options[0].text = 'Carregando ano';

    if(campoEscola.value == '')
    return;

    var xml1 = new ajax(loadFromXML);
    strURL = "educar_escola_ano_letivo_xml.php?esc="+campoEscola.value+"&lim=5&ano_atual="+anoAtual;
    xml1.envia(strURL);
  }

  function loadFromXML(xml)
  {
    var campoAno = document.getElementById('ano');

    var DOM_array = xml.getElementsByTagName( "ano" );

    if(DOM_array.length)
  {
    campoAno.length = 1;
    campoAno.options[0].text = 'Selecione um ano';
    campoAno.disabled = false;

    for( var i = 0; i < DOM_array.length; i++ )
  {
    campoAno.options[campoAno.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].firstChild.data,false,false);
  }
  }
    else
    campoAno.options[0].text = 'A escola não possui nenhum ano letivo';
  }

  geraAnos();

