$j(document).ready(function () {
  const optional = $j('#search-postal-code').data('optional') === 1;

  var disableAddressing = function (flag) {
    $j('#search-postal-code').css('opacity', flag ? 0.5 : 1);
    $j('#address').attr('disabled', flag);
    $j('#number').attr('disabled', flag);
    $j('#complement').attr('disabled', flag);
    $j('#neighborhood').attr('disabled', flag);
    $j('#city_city').attr('disabled', flag);
  };

  var searchCep = function () {
    var postalCode = $j('#postal_code').val();
    var regexp = /[0-9]{5}\-[0-9]{3}/;
    var valid = regexp.test(postalCode);

    if (valid) {
      $j('#postal_code_search_loading').css('visibility', 'visible');

      $j.get('/api/postal-code/' + postalCode.replace(/\D/g, '')).done(function (res) {
        $j('#address').val(res.address);
        $j('#complement').val(res.complement);
        $j('#neighborhood').val(res.neighborhood);
        $j('#city_id').val(res.city.id);
        $j('#city_city').val(res.city.id + ' - ' + res.city.name + ' (' + res.state_abbreviation + ')');
      }).always(function() {
        $j('#postal_code_search_loading').css('visibility', 'hidden');
        if (!optional) {
          disableAddressing(false);
        }
      });
    } else if (!optional) {
        disableAddressing(true);
      }
  };

  var changePostalCode = function () {
    var postalCode = $j('#postal_code').val();
    var regexp = /[0-9]{5}\-[0-9]{3}/;
    var valid = regexp.test(postalCode);

    if (!optional) {
      disableAddressing(!valid);
    }
  };

  $j('#search-postal-code').click(searchCep);
  $j('#postal_code').change(changePostalCode);
  $j('#postal_code').keyup(changePostalCode);
});
