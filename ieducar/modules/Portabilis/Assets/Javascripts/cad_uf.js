$j('#sigla_uf').mask('AAA');



    
 $j('#sigla_uf').change(function() {
    verificaSiglaUf();

 });


    
 $j('#sigla_uf').keyup(function() {
 if($j('#idpais').val() == 45){
   $j('#sigla_uf').val($j('#sigla_uf').val().substring(0,2));
 }

 });


 
 function verificaSiglaUf(){

 if($j('#idpais').val() != 45){

    if($j('#sigla_uf').val().length < 2){
        
       $j('#sigla_uf').val("");
    alert("O campo UF deve ter 2 ou 3 caracteres para estados estrangeiros!");
    }

 }

 }


