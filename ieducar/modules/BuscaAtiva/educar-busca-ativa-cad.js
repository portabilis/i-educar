$j(document).ready(function(){
  const data_fim = $j('#data_fim');
  BuscaAtiva.init();

  data_fim.on('blur', function (){
    if($j(this).val()){
      BuscaAtiva.showTipoRetornoBusca();
    } else {
      BuscaAtiva.hideTipoRetornoBusca() ;
    }
  });
});
