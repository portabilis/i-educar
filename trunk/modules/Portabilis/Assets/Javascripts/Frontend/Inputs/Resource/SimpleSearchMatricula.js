var simpleSearchMatriculaOptions = {

  params : { escola_id : function() { 
    return $j('#ref_cod_escola').val() } 
  },

  canSearch : function() { 
    var can = $j('#ref_cod_escola').val() != '';

    if (! can)
      alert('Selecione uma escola.');
    
    return can;
 }
};
