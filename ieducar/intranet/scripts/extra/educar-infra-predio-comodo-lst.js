
  function getInfraPredioFuncao(xml_infra_comodo_funcao)
  {
    var campoFuncao = document.getElementById('ref_cod_infra_comodo_funcao');
    var DOM_array = xml_infra_comodo_funcao.getElementsByTagName( "infra_comodo_funcao" );

    if(DOM_array.length)
  {
    campoFuncao.length = 1;
    campoFuncao.options[0].text = 'Selecione uma função';
    campoFuncao.disabled = false;

    for( var i = 0; i < DOM_array.length; i++ )
  {
    campoFuncao.options[campoFuncao.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_infra_comodo_funcao"),false,false);
  }
  }
    else
    campoFuncao.options[0].text = 'A escola não possui nenhuma função';
  }

  document.getElementById('ref_cod_escola').onchange = function()
  {

    var campoEscola  = document.getElementById('ref_cod_escola').value;

    var campoFuncao = document.getElementById('ref_cod_infra_comodo_funcao');
    campoFuncao.length = 1;
    campoFuncao.disabled = true;
    campoFuncao.options[0].text = 'Carregando função';

    var xml_infra_comodo_funcao = new ajax( getInfraPredioFuncao );
    xml_infra_comodo_funcao.envia( "educar_infra_comodo_funcao_xml.php?esc="+campoEscola );
  }

  before_getEscola = function()
  {
    var campoFuncao = document.getElementById('ref_cod_infra_comodo_funcao');
    campoFuncao.length = 1;
    campoFuncao.options[0].text = 'Selecione';
    campoFuncao.disabled = false;
  }

