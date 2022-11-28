
 $j('#btn_enviar').prop('disabled', true);
 $j('#btn_enviar').addClass('btn-disabled');

  $j('<tr id="tr_confirma_dados_unificacao"></tr>').insertBefore($j('#btn_enviar'));

  let htmlCheckbox = '<td colspan="2">'
  htmlCheckbox += '<input onchange="confirmaAnalise()" id="check_confirma_exclusao" type="checkbox" />';
  htmlCheckbox += '<label for="check_confirma_exclusao" style="color:red !important;">Esta rotina excluirá todas as informações do diário do aluno posteriormente a data de movimentação, assinale se concorda.</label>';
  htmlCheckbox += '</td>';

  $j('#tr_confirma_dados_unificacao').html(htmlCheckbox);

  function confirmaAnalise() {
  let checked = $j('#check_confirma_exclusao').is(':checked');

  if (checked) {
    habilitaBotaoUnificar();
    return;
  }

  if (!checked) {
    desabilitaBotaoUnificar();
    return;
  }

}

function habilitaBotaoUnificar() {
    $j('#btn_enviar').prop('disabled', false);
    $j('#btn_enviar').addClass('btn-green');
    $j('#btn_enviar').removeClass('btn-disabled');
  }

  function desabilitaBotaoUnificar() {
    $j('#btn_enviar').prop('disabled', true);
    $j('#btn_enviar').removeClass('btn-green');
    $j('#btn_enviar').addClass('btn-disabled');
  }






