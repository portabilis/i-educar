(function($){
  $(document).ready(function(){
    $j.each(arrayOptions, function(id, e) {
      var element = e.element;
      var values = e.values;
      setTimeout(function() {
        element.val(values);
        element.trigger('chosen:updated');
      }, 500);
    });
  });
})(jQuery);
