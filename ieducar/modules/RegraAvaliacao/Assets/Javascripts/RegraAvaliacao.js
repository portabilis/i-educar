$j(function(){
	$tipoRecuperacaoParalelaField = $j('#tipoRecuperacaoParalela');
	$tipoRecuperacaoParalelaField.on('change', tipoRecuperacaoParalelaChange);
	$tipoRecuperacaoParalelaField.trigger('change');

	function tipoRecuperacaoParalelaChange(){
		if($j(this).val() == 0 ) {
			$j('#mediaRecuperacaoParalela').val("");
			$j('#mediaRecuperacaoParalela').closest('tr').hide();
		}else{
			$j('#mediaRecuperacaoParalela').closest('tr').show();
		}
	}
});