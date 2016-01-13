// before page is ready

// $deleteButton = $j('<input value=" Excluir " type="button" style="display: inline; margin-left: 6px;">').html('')
                              // .addClass('botaolistagem').insertAfter('#btn_enviar');

var $idField        = $j('#id');

var submitForm = function(event) {
  if ($j('#cep_').val()){
    if (!validateEndereco()){
      return;
    }
  }
  submitFormExterno();
}
// ajax

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

  $j('#cep_').val(dataResponse.cep);

  $j('#latitude').val(dataResponse.latitude);
  $j('#longitude').val(dataResponse.longitude);

    if ($j('#cep_').val()){

      $j('#municipio_municipio').removeAttr('disabled');
      $j('#distrito_distrito').removeAttr('disabled');
      $j('#bairro_bairro').removeAttr('disabled');
      $j('#logradouro_logradouro').removeAttr('disabled');
      $j('#bairro').removeAttr('disabled');
      $j('#zona_localizacao').removeAttr('disabled');
      $j('#idtlog').removeAttr('disabled');
      $j('#logradouro').removeAttr('disabled');

      $j('#complemento').val(dataResponse.complemento);
      $j('#numero').val(dataResponse.numero);

      $j('#municipio_id').val(dataResponse.idmun);
      $j('#distrito_id').val(dataResponse.iddis);

      $j('#municipio_municipio').val(dataResponse.idmun+' - '+dataResponse.municipio+' ('+dataResponse.sigla_uf+')');
      $j('#distrito_distrito').val(dataResponse.iddis+' - '+dataResponse.distrito);

      if (dataResponse.idbai && dataResponse.idlog){

        $j('#bairro_id').val(dataResponse.idbai);
        $j('#logradouro_id').val(dataResponse.idlog);
        $j('#bairro_bairro').val(dataResponse.bairro + ' / Zona '+(dataResponse.zona_localizacao == "1" ? "Urbana" : "Rural"));
        $j('#logradouro_logradouro').val($j("#idtlog option[value='"+dataResponse.idtlog+"']").text() + ' '+dataResponse.logradouro);

      }else{

        $j('#bairro').val(dataResponse.bairro);
        $j('#logradouro').val(dataResponse.logradouro);
        $j('#idtlog').val(dataResponse.idtlog);
        $j('#zona_localizacao').val(dataResponse.zona_localizacao);

      }
    }

    hideEnderecoFields();

};

var singletonMap;

function reloadSingletonMap(){
   window.setTimeout(function() {
    singletonMap.reload();
  }, 1000);
}

// when page is ready
$j(document).ready(function() {

  hideEnderecoFields();
  fixUpPlaceholderEndereco();

  $j('#latitude').attr('readonly', 'true');
  $j('#longitude').attr('readonly', 'true');
  $j('#latitude').css('background-color' , '#DFDFDF')
  $j('#longitude').css('background-color' , '#DFDFDF')

  $j('<tr>').html('<td colspan=\'2\' ><div id=\'map\' style=\'height: 300px\' align=\'center\' width=\'500px\'></div> </td>').insertBefore($j('.tableDetalheLinhaSeparador').closest('tr'));

  singletonMap = new IeducarSingletonMap();

  window.setTimeout(function() {
    $j('#idtlog').css('width', '');
    $j('#zona_localizacao').css('width', '');
    $submitButton.removeAttr('onclick');
    $submitButton.unbind('click');
    $submitButton.click(submitForm);
    singletonMap.render();
  }, 1000);

  $j('input[type="text"]').on('input', reloadSingletonMap);

}); // ready