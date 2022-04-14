   $j('#idpais').change(function() {
       abreSiglaUfBrasilEstrangeira();
    });



 function abreSiglaUfBrasilEstrangeira(){

	

 	$j('#sigla_uf').val('');
 if($j('#idpais').val() != 45){
	$j('#sigla_uf').attr('maxlength', '3');
 }else{
	$j('#sigla_uf').attr('maxlength', '2');
    }
 }