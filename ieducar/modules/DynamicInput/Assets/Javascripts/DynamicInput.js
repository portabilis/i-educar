function updateSelect($targetElement, options, emptyOptionHtml) {
  $targetElement.children().not('[value=""]').remove();

  $j.each(options, function(index, value){
    $j(value).appendTo($targetElement);
  });

  if (options.length > 0) {
    $targetElement.removeAttr('disabled');
    $targetElement.children('[value=""]').first().html(emptyOptionHtml || "Selecione uma op&ccedil;&atilde;o");
  }
  else
    $targetElement.children(':first').html('Sem op&ccedil;&otilde;es');
}


function resetSelect($targetElement) {
  $targetElement.children().not('[value=""]').remove();
  $targetElement.children().first().attr('checked', 'checked');
  //$targetElement.attr('disabled', 'disabled');
}


function xmlResourcesToSelectOptions(resources, parentNodeName, nodeIdAttrName, nodeValueAttrName) {
  var options = [];

  $j.each($j(resources).find(parentNodeName).children(), function(index, value){
    var $value = $j(value);
    var text;

    var $option = $j('<option />');
    $option.attr('value', $value.attr(nodeIdAttrName));

    if (typeof nodeValueAttrName != 'undefined')
      text = safeCapitalize($value.attr(nodeValueAttrName));
    else
      text = safeCapitalize($value.text());

    $option.html(text);
    options.push($option);
  });

  return options;
}


function jsonResourcesToSelectOptions(resources) {
  var options = [];

  $j.each(resources, function(id, value) {

    // como arrays com chave numerica são ordenados pela chave pode-se enviar
    // arrays como { __123 : 'value a', __111 : 'value b'} com a chave iniciando com '__'
    // para que seja respeitado a posição dos elementos da lista e não pela chave
    // assim o '__' do inicio do id será removido antes de usa-lo.

    if (id.indexOf && id.substr && id.indexOf('__') == 0)
      id = id.substr(2);

    options.push($j('<option />').attr('value', id).html(safeCapitalize(value)));
  });

  return options;
}


function getFirstDefined(attrIdNames) {
  if (! $j.isArray(attrIdNames))
    attrIdNames = [attrIdNames];

  var $element = undefined;

  $j.each(attrIdNames, function(index, attIdName){
    $element = $j(buildId(attIdName));

    if ($element.length > 0)
      return false;
  });

  return $element;
}

function getElementFor(entityName) {
  var $element = getFirstDefined([entityName    + "_id" ,
                                 "ref_cod_"     + entityName,
                                 "ref_ref_cod_" + entityName,
                                 entityName]);
  if ($element == undefined)
    safeLog("Elemento não definido para '" + entityName + "'");

  return $element;
}
