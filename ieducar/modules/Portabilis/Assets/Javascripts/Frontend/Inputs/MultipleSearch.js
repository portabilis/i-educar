// multiple search input
var arrayOptions = [];
var defaultChosenOptions = {
  no_results_text: "Sem resultados para ",
  width: '290px',
  placeholder_text_multiple: "Selecione as opções",
  placeholder_text_single: "Selecione uma opção",
  search_contains: true
};

var defaultMultipleSearchOptions = {
  // options that cannot be overwritten

  get       : function(optionName) { return optionsUtils.get(this, optionName) },
  mergeWith : function(options) {
    options                   = optionsUtils.merge(this, options);
    options.chosenOptions = optionsUtils.merge(defaultChosenOptions, options.chosenOptions);

    return options;
  },

  // options that must be overwritten
  objectName    : undefined,
  attrName      : undefined,
  searchPath    : undefined,
  typeSearch    : undefined,

  // options that can be overwritten
  // placeholder   : safeUtf8Decode('Selecione as opções')
};

var multipleSearch = {
  setup : function(options) {
    options = defaultMultipleSearchOptions.mergeWith(options);
    options.chosenOptions.url = options.get('searchPath');
    var typeSearch = options.get('typeSearch');
    var attrName = options.get('attrName');
    if (attrName) { attrName = '_' + attrName; }

    var objectId = buildId(options.get('objectName') + attrName);

    var $input  = $j(objectId);

    // fixups for chosen
    if(typeSearch == 'multiple'){
      $input.attr('multiple', '');
    }

    var objectName = options.get('objectName');

    // jquery scope
    $input.chosen(options.get('chosenOptions'));

    // fixup to API receive all ids
    $j(objectId).attr('name', $j(objectId).attr('name') + '[]');
  }
};

var multipleSearchHelper = {
  setup : function(objectName, attrName, searchPath, typeSearch, searchResourceOptions) {
    var defaultOptions = {
      searchPath : searchPath,
      objectName : objectName,
      attrName   : attrName,
      typeSearch : typeSearch
    };

    var options = optionsUtils.merge(defaultOptions, searchResourceOptions);
    multipleSearch.setup(options);
  }
};

var updateChozen = function(input, values){
  var orderedList = [];

  if (!Array.isArray(values)) {
    for (let prop in values) {
      orderedList.push({
        label:values[prop],
        value: prop
      });
    }
  }

  orderedList = orderedList.sort(function (a,b) {
    return a.label > b.label ? 1 : -1
  });

  for (let option in orderedList) {
    if (orderedList[option ].value !== undefined) {
      input.append('<option value="' + orderedList[option].value + '"> ' + orderedList[option].label + '</option>');
    }
  }

  input.trigger("chosen:updated");
};

var clearValues = function(input){
  input.empty();
  input.trigger("chosen:updated");
}
