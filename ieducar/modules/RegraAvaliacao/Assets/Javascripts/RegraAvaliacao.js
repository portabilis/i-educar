hideShowTabelaArredondamentoNota();
$j('input:radio').change(function(){
  hideShowTabelaArredondamentoNota();
});

function hideShowTabelaArredondamentoNota(){
  if($j("input[name='tipoNota']:checked").val() == 3){
    $j('#tr_tabelaArredondamento').hide();
    $j('#tr_tabelaArredondamentoNumero').show();
    $j('#tr_tabelaArredondamentoConceitual').show();
  }else{
    $j('#tr_tabelaArredondamento').show();
    $j('#tr_tabelaArredondamentoNumero').hide();
    $j('#tr_tabelaArredondamentoConceitual').hide();
  }
}

$j(function(){
	$tipoRecuperacaoParalelaField = $j('#tipoRecuperacaoParalela');
	$usaNotaGeralPorEtapaField = $j('#notaGeralPorEtapa');

  $tipoRecuperacaoParalelaField.on('change', tipoRecuperacaoParalelaChange);
	$tipoRecuperacaoParalelaField.trigger('change');

  $usaNotaGeralPorEtapaField.on('change', usaNotaGeralPorEtapaChange);
  $usaNotaGeralPorEtapaField.trigger('change');

  function usaNotaGeralPorEtapaChange(){
    if($j(this).val() == 1) {
      $tipoRecuperacaoParalelaField.val("");
      $tipoRecuperacaoParalelaField.closest('tr').hide();
    }else{
      $tipoRecuperacaoParalelaField.closest('tr').show();
    }
    $tipoRecuperacaoParalelaField.trigger('change');
  }

	function tipoRecuperacaoParalelaChange(){
		if($j(this).val() == 1 ) {
      $j('#mediaRecuperacaoParalela').closest('tr').show();
      $j('#tipoCalculoRecuperacaoParalela').closest('tr').show();
		}else{
      $j('#mediaRecuperacaoParalela').val("");
      $j('#mediaRecuperacaoParalela').closest('tr').hide();
      $j('#tipoCalculoRecuperacaoParalela').closest('tr').hide();
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
// fix checkboxes
$j('[name^="recuperacao[excluir]"]').on('change', function(){
  if($j(this).is(':checked'))
    $j(this).val('on');
  else
    $j(this).val('');
});

$j('#notaGeralPorEtapa, #aprovaMediaDisciplina, #reprovacaoAutomatica, #definirComponentePorEtapa, #desconsiderarLancamentoFrequencia').on('change', function(){
  if($j(this).is(':checked'))
    $j(this).val('1');
  else
    $j(this).val('0');
});
