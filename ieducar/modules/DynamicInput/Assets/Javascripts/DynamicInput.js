function updateSelect($targetElement, options, emptyOptionHtml) {
  $targetElement.children().not('[value=""]').remove();

  var groups = new Array();
  var optgroup = null;

  $j.each(options, function(index, value) {
    if ($j(value).data('group')) {
      if (groups.indexOf($j(value).data('group')) == -1) {
        if (optgroup != null) {
          optgroup.appendTo($targetElement);
        }
        optgroup = $j('<optgroup />').attr('label', $j(value).data('group'));
        groups.push($j(value).data('group'));
      }
      $j(value).appendTo(optgroup);
    } else {
      $j(value).appendTo($targetElement);
    }
  });

  if (optgroup != null) {
    optgroup.appendTo($targetElement);
  }

  if (options.length === 1) {
      setTimeout(function () {
        $targetElement.removeAttr('selected').find('option:eq(1)').attr('selected', 'selected').change();
      },200)
  }

  if (options.length > 0) {
    $targetElement.removeAttr('disabled');
    $targetElement.children('[value=""]').first().html(emptyOptionHtml || "Selecione uma opção");
  } else {
    $targetElement.children(':first').html('Sem opções');
  }
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
      text = ($value.attr(nodeValueAttrName));
    else
      text = ($value.text());

    $option.html(text);
    options.push($option);
  });

  return options;
}


function jsonResourcesToSelectOptions(resources, captalizeFirstCaracter) {
  var options = [];

  $j.each(resources, function(id, value) {

    // como arrays com chave numerica são ordenados pela chave pode-se enviar
    // arrays como { __123 : 'value a', __111 : 'value b'} com a chave iniciando com '__'
    // para que seja respeitado a posição dos elementos da lista e não pela chave
    // assim o '__' do inicio do id será removido antes de usa-lo.

    if (id.indexOf && id.substr && id.indexOf('__') == 0)
      id = id.substr(2);

    var opt = $j('<option />').attr('value', id);

    var newValue = value;
    if (typeof(value) == 'object') {
      $j.each(value, function(optId, optValue) {
        if (optId != 'value') {
          opt.data(optId, optValue);
        } else {
          newValue = optValue;
        }
      });
    }

    if (captalizeFirstCaracter)
      opt.html((newValue));
    else
      opt.html(newValue);

    options.push(opt);
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
