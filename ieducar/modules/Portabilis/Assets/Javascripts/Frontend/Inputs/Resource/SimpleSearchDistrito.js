var simpleSearchDistritoOptions = {

  params: {
    city_id: function () {
      return $j('#city_id').val()
    }
  },

  canSearch: function () {
    if (!$j('#city_id').val()) {
      alert('Selecione um teste.');
      return false;
    }

    return true;
  }
};
