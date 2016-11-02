var apiUrlBase = '/module/Api/matricula';

var handleDeleteAbandono = function(dataResponse) {
  handleMessages(dataResponse.msgs);
  location.reload();
}

function deleteAbandono(matriculaId) {
  if (! confirm(stringUtils.toUtf8('Deseja desfazer o abandono?')))
    return false;

  var options = {
    url      : deleteResourceUrlBuilder.buildUrl(apiUrlBase, 'abandono'),
    dataType : 'json',
    data     : {
      id : matriculaId
    },
    success  : handleDeleteAbandono
  };

  deleteResource(options);
}