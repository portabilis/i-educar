// multiple search input

var multipleSearch = {
  for : function(options) {
  }
};

var multipleSearchHelper = {
  setup : function(objectName, attrName, searchPath, searchResourceOptions) {
    var defaultOptions = {
      searchPath : searchPath,
      objectName : objectName,
      attrName   : attrName,
    };

    var options = optionsUtils.merge(defaultOptions, searchResourceOptions);
    multipleSearch.for(options);
  },
};