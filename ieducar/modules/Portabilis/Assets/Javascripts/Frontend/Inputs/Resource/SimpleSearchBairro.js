var simpleSearchBairroOptions = {

  params : { 
    distrito_id : function() {
      return $j('#distrito_id').val() 
    },
    exibir_municipio : function() {
      return $j('#exibir_municipio').length > 0
    }
  },

  canSearch : function() { 

    if ($j('#distrito_id').length > 0 && !$j('#distrito_id').val()) {
      alert('Selecione um distrito.');
      return false;
    }
    
    return true;
 }
};
