
$j(document).ready(function(){

  $j('#ano').closest('tr').hide();

  var $escolaField = getElementFor('escola');

  var handleGetEscolaAnoLetivo = function(response) {
    $j('#ano').val(response[0]-1);
  }

  var updateEscolaAnoLetivo = function(){

    var urlForGetEscolaAnoLetivo = getResourceUrlBuilder.buildUrl('/module/Api/escola', 'escola-ano-letivo', {
      escola_id : $escolaField.val()
    });

    var options = {
      url : urlForGetEscolaAnoLetivo,
      dataType : 'json',
      success  : handleGetEscolaAnoLetivo
    };

    getResources(options);
  };

  $escolaField.change(updateEscolaAnoLetivo);

  //Atualiza o ano letivo para usuários cuja escola já venha selecionada
  updateEscolaAnoLetivo();

}); // ready