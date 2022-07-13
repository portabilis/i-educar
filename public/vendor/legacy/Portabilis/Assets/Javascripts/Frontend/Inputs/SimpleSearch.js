// simple search input

var defaultJQueryAutocompleteOptions = {
  minLength   : 1,
  autoFocus   : true,
  source      : function(request, response) { return simpleSearch.search(this.element, request, response) },
  select      : function(event, ui) { return simpleSearch.handleSelect(event, ui) },

  // options that can be overwritten
  change      : function (event, ui) {
    $element = $j(event.target);
    $hiddenField = $element.data('hidden-input-id');

    if($element.val() == ""){
      $hiddenField.val("");
    }
  },
  // close      : function (event, ui) {},
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

  // additional search params to send to api
  params        : {},

  // #TODO implementar validacao dependencia
  // elements that presence is required to do the search
  // requiresPresenceOf : [ /* $j('#someElement') */ ],

  canSearch : function() { return true; }
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

  search : function(element, request, response) {
    var $element      = $j(element);

    if ($element.data('simple-search-options').canSearch()) {
      var hiddenInputId = $element.data('hidden-input-id');
      var searchPath    = $element.data('simple-search-options').searchPath;
      var params        = { query : request.term };

      // inject additional params
      $j.each($element.data('simple-search-options').params, function(name, value) {
        params[name] = typeof value == 'function' ? value() : value;
      });

      // clear the hidden id, because it will be set when the user select another result.
      $j(hiddenInputId).val('');

      $j.get(searchPath, params, function(dataResponse) {
        simpleSearch.handleSearch(dataResponse, response);
      });
   }
  },

  handleSearch : function(dataResponse, response) {
    handleMessages(dataResponse['msgs']);

    if (dataResponse.result) {
      response(simpleSearch.fixResult(dataResponse.result));
    }
  },

  handleSelect : function(event, ui) {
    var $element      = $j(event.target);
    var hiddenInputId = $element.data('hidden-input-id');

    $element.val(ui.item.label);
    $j(hiddenInputId).val(ui.item.value);

    /* Alterar valor de hiddenInputs no jQuery não chama o método 'change' por padrão, então forçamos
       o elemento a disparar esse método (caso estiver implementado) através do método trigger */
    $j(hiddenInputId).trigger('change');


    return false;
  },

  /* limpa o texto dos inputs simple search, cujo hidden id é obrigatorio porem está vazio,
     para que ao tentar gravar o formulário o campo de texto obrigatório (e vazio) seja validado
     isto ocorre quando é informado qualquer valor, sem selecionar um resultado. */
  fixupRequiredFieldsValidation : function() {
    $j('input[type=hidden].simple-search-id.obrigatorio').each(function(index, element){
      var $element = $j(element);

      if (! $element.val())
        $j(buildId($element.data('for'))).val('');
    });
  },

  setup : function(options) {
    options      = defaultSimpleSearchOptions.mergeWith(options);

    var attrName = options.get('attrName');
    if (attrName) { attrName = '_' + attrName; }

    var inputId = buildId(options.get('objectName') + attrName);
    var hiddenInputId = buildId(options.get('objectName') + '_id');

    var $input        = $j(inputId);
    var $hiddenInput  = $j(hiddenInputId);

    $input.data('simple-search-options', options);
    $input.data('hidden-input-id', $hiddenInput);

    $hiddenInput.addClass('simple-search-id');
    $hiddenInput.attr('data-for', $input.attr('id'));

    if ($input.hasClass('obrigatorio'))
      $hiddenInput.addClass('obrigatorio required');

    $input.keyup(function() {
      $element = $j($hiddenInput.target);
      if ($element.val() == '') {
        $j(hiddenInputId).val('');
      }
    });

    $input.autocomplete(options.get('autocompleteOptions'));
  }
};


var simpleSearchHelper = {
  setup : function(objectName, attrName, searchPath, searchResourceOptions) {
    var defaultOptions = {
      searchPath : searchPath,
      objectName : objectName,
      attrName   : attrName
    };

    var options = optionsUtils.merge(defaultOptions, searchResourceOptions);
    simpleSearch.setup(options);
  }
};
