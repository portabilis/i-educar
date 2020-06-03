// Verifica se o formato do cep é válido
function checkCepFields(cep) {
    var regexp = /[0-9]{5}\-[0-9]{3}/;
    var valid = regexp.test(cep);
    return valid;
}

function permiteEditarEndereco(){

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/endereco', 'permissao_editar'),
    dataType : 'json',
    data     : {},
    success  : handleGetPermissaoEditar
  };
  getResource(options);
}

var handleGetPermissaoEditar = function(dataResponse) {
  if (dataResponse.permite_editar == 0) {
    $j('#span-busca-logradouro').hide();
    $j('#span-busca-bairro').hide();
  }
};

function validateEndereco(){

  var err = false;

  if (!checkCepFields($j('#postal_code').val())){
    $j('#postal_code').addClass('error');
    messageUtils.error('Informe um CEP no formato NNNNN-NNN.');
    err = true;
  }

  if (!$j('#address').val()){
    $j('#address').addClass('error');
    messageUtils.error('Digite o nome do logradouro.');
    err = true;
  }

  if (!$j('#neighborhood').val()){
    $j('#neighborhood').addClass('error');
    messageUtils.error('Digite o nome do bairro.');
    err = true;
  }

  if (!$j('#city_id').val()){
    $j('#city_city').addClass('error');
    messageUtils.error('Selecione um município corretamente.');
    err = true;
  }

  return !err;
}
