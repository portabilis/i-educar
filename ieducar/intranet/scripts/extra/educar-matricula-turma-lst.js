
  function enturmar(ref_cod_escola, ref_cod_serie, ref_cod_matricula, ref_cod_turma, ano_letivo) {
  document.formcadastro.method = 'post';
  document.formcadastro.action = 'educar_matricula_turma_det.php';

  document.formcadastro.ref_cod_escola.value = ref_cod_escola;
  document.formcadastro.ref_cod_serie.value = ref_cod_serie;
  document.formcadastro.ref_cod_matricula.value = ref_cod_matricula;
  document.formcadastro.ref_cod_turma.value = ref_cod_turma;
  document.formcadastro.ano_letivo.value = ano_letivo;

  document.formcadastro.submit();
}

