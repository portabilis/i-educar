
  function getSerie()
  {
    var campoCurso = document.getElementById('ref_cod_curso').value;
    var campoSerie = document.getElementById('serie_matricula').value;
    var xml1 = new ajax(getSerie_XML);
    strURL = "educar_sequencia_serie_curso_xml.php?cur="+campoCurso+"&ser_dif="+campoSerie;
    xml1.envia(strURL);

  }

  function getSerie_XML(xml)
  {
    //var campoCurso = document.getElementById('ref_cod_curso').value;
    var campoSerie = document.getElementById('ref_ref_cod_serie');
    //var campoSerieMatricula = document.getElementById('serie_matricula').value;

    var seq_serie = xml.getElementsByTagName( "serie" );
    campoSerie.length = 1;

    for( var ct = 0;ct < seq_serie.length;ct++ )
  {
    //  if( curso == sequencia_serie[ct][0] && sequencia_serie[ct][1] != campoSerieMatricula)
    //{
    campoSerie[campoSerie.length] = new Option(seq_serie[ct].firstChild.nodeValue,seq_serie[ct].getAttribute("cod_serie"),false,false);
    //  }

  }
    getSerie();

