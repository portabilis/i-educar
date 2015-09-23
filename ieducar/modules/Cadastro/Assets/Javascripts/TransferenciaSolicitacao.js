$j(function(){
	$campoEscolaDestinoExterna = $j('#escola_destino_externa');
	$campoEscolaDestinoSistema = $j('#ref_cod_escola_destino');

	$campoEscolaDestinoExterna.closest("tr").hide();
	$campoEscolaDestinoSistema.val('');

	$campoEscolaDestinoSistema.on('click', onEscolaDestinoSistemaClick);


	function onEscolaDestinoSistemaClick(){
		console.log($j(this).val());
		if($j(this).val() == 'OUTRA'){
			$campoEscolaDestinoExterna.closest("tr").show();
		}else{
			$campoEscolaDestinoExterna.closest("tr").hide();
			$campoEscolaDestinoExterna.val("");
		}
	}
});