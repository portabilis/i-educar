function safeLog(value)
{
  if(typeof(console) != 'undefined' && typeof(console.log) == 'function')
    console.log(value);
}


function buildId(id) {
  return typeof(id) == 'string' && id.length > 0 && id[0] == '#' ? id : '#' + id;
}


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


function xmlResourcesToSelectOptions(resources, parentNodeName, optionIdAttrName) {
  var options = [];

  $j.each($j(resources).find(parentNodeName).children(), function(index, value){
    $value = $j(value);

    var $option = $j('<option />');
    $option.attr('value', $value.attr(optionIdAttrName));
    $option.html($value.text());
    options.push($option);
  });

  return options;
}


function jsonResourcesToSelectOptions(resources, attrIdName, attrValueName) {
  var options = [];

  $j.each(resources, function(index, resource){
    var $option = $j('<option />');
    $option.attr('value', resource[attrIdName]);
    $option.html(resource[attrValueName]);
    options.push($option);
  });

  return options;
}


function fixupFieldsWidth(){
  var maxWidth = 0;
  var $fields = $j('form select');

  //get maxWidh
  $j.each($fields, function(index, value){
    $value = $j(value);
    if ($value.outerWidth() > maxWidth)
      maxWidth = $value.outerWidth();
  });

  //set maxWidth
  $j.each($fields, function(index, value){
    $j(value).width(maxWidth);
  });
};


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
