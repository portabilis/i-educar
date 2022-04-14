

  changeCurso =
  function(){
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var xml1 = new ajax(getCurso_XML);
  strURL = "educar_curso_serie_xml.php?esc="+campoEscola+"&cur=1";
  xml1.envia(strURL);


}

  after_getEscola = changeCurso;

  function getCurso_XML(xml)
  {

    var escola = document.getElementById('ref_cod_escola');
    var cursos = document.getElementById('cursos');
    var conteudo = '';
    var achou = false;
    var escola_curso = xml.getElementsByTagName( "item" );

    cursos.innerHTML = 'Selecione uma escola';
    if(escola.value == '')
    return;

    for(var ct = 0; ct < escola_curso.length;ct+=2)
  {

    achou = true;
    conteudo += '<input type="checkbox" checked="checked" name="cursos[]" id="cursos[]" value="'+ escola_curso[ct].firstChild.nodeValue +'"><label for="cursos[]">' + escola_curso[ct+1].firstChild.nodeValue +'</label> <br />';

  }
    if( !achou ){
    cursos.innerHTML = 'Escola sem cursos';
    return;
  }
    cursos.innerHTML = '<table cellspacing="0" cellpadding="0" border="0">';
    cursos.innerHTML += '<tr align="left"><td>'+ conteudo +'</td></tr>';
    cursos.innerHTML += '</table>';

  }

  function acao2()
  {

    if(!acao())
    return false;

    showExpansivelImprimir(400, 200,'',[], "Movimentação Mensal de Alunos");

    document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

    document.formcadastro.submit();
  }

  document.formcadastro.action = 'educar_alunos_defasados_nominal_proc.php';


