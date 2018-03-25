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
        updateChozen($j(this), selectOptions);
      });

    }

    function updateChozen(input, values){
      $j.each(values, function(index, value){
        var opt = '<optgroup label="' + value.nome + '">';
        $j.each(value.series, function (index2, value2) {
          opt +='<option value="' + index2 + '"> ' + value2 + '</option>';
        })
        opt+='</optgroup>';
        input.append(opt);
      });
      input.trigger("chosen:updated");
    };

  });
})(jQuery);
