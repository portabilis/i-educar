(function($){
  $(document).ready(function(){

    function getSeries(){
      var urlForGetSerieMultipleSearch = getResourceUrlBuilder.buildUrl('/module/Api/Serie', 'series-curso-grouped');
      var options = {
        url      : urlForGetSerieMultipleSearch,
        dataType : 'json',
        success  : handleGetSerie
      };
      getResources(options);
    }

    getSeries();

    function handleGetSerie(response) {
      var selectOptions = response['options'];
      $j('select[id^=multiple_search_serie]').each(function () {
        var opt = montaOptions(selectOptions);
        updateChozen($j(this), opt);
      });

    }

    function updateChozen(input, values){
      input.append(values);
      input.trigger("chosen:updated");
    };

    function montaOptions(values) {
      var opt = '';
      $j.each(values, function(index, value){
         opt += '<optgroup label="' + value.nome + '">';
        $j.each(value.series, function (index2, value2) {
          opt +='<option value="' + index2 + '"> ' + value2 + '</option>';
        })
        opt +='</optgroup>';
      });

      return opt;
    }

    $j.each(arrayOptions, function(id, values) {
      values.element.trigger('chosen:updated');
      getValues(values.element, values.values);
    });

    function getValues(element, val) {
      var options = {
        success  : function(){
          if(val){
            element.val(val);
            element.trigger('chosen:updated');
          }
        },
      };
      getResource(options);
    }

  });
})(jQuery);
