// multiple search ajax input

var defaultAjaxChosenOptions = {
  minTermLength : 1,
  type: 'GET',
  dataType: 'json',
  url: '',
  jsonTermKey: 'query'
};


var defaultMultipleSearchAjaxOptions = {
  // options that cannot be overwritten

  get       : function(optionName) { return optionsUtils.get(this, optionName) },
  mergeWith : function(options) {
    options                   = optionsUtils.merge(this, options);
    options.ajaxChosenOptions = optionsUtils.merge(defaultAjaxChosenOptions, options.ajaxChosenOptions);

    return options;
  },

  // options that must be overwritten
  objectName    : undefined,
  attrName      : undefined,
  searchPath    : undefined
};


var multipleSearchAjax = {
  handleSearch : function(dataResponse) {
    var results = [];

    $j.each(dataResponse.result, function (value, label) {
      results.push({ value: value, text: label });
    });

    return results;
  },

  setup : function(options) {
    options = defaultMultipleSearchAjaxOptions.mergeWith(options);
    options.ajaxChosenOptions.url = options.get('searchPath');

    var attrName = options.get('attrName');
    if (attrName) { attrName = '_' + attrName; }

    var $input  = $j(buildId(options.get('objectName') + attrName));

    // fixups for chosen
    //$input.css('width', '430px');
    $input.attr('multiple', '');

    $input.ajaxChosen(options.get('ajaxChosenOptions'), multipleSearchAjax.handleSearch);
  }
};

var multipleSearchAjaxHelper = {
  setup : function(objectName, attrName, searchPath, searchResourceOptions) {
    var defaultOptions = {
      searchPath : searchPath,
      objectName : objectName,
      attrName   : attrName
    };

    var options = optionsUtils.merge(defaultOptions, searchResourceOptions);
    multipleSearchAjax.setup(options);
  }
};

// # TODO implementar 'load' options