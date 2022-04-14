$j(function(){
  $j('#substituiMenorNotaRc').on('change', function(){
    if($j(this).is(':checked')){
      $j(this).val('1');
    }
    else{
      $j(this).val('0');
    }
  });
});