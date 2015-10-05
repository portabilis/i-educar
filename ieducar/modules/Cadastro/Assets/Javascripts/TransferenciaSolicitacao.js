$j(function(){
	$campoEscolaDestinoExterna = $j('#escola_destino_externa');
	$campoEscolaDestinoSistema = $j('#ref_cod_escola_destino');

	$campoEscolaDestinoExterna.closest("tr").hide();
	$campoEscolaDestinoSistema.val('');

	$campoEscolaDestinoSistema.on('click', onEscolaDestinoSistemaClick);


	function onEscolaDestinoSistemaClick(){
		console.log($j(this).val());
		if($j(this).val() == 0){
			$campoEscolaDestinoExterna.closest("tr").show();
		}else{
			$campoEscolaDestinoExterna.closest("tr").hide();
			$campoEscolaDestinoExterna.val("");
		}
	}
});

var msg = '<b>Novidade</b>: O processo de transferência foi simplificado!<br/>' +
          'Agora você não precisa mais o tipo de transferência que será utilizado.<br/>' +
          'Basta, preencher os campos: Escola, Motivo e Data e o aluno ficará<br/>'+
          'com a situação de transferido automaticamente.';

$j('<p>').addClass('right-top-notice notice')
         .html(stringUtils.toUtf8(msg))
         .appendTo($j('#tr_nm_aluno').closest('td'));