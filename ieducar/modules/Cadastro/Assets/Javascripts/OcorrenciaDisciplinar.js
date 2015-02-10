$j('#btn_enviar').bind('click', function(){
	if($j('#visivel_pais').prop('checked')){
		alert($j('#cod_ocorrencia_disciplinar').val());
	}
});