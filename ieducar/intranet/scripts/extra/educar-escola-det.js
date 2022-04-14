
  function preencheForm (ano, escola, acao) {
  if (!confirm('Deseja realmente \'' + acao.substr(0, 1).toUpperCase() + acao.substr(1) + '\' o ano letivo?')) {
  return false;
}

  document.acao_ano_letivo.ano.value = ano;
  document.acao_ano_letivo.ref_cod_escola.value = escola;
  document.acao_ano_letivo.tipo_acao.value = acao;
  document.acao_ano_letivo.submit();
}

