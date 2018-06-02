var simpleSearchDistritoOptions = {

  params : { 
    municipio_id : function() {
      return $j('#municipio_id').val() 
    }
  },

  canSearch : function() { 

    if (! $j('#municipio_id').val()) {
      alert('Selecione um munic\u00edpio.');
      return false;
    }
    
    return true;
 }
};
