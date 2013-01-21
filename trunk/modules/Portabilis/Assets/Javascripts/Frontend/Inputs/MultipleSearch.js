// multiple search input

var defaultChosenOptions = {
  no_results_text: "Sem resultados"
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

  // options that can be overwritten
  placeholder   : safeUtf8Decode('Selecione as opções')
};

var multipleSearch = {
  setup : function(options) {
    options = defaultMultipleSearchOptions.mergeWith(options);
    options.chosenOptions.url = options.get('searchPath');

    var attrName = options.get('attrName');
    if (attrName) { attrName = '_' + attrName; }

    var $input  = $j(buildId(options.get('objectName') + attrName));

    // fixups for chosen
    $input.attr('multiple', '');
    $input.attr('data-placeholder', options.get('placeholder'));

    // jquery scope
    $input.chosen(options.get('chosenOptions'), multipleSearch.handleSearch);

    // fixup to API receive all ids
    $j("#deficiencias").attr('name', $j("#deficiencias").attr('name') + '[]');
  }
};

var multipleSearchHelper = {
  setup : function(objectName, attrName, searchPath, searchResourceOptions) {
    var defaultOptions = {
      searchPath : searchPath,
      objectName : objectName,
      attrName   : attrName
    };

    var options = optionsUtils.merge(defaultOptions, searchResourceOptions);
    multipleSearch.setup(options);
  }
};