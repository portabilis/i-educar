$j('select[name^="valor[acao]"]').on('change', onTipoArredondamentoNotaChange);
$j('select[name^="valor[acao]"]').trigger('change');

function onTipoArredondamentoNotaChange(){
	$labelProximoCampo = $j(this).next('span');

	if($j(this).val() == 3){
		$labelProximoCampo.show();
		$labelProximoCampo.next('input').show();
	}else{
		$labelProximoCampo.hide();
		$labelProximoCampo.next('input').hide();
	}
}