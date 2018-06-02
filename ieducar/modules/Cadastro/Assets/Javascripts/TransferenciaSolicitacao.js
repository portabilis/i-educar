$j(function(){
	$campoEscolaDestinoExterna = $j('#escola_destino_externa');
	$campoEstadoEscolaDestinoExterna = $j('#estado_escola_destino_externa');
	$campoMunicipioEscolaDestinoExterna = $j('#municipio_escola_destino_externa');
	$campoEscolaDestinoSistema = $j('#ref_cod_escola_destino');

	$campoEscolaDestinoExterna.closest("tr").hide();
	$campoEstadoEscolaDestinoExterna.closest("tr").hide();
	$campoMunicipioEscolaDestinoExterna.closest("tr").hide();
	$campoEscolaDestinoSistema.val('');

	$campoEscolaDestinoSistema.on('change', onEscolaDestinoSistemaClick);


	function onEscolaDestinoSistemaClick(){
		if($j(this).val() == 0){
			$campoEscolaDestinoExterna.closest("tr").show();
			$campoEstadoEscolaDestinoExterna.closest("tr").show();
			$campoMunicipioEscolaDestinoExterna.closest("tr").show();
		}else{
			$campoEscolaDestinoExterna.closest("tr").hide();
			$campoEstadoEscolaDestinoExterna.closest("tr").hide();
			$campoEscolaDestinoExterna.closest("tr").hide();
			$campoMunicipioEscolaDestinoExterna.val("");
			$campoEstadoEscolaDestinoExterna.val("");
			$campoMunicipioEscolaDestinoExterna.val("");
		}
	}
});

var msg = '<b>Novidade</b>: O processo de transferência foi simplificado!<br/>' +
          'Agora você não precisa mais informar o tipo de transferência<br/>' +
          'que será utilizado. Basta preencher os campos obrigatórios, e<br/>'+
          'o aluno ficará com a situação de transferido automaticamente.';

$j('<p>').addClass('right-top-notice notice')
         .html(stringUtils.toUtf8(msg))
         .appendTo($j('#tr_nm_aluno').closest('td'));