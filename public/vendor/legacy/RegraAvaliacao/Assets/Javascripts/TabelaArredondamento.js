habilitaCasaDecimalExtra();
EmiteAlertCampos();

function habilitaCasaDecimalExtra() {
	$j('select[name^="valor_acao"]').each(function(id, val) {
	    if(val.value != 3){
	    	$j('input[name^="valor_casa_decimal_exata['+id+']"]').prop('disabled', true);
	    }
	    $j('select[name^="valor_acao['+id+']"]').change(function() {
			if(val.value == 3){
		    	$j('input[name^="valor_casa_decimal_exata['+id+']"]').prop('disabled', false);
		    }else{
		    	$j('input[name^="valor_casa_decimal_exata['+id+']"]').val('');
		    	$j('input[name^="valor_casa_decimal_exata['+id+']"]').prop('disabled', true);
		    }
		});
	});
}

// Emite alert() se campos valor máximo ou mínimo for acima de 100
function EmiteAlertCampos(){
	$j('input[name^="valor_maximo"]').each(function(id, val) {
	    $j('input[name^="valor_maximo['+id+']"]').keyup(function(){
	        if(val.value >= 100) {
	        	$j('#btn_enviar').click(function(e){
	        		alert('Campo "Valor máximo" não permite valor acima de 100');
	        	});
	        }
	    });
	});

	$j('input[name^="valor_minimo"]').each(function(id, val) {
	    $j('input[name^="valor_minimo['+id+']"]').keyup(function(){
	        if(val.value >= 100) {
	        	$j('#btn_enviar').click(function(e){
	        		alert('Campo "Valor mínimo" não permite valor acima de 100');
	        	});
	        }
	    });
	});
}