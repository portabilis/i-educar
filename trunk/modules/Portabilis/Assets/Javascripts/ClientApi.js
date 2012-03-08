// url builders
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
      att : resourceName,
      oper : 'get'
    };

    return resourceUrlBuilder.buildUrl(urlBase, $j.extend(vars, additionalVars));
  }
};
