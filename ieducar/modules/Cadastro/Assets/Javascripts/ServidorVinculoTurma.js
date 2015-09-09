$j(document).ready(function() {
  
  function fiupMultipleSearchSize(){

    $j('.search-field input').css('height', '25px');  
    
  }

  fiupMultipleSearchSize();

  $componentecurricular = $j('#componentecurricular');

  $componentecurricular.trigger('chosen:updated');

  var handleGetComponenteCurricular = function(dataResponse) {
    testezin = dataResponse['componentecurricular'];
    
    $j.each(dataResponse['componentecurricular'], function(id, value) {
      
      $componentecurricular.children("[value=" + value + "]").attr('selected', '');
    });

    $componentecurricular.trigger('chosen:updated');
  }

  var getComponenteCurricular = function() {
        
    var $id = $j('#id');
    
    if ($id.val()!='') {    
      var additionalVars = {
        id : $id.val(),
      };

      var options = {
        url      : getResourceUrlBuilder.buildUrl('/module/Api/componenteCurricular', 'componentecurricular-search', additionalVars),
        dataType : 'json',
        data     : {},
        success  : handleGetComponenteCurricular,
      };

      getResource(options);
    }
  }

  getComponenteCurricular();  
});