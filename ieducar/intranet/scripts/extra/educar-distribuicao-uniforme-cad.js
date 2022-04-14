
  function bloqueiaCamposQuantidade(){
  $j('#agasalho_qtd').val('').attr('disabled', 'disabled');
  $j('#camiseta_curta_qtd').val('').attr('disabled', 'disabled');
  $j('#camiseta_longa_qtd').val('').attr('disabled', 'disabled');
  $j('#camiseta_infantil_qtd').val('').attr('disabled', 'disabled');
  $j('#calca_jeans_qtd').val('').attr('disabled', 'disabled');
  $j('#meias_qtd').val('').attr('disabled', 'disabled');
  $j('#saia_qtd').val('').attr('disabled', 'disabled');
  $j('#bermudas_tectels_qtd').val('').attr('disabled', 'disabled');
  $j('#bermudas_coton_qtd').val('').attr('disabled', 'disabled');
  $j('#tenis_qtd').val('').attr('disabled', 'disabled');
  return true;
}

  function liberaCamposQuantidade(){
  $j('#agasalho_qtd').removeAttr('disabled');
  $j('#camiseta_curta_qtd').removeAttr('disabled');
  $j('#camiseta_longa_qtd').removeAttr('disabled');
  $j('#camiseta_infantil_qtd').removeAttr('disabled');
  $j('#calca_jeans_qtd').removeAttr('disabled');
  $j('#meias_qtd').removeAttr('disabled');
  $j('#saia_qtd').removeAttr('disabled');
  $j('#bermudas_tectels_qtd').removeAttr('disabled');
  $j('#bermudas_coton_qtd').removeAttr('disabled');
  $j('#tenis_qtd').removeAttr('disabled');
}

  $j(document).ready(function(){
  if($j('#kit_completo').is(':checked'))
  bloqueiaCamposQuantidade();

  $j('#kit_completo').on('change', function(){
  if($j('#kit_completo').is(':checked'))
  bloqueiaCamposQuantidade();
  else
  liberaCamposQuantidade();
});
})

