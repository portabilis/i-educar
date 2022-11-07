var $idField        = $j('#id');

var submitForm = function(event) {
  if ($j('#cep_').val()){
    if (!validateEndereco()){
      return;
    }
  }
  submitFormExterno();
}

resourceOptions.handlePost = function(dataResponse) {

  if (! dataResponse.any_error_msg)
    window.setTimeout(function() { document.location = '/intranet/transporte_ponto_det.php?cod_ponto=' + resource.id(); }, 500);
  else
    $submitButton.removeAttr('disabled').val('Gravar');
}

resourceOptions.handlePut = function(dataResponse) {
  if (! dataResponse.any_error_msg)
    window.setTimeout(function() { document.location = '/intranet/transporte_ponto_det.php?cod_ponto=' + resource.id(); }, 500);
  else
    $submitButton.removeAttr('disabled').val('Gravar');
}

resourceOptions.handleGet = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  $deleteButton.removeAttr('disabled').show();

  $idField.val(dataResponse.id);
  $j('#desc').val(dataResponse.desc);

  $j('#postal_code').val(dataResponse.cep);

  $j('#latitude').val(dataResponse.latitude);
  $j('#longitude').val(dataResponse.longitude);

  if ($j('#postal_code').val()){
    $j('#address').val(dataResponse.logradouro);
    $j('#number').val(dataResponse.numero);
    $j('#complement').val(dataResponse.complemento);
    $j('#neighborhood').val(dataResponse.bairro);
    $j('#city_id').val(dataResponse.idmun);
    $j('#city_city').val(dataResponse.idmun+' - '+dataResponse.municipio+' ('+dataResponse.sigla_uf+')');
  }
};

var singletonMap;

function reloadSingletonMap(){
   window.setTimeout(function() {
    singletonMap.reload();
  }, 1000);
}

// when page is ready
$j(document).ready(function() {

  $j('#latitude').attr('readonly', 'true');
  $j('#longitude').attr('readonly', 'true');
  $j('#latitude').css('background-color' , '#DFDFDF')
  $j('#longitude').css('background-color' , '#DFDFDF')

  $j('<tr>').html('<td colspan=\'2\' ><div id=\'map\' style=\'height: 300px\' align=\'center\' width=\'500px\'></div> </td>').insertBefore($j('.tableDetalheLinhaSeparador').closest('tr'));

  singletonMap = new IeducarSingletonMap();

  window.setTimeout(function() {
    $submitButton.removeAttr('onclick');
    $submitButton.unbind('click');
    $submitButton.click(submitForm);
    singletonMap.render();
  }, 1000);

  $j('input[type="text"]').on('input', reloadSingletonMap);

});
