var simpleSearchEscolaOptions = {

  params : {
    instituicao_id : function() {
      return $j('#ref_cod_instituicao').val()
    },
  },

  canSearch : function() {

    if (! $j('#ref_cod_instituicao').val()) {
      alert(stringUtils.toUtf8('Selecione uma instituição.'));
      return false;
    }
    return true;
 }
};
