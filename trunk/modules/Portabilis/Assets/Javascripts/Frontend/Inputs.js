// simple search input

var defaultJQueryAutocompleteOptions = {
  source      : function(request, response) { return simpleSearch.search(this.element, request, response) },
  minLength   : 1,
  autoFocus   : true,
  select      : function(event, ui) { return simpleSearch.handleSelect(event, ui) },

  // options that can be overwritten
  // change      : function (event, ui) {},
};


var defaultSimpleSearchOptions = {
  // options that cannot be overwritten

  get       : function(optionName) { return optionsUtils.get(this, optionName) },
  mergeWith : function(options) {
    options                     = optionsUtils.merge(this, options);
    options.autocompleteOptions = optionsUtils.merge(defaultJQueryAutocompleteOptions, options.autocompleteOptions);

    return options;
  },

  // options that must be overwritten
  objectName    : undefined,
  attrName      : undefined,
  searchPath    : undefined,
};


var simpleSearch = {

  /* API returns result as {value : label, value2, label2}
     but jquery autocomplete, expects [{value : label}, {value : label}]
  */
  fixResult :  function(result) {
    var fixed = [];

    $j.each(result, function(value, label) {
      fixed.push({ value : value, label : label})
    });

    return fixed;
  },

  handleSearch : function(dataResponse, response) {
    handleMessages(dataResponse['msgs']);

    if (dataResponse.result) {
      response(simpleSearch.fixResult(dataResponse.result));
    }
  },

  search : function(element, request, response) {
    var $element      = $j(element);

    var searchPath = $element.data('simple_search_options').search_path;

    $j.get(searchPath, { query : request.term }, function(dataResponse) {
      simpleSearch.handleSearch(dataResponse, response);
    });
  },

  handleSelect : function(event, ui) {
    var $element      = $j(event.target);
    var hiddenInputId = $element.data('simple_search_options').hidden_input_id;

    $element.val(ui.item.label);
    $j(hiddenInputId).val(ui.item.value);

    return false;
  },

  for : function(options) {
    options           = defaultSimpleSearchOptions.mergeWith(options);

    var inputId       = buildId(options.get('objectName') + '_' + options.get('attrName'));
    var hiddenInputId = buildId(options.get('objectName') + '_id');
    var $input        = $j(inputId);

    $input.autocomplete(options.get('autocompleteOptions'));
    $input.data('simple_search_options', { 'hidden_input_id' : hiddenInputId, 'search_path' : options.get('searchPath') });
  }
};

var simpleSearchHelper = {
  setup : function(objectName, attrName, searchPath, simpleSearchResourceOptions) {
    var defaultOptions = {
      searchPath : searchPath,
      objectName : objectName,
      attrName   : attrName,
    };

    var options = optionsUtils.merge(defaultOptions, simpleSearchResourceOptions);
    simpleSearch.for(options);
  },
};