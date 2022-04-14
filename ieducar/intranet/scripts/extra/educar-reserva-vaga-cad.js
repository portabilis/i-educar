
  function pesquisa_aluno() {
  pesquisa_valores_popless('educar_pesquisa_aluno.php')
}

  function showAlunoExt(acao) {
  setVisibility('tr_nm_aluno_ext',acao);
  setVisibility('tr_cpf_responsavel',acao);
  setVisibility('tr_nm_aluno',!acao);

  document.getElementById('nm_aluno_ext').disabled = !acao;
  document.getElementById('cpf_responsavel').disabled = !acao;

  document.getElementById('tipo_aluno').value = (acao == true ? 'e' : 'i');
}

  setVisibility('tr_nm_aluno_ext', false);
  setVisibility('tr_cpf_responsavel', false);

  function acao2() {
  if (document.getElementById('tipo_aluno').value == 'e') {
  if (document.getElementById('nm_aluno_ext').value == '') {
  alert('Preencha o campo \'Nome aluno\' Corretamente');
  return false;
}

  if (! (/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/.test(document.formcadastro.cpf_responsavel.value))) {
  alert('Preencha o campo \'CPF responsável\' Corretamente');
  return false;
}
  else {
  if(! DvCpfOk( document.formcadastro.cpf_responsavel) )
  return false;
}

  document.getElementById('nm_aluno_').value = '';
  document.getElementById('ref_cod_aluno').value = '';

  document.formcadastro.submit();
}
  else {
  document.getElementById('nm_aluno_ext').value = '';
  document.getElementById('cpf_responsavel').value =  '';
}
  acao();
}

