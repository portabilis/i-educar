var apiUrlBase = '/module/Api/matricula';

var handleDeleteAbandono = function(dataResponse) {
  handleMessages(dataResponse.msgs);
  location.reload();
}
var handleDeleteReclassificacao = function(dataResponse){
  alert(stringUtils.toUtf8("Reclassificação cancelada com sucesso!\nLembre-se de cancelar a matrícula gerada pela reclassificação."));
  location.reload();
}

function deleteAbandono(matriculaId) {
  if (! confirm(stringUtils.toUtf8('Deseja desfazer o abandono?')))
    return false;

  var options = {
    url      : deleteResourceUrlBuilder.buildUrl(apiUrlBase, 'abandono'),
    dataType : 'json',
    data     : {
    id       : matriculaId
    },
    success  : handleDeleteAbandono
  };

  deleteResource(options);
}

function deleteReclassificacao(matriculaId) {
  if (! confirm(stringUtils.toUtf8('Deseja desfazer a reclassificação?')))
    return false;

  var options = {
    url      : deleteResourceUrlBuilder.buildUrl(apiUrlBase, 'reclassificacao'),
    dataType : 'json',
    data     : {
    id       : matriculaId
    },
    success  : handleDeleteReclassificacao
  };

    deleteResource(options)
}