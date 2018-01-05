(function($){

  $(document).ready(function(){

    $customElement = $j('#' + elementName);

    $customElement.trigger('chosen:updated');

    var handleGetValues = function() {
      if(value){
        $j.each(value, function(id, values) {

          $customElement.children("[value=" + values + "]").attr('selected', '');
        });
      }

      $customElement.trigger('chosen:updated');
    }

  var getValues = function() {
    var options = {
      success  : handleGetValues,
    };

    getResource(options);
  }

  getValues();

  });
})(jQuery);
