   $j('#idpais').change(function() {
       abreSiglaUfBrasilEstrangeira();
    });



 function abreSiglaUfBrasilEstrangeira(){

	

 	$j('#sigla_uf').val('').toUpperCase();
 if($j('#idpais').val() != 45){
	$j('#sigla_uf').attr('maxlength', '3').toUpperCase();
 }else{
	$j('#sigla_uf')attr('maxlength', '2').toUpperCase().;
    }
 }