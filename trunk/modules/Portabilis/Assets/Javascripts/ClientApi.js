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

var putResourceUrlBuilder = {
  buildUrl : function(urlBase, resourceName, additionalVars){

    var vars = {
      oper : 'put'
    };

    if (resourceName)
      vars.resource = resourceName;

    if (additionalVars)
      vars = $j.extend(vars, additionalVars);

    return resourceUrlBuilder.buildUrl(urlBase, vars);
  }
};

var deleteResourceUrlBuilder = {
  buildUrl : function(urlBase, resourceName, additionalVars){

    var vars = {
      oper : 'delete'
    };

    if (resourceName)
      vars.resource = resourceName;

    if (additionalVars)
      vars = $j.extend(vars, additionalVars);

    return resourceUrlBuilder.buildUrl(urlBase, vars);
  }
};

var handleErrorOnGetResource = function(response){
  alert('Erro ao obter recurso, detalhes:' + response.responseText);
  safeLog(response);
};

var handleErrorOnPostResource = function(response){
  handleMessages([{type : 'error', msg : 'Erro ao alterar recurso, detalhes:' + response.responseText}], '');
  safeLog(response);
};

var handleErrorOnPutResource = function(response){
  handleMessages([{type : 'error', msg : 'Erro ao alterar recurso, detalhes:' + response.responseText}], '');
  safeLog(response);
};

function handleErrorOnDeleteResource(response){
  handleMessages([{type : 'error', msg : 'Erro ao remover recurso, detalhes:' + response.responseText}], '');
  safeLog(response);
}

var getResource = function(options, errorCallback) {
  $j.ajax(options).error(errorCallback || handleErrorOnGetResource);
};

var getResources = function(options, errorCallback) {
  getResource(options, errorCallback);
};

var postResource = function(options, errorCallback){
  $j.ajax(options).error(errorCallback || handleErrorOnPostResource);
};

var putResource = function(options, errorCallback){
  $j.ajax(options).error(errorCallback || handleErrorOnPutResource);
};

function deleteResource(options, errorCallback){
  $j.ajax(options).error(errorCallback || handleErrorOnDeleteResource);
}
