// before page is ready

$deleteButton = $j('<input value=" Excluir " type="button" style="display: inline; margin-left: 6px;">').html('')
                              .addClass('botaolistagem').insertAfter('#btn_enviar');
var $idField        = $j('#id');

// ajax

resourceOptions.handlePost = function(dataResponse) {

  if (! dataResponse.any_error_msg)
    window.setTimeout(function() { document.location = '/intranet/transporte_rota_det.php?cod_rota=' + resource.id(); }, 500);
  else
    $submitButton.removeAttr('disabled').val('Gravar');
}

resourceOptions.handlePut = function(dataResponse) {
  if (! dataResponse.any_error_msg)
    window.setTimeout(function() { document.location = '/intranet/transporte_rota_det.php?cod_rota=' + resource.id(); }, 500);
  else
    $submitButton.removeAttr('disabled').val('Gravar');
}

resourceOptions.handleGet = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  $deleteButton.removeAttr('disabled').show();

  $idField.val(dataResponse.id);
  $j('#desc').val(dataResponse.desc);
  $j('#pessoaj_id').val(dataResponse.ref_idpes_destino);
  $j('#pessoaj_ref_idpes_destino').val(dataResponse.ref_idpes_destino+' - '+dataResponse.nomeDestino);
  $j('#empresa_id').val(dataResponse.ref_cod_empresa_transporte_escolar);
  $j('#empresa_ref_cod_empresa_transporte_escolar').val(dataResponse.ref_cod_empresa_transporte_escolar+' - '+dataResponse.nomeEmpresa);
  $j('#ano').val(dataResponse.ano);
  $j('#tipo_rota').val(dataResponse.tipo_rota);
  $j('#km_pav').val(dataResponse.km_pav);
  $j('#km_npav').val(dataResponse.km_npav);
  $j('#km_npav').val(dataResponse.km_npav);
  if (dataResponse.tercerizado == 'S'){
    $j('#tercerizado').attr('checked',true);  
    $j('#tercerizado').val('on');   
  }  

};