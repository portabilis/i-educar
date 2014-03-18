function reclassifica_matriculas(turma_id) {

  var additionalVars = {
    id : turma_id,
  };

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/turma', 'ordena-turma-alfabetica', additionalVars),
    dataType : 'json',
    data     : {},
    success  : alert('Matr\u00edculas reclassificadas alfabeticamente com sucesso!')
  };

  getResource(options);
}
