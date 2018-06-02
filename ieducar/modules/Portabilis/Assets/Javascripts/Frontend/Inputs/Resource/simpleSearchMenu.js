(function() {
  doc = document;
  loc = doc.location;
  host = loc.hostname;
  $j("#menu").keypress(function(e){
    if(e.keyCode == '13'){
      caminho = $j("#menu_id").val();
      if (caminho){
        if(caminho.search(".php") > -1){
          caminhoCompleto = host + '/intranet/' + caminho;
          loc.href = caminhoCompleto;
        }else{
          loc.pathname = '/' + caminho;
        }
      }
    }
  });
}());
