$j(document).ready(function(){

  $j("#ano").on('change', function(){
    $j("#ref_cod_serie").val("");
    $j("#ref_cod_turma").val("");
  });

  if ($j("#ref_cod_candidato_fila_unica").val()){
    $j("#ref_cod_instituicao").unbind();
    getEscolasCandidato();
  }

  function getEscolasCandidato() {
    var url = getResourceUrlBuilder.buildUrl('/module/Api/FilaUnica',
      'escolas-candidato',
      {cod_candidato_fila_unica : $j("#ref_cod_candidato_fila_unica").val()});

    var options = {
      url      : url,
      dataType : 'json',
      async    : false,
      success  : function (response){
        var comboEscola = $j('#ref_cod_escola');
        comboEscola.empty();
        comboEscola.trigger("chosen:updated");
        comboEscola.append('<option value="">Selecione uma escola</option>');
        if(response.escolas){
          $j.each(response['escolas'], function (key, escola) {
            comboEscola.append('<option value="' + escola.ref_cod_escola + '">' + escola.nome + '</option>');
          });
        }
        comboEscola.trigger("chosen:updated");
      }
    };
    getResources(options);
  }

});
