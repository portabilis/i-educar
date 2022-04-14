
  // Reescrita da função para exibir mensagem interativa
  function excluir()
  {
    document.formcadastro.reset();

    if (confirm('Deseja mesmo excluir essa deficiência? \nVinculos com os alunos serão deletados.')) {
    document.formcadastro.tipoacao.value = 'Excluir';
    document.formcadastro.submit();
  }
  }

