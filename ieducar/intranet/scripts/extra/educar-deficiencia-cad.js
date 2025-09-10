
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
      alert( 'Preencha o campo \'Deficiência Educacenso\' corretamente!' );
      $j('#deficiencia_educacenso').addClass('formdestaque').focus();
      return false;
    }

    if ($j('#deficiency_type_id').val() === '2' && ! $j('#transtorno_educacenso').val()) {
      mudaClassName( 'formdestaque', 'obrigatorio' );
      alert( 'Preencha o campo \'Transtorno Educacenso\' corretamente!' );
      $j('#transtorno_educacenso').addClass('formdestaque').focus();
      return false;
    }
    acao();
  }

  function deficiencyType() {
    if ($j('#deficiency_type_id').val() === '1') {
      $j('#tr_deficiencia_educacenso').show();
      $j('#deficiencia_educacenso');
      $j('#deficiencia_educacenso').makeRequired();

      $j('#tr_transtorno_educacenso').hide();
      $j('#transtorno_educacenso').val('');
    } else if ($j('#deficiency_type_id').val() === '2') {
      $j('#tr_transtorno_educacenso').show();
      $j('#transtorno_educacenso');
      $j('#transtorno_educacenso').makeRequired();

      $j('#tr_deficiencia_educacenso').hide();
      $j('#deficiencia_educacenso').val('');
    } else {
      $j('#tr_deficiencia_educacenso').hide();
      $j('#deficiencia_educacenso').val('');

      $j('#tr_transtorno_educacenso').hide();
      $j('#transtorno_educacenso').val('');

      $j('#deficiencia_educacenso').makeUnrequired();
      $j('#transtorno_educacenso').makeUnrequired();
    }
  }

  deficiencyType();
  $j('#deficiency_type_id').change(deficiencyType);

