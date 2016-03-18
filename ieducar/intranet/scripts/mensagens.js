jQuery.get("http://portabilis-adm.herokuapp.com/api/v1/messages?product=2", function(response) {
	for (var i = response.length - 1; i >= 0; i--) {
	  	var cabecalho = '<div class="teste '+i+'" style="min-height: 0px; padding: 5px 10px; background-color:'+response[i].color+';">';

	  	$j(".mensagens").append(cabecalho+response[i].message+'</div>'+'<br>');
	}
});
