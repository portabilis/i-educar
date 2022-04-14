var simpleSearchAcervoOptions = {

  params : {
    biblioteca_id : function() {
      return $j('#ref_cod_biblioteca').val()
    },

  },

  canSearch : function() {

    if ($j('#ref_cod_biblioteca').hasClass('obrigatorio') && ! $j('#ref_cod_biblioteca').val()) {
      alert('Selecione uma biblioteca.');
      return false;
    }

    return true;
 }
};
