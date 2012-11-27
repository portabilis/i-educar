function updateSelect($targetElement, options, emptyOptionHtml) {
  $targetElement.children('[value^=""]').remove();

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
  $targetElement.children('[value^=""]').remove();
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

    // some xml like portabilis_alunos_matriculados_xml.php put the value in an attribute
    if (typeof nodeValueAttrName != 'undefined')
      text = safeCapitalize($value.attr(nodeValueAttrName));
    else
      text = safeCapitalize($value.text());

    $option.html(text);
    options.push($option);
  });

  return options;
}


function jsonResourcesToSelectOptions(resources, attrIdName, attrValueName) {
  var options = [];

  $j.each(resources, function(index, resource){
    var $option = $j('<option />');
    $option.attr('value', resource[attrIdName]);

    var text = safeCapitalize(resource[attrValueName]);
    $option.html(text);

    options.push($option);
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
