var simpleSearchDistrictOptions = {

  params: {
    city_id: function () {
      return $j('#city_id').val()
    }
  },

  canSearch: function () {
    if (!$j('#city_id').val()) {
      alert('Selecione uma cidade.');
      return false;
    }

    return true;
  }
};
