
  // Reescrita da função para exibir mensagem interativa
  function excluir()
  {
    document.formcadastro.reset();

    if (confirm('Deseja mesmo excluir essa deficiência? \nVinculos com os alunos serão deletados.')) {
    document.formcadastro.tipoacao.value = 'Excluir';
    document.formcadastro.submit();
  }
  }

  function acaoEnviar() {
    if ($j('#deficiency_type_id').val() === '1' && ! $j('#deficiencia_educacenso').val()) {
      mudaClassName( 'formdestaque', 'obrigatorio' );
      alert( 'Preencha o campo \'Deficiência educacenso\' corretamente!' );
      $j('#deficiencia_educacenso').addClass('formdestaque').focus();
      return false;
    }

    acao();
  }

  function deficiencyType() {
    if ($j('#deficiency_type_id').val() === '1') {
      $j('#tr_deficiencia_educacenso').show();
      $j('#deficiencia_educacenso');
    } else {
      $j('#tr_deficiencia_educacenso').hide();
      $j('#deficiencia_educacenso').val('');
    }
  }

  deficiencyType();
  $j('#deficiency_type_id').change(deficiencyType);

