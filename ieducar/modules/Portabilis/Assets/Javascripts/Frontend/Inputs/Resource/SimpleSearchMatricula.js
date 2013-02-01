var simpleSearchMatriculaOptions = {

  params : { 
    escola_id : function() { 
      return $j('#ref_cod_escola').val() 
    },

    ano : function() {
      return $j('#ano').val() 
    } 
  },

  canSearch : function() { 

    if (! $j('#ano').val()) {
      alert('Informe o ano.');
      return false;
    }

    if (! $j('#ref_cod_escola').val()) {
      alert('Selecione uma escola.');
      return false;
    }
    
    return true;
 }
};
