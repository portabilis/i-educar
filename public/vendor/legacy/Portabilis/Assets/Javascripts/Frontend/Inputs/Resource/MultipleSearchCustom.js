(function($){
  let validValue = (value) => {
    return value !== undefined && value !== null && value !== "";
  };

  $(document).ready(function(){
    $j.each(arrayOptions, function(id, e) {
      var element = e.element;
      var values = e.values;

      if (values && values.constructor === Array ) {
        values = values.filter((x) => validValue(x));
      } else if(!validValue(values) ) {
        values = [];
      }

      setTimeout(function() {
        element.val(values);
        element.trigger('chosen:updated');
      }, 500);
      setTimeout(function() {
        element.val(values);
        element.trigger('chosen:updated');
      }, 100);
    });
  });
})(jQuery);
