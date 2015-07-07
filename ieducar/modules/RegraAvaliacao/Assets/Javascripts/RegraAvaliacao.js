$j(function(){
	$tipoRecuperacaoParalelaField = $j('#tipoRecuperacaoParalela');
	$tipoRecuperacaoParalelaField.on('change', tipoRecuperacaoParalelaChange);
	$tipoRecuperacaoParalelaField.trigger('change');

	function tipoRecuperacaoParalelaChange(){
		if($j(this).val() == 1 ) {
      $j('#mediaRecuperacaoParalela').closest('tr').show();
		}else{
      $j('#mediaRecuperacaoParalela').val("");
      $j('#mediaRecuperacaoParalela').closest('tr').hide();
		}

    if($j(this).val() == 2){
      $j('[id^="tr_recuperacao"').show();
      $j('#tr___help1').show();
      $j('tr > td > hr').closest('tr').show();
    }else{
      $j('[id^="tr_recuperacao"').hide();
      $j('#tr___help1').hide();
      $j('tr > td > hr').closest('tr').hide();
    }
	}
});
// fix checkbox
$j('[name^="recuperacao[excluir]"]').on('change', function(){
  if($j(this).is(':checked'))
    $j(this).val('on');
  else
    $j(this).val('');
});

$j('#notaGeralPorEtapa').on('change', function(){
  if($j(this).is(':checked'))
    $j(this).val('1');
  else
    $j(this).val('0');
});