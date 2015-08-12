$j('#sigla_uf').mask('AAA');



    
 $j('#sigla_uf').change(function() {
    verificaSiglaUf();

 });

     $j('#idpais').change(function() {
    verificaSiglaUf();
 });

 
 function verificaSiglaUf(){

 if($j('#idpais').val() == 45){

    if($j('#sigla_uf').val().length > 2){
       $j('#sigla_uf').val("");
    alert("O campo UF deve ter 2 caracteres para estados brasileiros!"); 
    }

 }else{

    if($j('#sigla_uf').val().length != 3){
        
       $j('#sigla_uf').val("");
    alert("O campo UF deve ter 3 caracteres para estados estrangeiros!");
    }

 }

 }



