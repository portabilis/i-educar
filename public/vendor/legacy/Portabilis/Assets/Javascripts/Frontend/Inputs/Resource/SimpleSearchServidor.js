var simpleSearchServidorOptions = {

  params : { 
    escola_id : function() { 
      return $j('#ref_cod_escola').val() 
    },

  },

  canSearch : function() { 

    if ($j('#ref_cod_escola').hasClass('obrigatorio') && ! $j('#ref_cod_escola').val()) {
      alert('Selecione uma escola.');
      return false;
    }
    
    return true;
 }
};
