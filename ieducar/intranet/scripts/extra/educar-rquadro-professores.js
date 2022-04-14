

  function acao2()
  {

    if(!acao())
    return false;

    showExpansivelImprimir(400, 200,'',[], "Quadro Curricular");

    document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

    document.formcadastro.submit();
  }

  document.formcadastro.action = 'educar_relatorio_quadro_curricular_proc.php';

  document.getElementById('ref_cod_escola').onchange = function()
  {
    getEscolaCurso();
  }

  document.getElementById('ref_cod_curso').onchange = function()
  {
    getEscolaCursoSerie();
  }


