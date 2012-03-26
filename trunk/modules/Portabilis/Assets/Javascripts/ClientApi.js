var resourceUrlBuilder = {
  buildUrl : function(urlBase, vars){

    _vars = '';
    for(varName in vars){
      _vars += '&'+varName+'='+vars[varName];
    }
    return urlBase + '?' + _vars;
  }
};

var getResourceUrlBuilder = {
  buildUrl : function(urlBase, resourceName, additionalVars){

    var vars = {
      oper : 'get'
    };

    if (resourceName)
      vars.resource = resourceName;

    if (additionalVars)
      vars = $j.extend(vars, additionalVars);

    return resourceUrlBuilder.buildUrl(urlBase, vars);
  }
};

var postResourceUrlBuilder = {
  buildUrl : function(urlBase, resourceName, additionalVars){

    var vars = {
      oper : 'post'
    };

    if (resourceName)
      vars.resource = resourceName;

    if (additionalVars)
      vars = $j.extend(vars, additionalVars);

    return resourceUrlBuilder.buildUrl(urlBase, vars);
  }
};

var handleErrorGetResources = function(response){
  alert('Erro ao alterar recurso, detalhes:' + response.responseText);
  safeLog(response);
};

var handleErrorPost = function(response){
  handleMessages([{type : 'error', msg : 'Erro ao alterar recurso, detalhes:' + response.responseText}], '');
  safeLog(response);
};

var getResources = function(options, errorCallback) {
  $j.ajax(options).error(errorCallback || handleErrorGetResources);
};

var postResource = function(options, errorCallback){
  $j.ajax(options).error(errorCallback);
};
