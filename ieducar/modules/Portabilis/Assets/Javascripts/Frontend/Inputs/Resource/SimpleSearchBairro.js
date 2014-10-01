var simpleSearchBairroOptions = {

  params : { 
    distrito_id : function() {
      return $j('#distrito_id').val() 
    }
  },

  canSearch : function() { 

    if (! $j('#distrito_id').val() == 0) {
      alert('Selecione um distrito.');
      return false;
    }
    
    return true;
 }
};
