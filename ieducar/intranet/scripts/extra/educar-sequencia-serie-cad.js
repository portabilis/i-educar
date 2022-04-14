

  function getCurso(xml_curso)
  {
    var campoCurso = document.getElementById('ref_curso_origem');
    var campoCurso_ = document.getElementById('ref_curso_destino');
    var DOM_array = xml_curso.getElementsByTagName( "curso" );

    if(DOM_array.length)
  {
    campoCurso.length = 1;
    campoCurso.options[0].text = 'Selecione um curso origem';
    campoCurso.disabled = false;

    campoCurso_.length = 1;
    campoCurso_.options[0].text = 'Selecione um curso destino';
    campoCurso_.disabled = false;

    for( var i = 0; i < DOM_array.length; i++ )
  {
    campoCurso.options[campoCurso.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
    campoCurso_.options[campoCurso_.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
  }
  }
    else
  {
    campoCurso.options[0].text = 'A instituição não possui nenhum curso';
    campoCurso_.options[0].text = 'A instituição não possui nenhum curso';
  }
  }

  function getSerie(xml_serie)
  {
    var campoSerie = document.getElementById('ref_serie_origem');
    var DOM_array = xml_serie.getElementsByTagName( "serie" );

    if(DOM_array.length)
  {
    campoSerie.length = 1;
    campoSerie.options[0].text = 'Selecione uma série origem';
    campoSerie.disabled = false;

    for( var i = 0; i < DOM_array.length; i++ )
  {
    campoSerie.options[campoSerie.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_serie"),false,false);
  }
  }
    else
    campoSerie.options[0].text = 'O curso origem não possui nenhuma série';
  }

  function getSerie_(xml_serie_)
  {
    var campoSerie_ = document.getElementById('ref_serie_destino');
    var DOM_array = xml_serie_.getElementsByTagName( "serie" );

    if(DOM_array.length)
  {
    campoSerie_.length = 1;
    campoSerie_.options[0].text = 'Selecione uma série destino';
    campoSerie_.disabled = false;

    for( var i = 0; i < DOM_array.length; i++ )
  {
    campoSerie_.options[campoSerie_.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_serie"),false,false);
  }
  }
    else
    campoSerie_.options[0].text = 'O curso origem não possui nenhuma série';
  }

  document.getElementById('ref_cod_instituicao').onchange = function()
  {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

    var campoCurso = document.getElementById('ref_curso_origem');
    campoCurso.length = 1;
    campoCurso.disabled = true;
    campoCurso.options[0].text = 'Carregando curso origem';

    var campoCurso_ = document.getElementById('ref_curso_destino');
    campoCurso_.length = 1;
    campoCurso_.disabled = true;
    campoCurso_.options[0].text = 'Carregando curso destino';

    var xml_curso = new ajax( getCurso );
    xml_curso.envia( "educar_curso_xml2.php?ins="+campoInstituicao );

  };

  document.getElementById('ref_curso_origem').onchange = function()
  {
    var campoCurso = document.getElementById('ref_curso_origem').value;

    var campoSerie = document.getElementById('ref_serie_origem');
    campoSerie.length = 1;
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Carregando série origem';

    var xml_serie = new ajax( getSerie );
    xml_serie.envia( "educar_serie_xml.php?cur="+campoCurso )

  };

  document.getElementById('ref_curso_destino').onchange = function()
  {
    var campoCurso_ = document.getElementById('ref_curso_destino').value;

    var campoSerie_ = document.getElementById('ref_serie_destino');
    campoSerie_.length = 1;
    campoSerie_.disabled = true;
    campoSerie_.options[0].text = 'Carregando série destino';

    var xml_serie_ = new ajax( getSerie_ );
    xml_serie_.envia( "educar_serie_xml.php?cur="+campoCurso_ )

  };


