

  function getInfraPredio(xml_infra_predio)
  {var campoPredio = document.getElementById('ref_cod_infra_predio');
    var DOM_array = xml_infra_predio.getElementsByTagName( "infra_predio" );

    if(DOM_array.length)
  {
    campoPredio.length = 1;
    campoPredio.options[0].text = 'Selecione um prédio';
    campoPredio.disabled = false;

    for( var i = 0; i < DOM_array.length; i++ )
  {
    campoPredio.options[campoPredio.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_infra_predio"),false,false);
  }
  }
    else
    campoPredio.options[0].text = 'A escola não possui nenhum prédio';

  }

  function getInfraPredioFuncao(xml_infra_comodo_funcao)
  {
    var campoFuncao = document.getElementById('ref_cod_infra_comodo_funcao');
    var DOM_array = xml_infra_comodo_funcao.getElementsByTagName( "infra_comodo_funcao" );

    if(DOM_array.length)
  {
    campoFuncao.length = 1;
    campoFuncao.options[0].text = 'Selecione uma função cômodo';
    campoFuncao.disabled = false;

    for( var i = 0; i < DOM_array.length; i++ )
  {
    campoFuncao.options[campoFuncao.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_infra_comodo_funcao"),false,false);
  }
  }
    else
    campoFuncao.options[0].text = 'A escola não possui nenhuma função cômodo';
  }

  document.getElementById('ref_cod_escola').onchange = function()
  {
    var campoEscola  = document.getElementById('ref_cod_escola').value;

    var campoPredio = document.getElementById('ref_cod_infra_predio');
    campoPredio.length = 1;
    campoPredio.disabled = true;
    campoPredio.options[0].text = 'Carregando prédio';

    var campoFuncao = document.getElementById('ref_cod_infra_comodo_funcao');
    campoFuncao.length = 1;
    campoFuncao.disabled = true;
    campoFuncao.options[0].text = 'Carregando função cômodo';

    var xml_infra_predio = new ajax( getInfraPredio );
    xml_infra_predio.envia( "educar_infra_predio_xml.php?esc="+campoEscola );

    var xml_infra_comodo_funcao = new ajax( getInfraPredioFuncao );
    xml_infra_comodo_funcao.envia( "educar_infra_comodo_funcao_xml.php?esc="+campoEscola );

    if ($F('ref_cod_escola') != '')
  {
    $('img_colecao').style.display = '';
    $('img_colecao2').style.display = '';
  }
    else
  {
    $('img_colecao').style.display = 'none;'
    $('img_colecao2').style.display = 'none;'
  }

  }

  document.getElementById('ref_cod_instituicao').onchange = function()
  {
    getEscola();
    $('img_colecao').style.display = 'none;'
    $('img_colecao2').style.display = 'none;'
  }

  before_getEscola = function()
  {
    var campoPredio = document.getElementById('ref_cod_infra_predio');
    campoPredio.length = 1;
    campoPredio.options[0].text = 'Selecione';
    campoPredio.disabled = false;

    var campoFuncao = document.getElementById('ref_cod_infra_comodo_funcao');
    campoFuncao.length = 1;
    campoFuncao.options[0].text = 'Selecione';
    campoFuncao.disabled = false;
  }


