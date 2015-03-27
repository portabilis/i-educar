$j(function(){
	$campoEscolaDestinoExterna = $j('#escola_destino_externa');
	$campoEscolaDestinoSistema = $j('#ref_cod_escola_destino');
	
	$campoEscolaDestinoExterna.closest("tr").hide();
	$campoEscolaDestinoSistema.closest("tr").hide();
	
	$j('[name="transferencia_tipo"]').click(function(){
		
		if($j(this).val() == 1){
			$campoEscolaDestinoSistema.closest("tr").show();
			$campoEscolaDestinoExterna.closest("tr").hide();
			$campoEscolaDestinoExterna.val("");
		}else{
			$campoEscolaDestinoSistema.closest("tr").hide();
			$campoEscolaDestinoExterna.closest("tr").show();
			$campoEscolaDestinoSistema.val("");
		}
	});

});