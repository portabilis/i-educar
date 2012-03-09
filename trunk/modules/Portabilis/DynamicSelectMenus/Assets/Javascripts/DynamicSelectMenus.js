function safeLog(value)
{
  if(typeof(console) != 'undefined' && typeof(console.log) == 'function')
    console.log(value);
}


function buildId(id) {
  if (id.length > 0)
    return id[0] == '#' ? id : '#' + id;
  return id;
}


function updateSelect($targetElement, options) {
  $targetElement.children('[value^=""]').remove();

  $j.each(options, function(index, value){
    $j(value).appendTo($targetElement);
  });

  if (options.length > 0)
    $targetElement.removeAttr('disabled');
  else
    $targetElement.children(':first').html('Sem op&ccedil;&otilde;es');
}

function xmlResourcesToSelectOptions(resources, parentNodeName, optionIdAttrName) {
  var options = [];

  $j.each($j(resources).find(parentNodeName).children(), function(index, value){
    $value = $j(value);

    var $option = $j('<option />');
    $option.attr('id', $value.attr(optionIdAttrName));
    $option.html($value.text());
    options.push($option);
  });

  return options;
}
