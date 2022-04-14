
  document.getElementById('ref_cod_escola').onchange = function() {
  getEscolaCurso();
}

  document.getElementById('ref_cod_curso').onchange = function() {
  getEscolaCursoSerie();
}

  function pesquisa_aluno() {
  pesquisa_valores_popless('educar_pesquisa_aluno.php')
}

