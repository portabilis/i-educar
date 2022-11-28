
function adicionaCheckboxConfirmacao() {
  $j('<tr id="tr_confirma_dados_unificacao"></tr>').insertBefore($j('#tr_observacao'));

  let htmlCheckbox = '<td colspan="2">'
  htmlCheckbox += '<input onchange="confirmaAnalise()" id="check_confirma_dados_unificacao" type="checkbox" />';
  htmlCheckbox += '<label for="check_confirma_dados_unificacao">Esta rotina excluirá todas as informações do diário do aluno posteriormente a data de movimentação, assinale se concorda.</label>';
  htmlCheckbox += '</td>';

  $j('#tr_confirma_dados_unificacao').html(htmlCheckbox);
}






