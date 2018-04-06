(function($){
  $(document).ready(function(){
    $j.each(arrayOptions, function(id, values) {
      values.element.trigger('chosen:updated');
      getValues(values.element, values.values);
    });

    function getValues(element, val) {
      var options = {
        success  : function(){
          if(val){
            $j.each(val, function(id, values) {
              element.children("[value=" + values + "]").attr('selected', '');
            });
          }
          element.trigger('chosen:updated');
        },
      };
      getResource(options);
    }
  });
})(jQuery);
