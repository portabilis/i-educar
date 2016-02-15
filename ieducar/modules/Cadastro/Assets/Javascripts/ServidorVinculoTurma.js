$j(document).ready(function() {
  
  function fiupMultipleSearchSize(){

    $j('.search-field input').css('height', '25px');  
    
  }

  fiupMultipleSearchSize();

  $componentecurricular = $j('#componentecurricular');

  $selecionarTodosElement = $j('#selecionar_todos');

  $componentecurricular.trigger('chosen:updated');

  var handleGetComponenteCurricular = function(dataResponse) {
    
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

  $selecionarTodosElement.on('change',function(){
    $j('#componentecurricular option').attr('selected', $j(this).prop('checked'));
    $componentecurricular.trigger("chosen:updated");
  });

});