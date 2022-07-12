var simpleSearchAlunoOptions = {

  params : { 
    escola_id : function() { 
      return $j('#ref_cod_escola').val() 
    },

  },

  canSearch : function() { 

    if (! $j('#ref_cod_escola').val()) {
      alert('Selecione uma escola.');
      return false;
    }
    
    return true;
 }
};
