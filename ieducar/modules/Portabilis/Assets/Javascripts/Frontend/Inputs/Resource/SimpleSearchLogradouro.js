var simpleSearchLogradouroOptions = {

  params : { 
    municipio_id : function() {
      return $j('#municipio_id').val() 
    },
    exibir_municipio : function() {
      return $j('#exibir_municipio').length > 0
    }
  },

  canSearch : function() {

    if ($j('#municipio_id').length > 0 && !$j('#municipio_id').val()) {
      alert('Selecione um munic\u00edpio.');
      return false;
    }
    
    return true;
 }
};
