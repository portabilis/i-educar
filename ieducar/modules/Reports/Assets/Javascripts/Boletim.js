$j('#manual').closest('tr').hide();

var handleGetTipoBoletimTurma = function(dataResponse) {
  if ((dataResponse['tipo-boletim'] == 'portabilis_boletim_educ_infantil_semestral') || 
     (dataResponse['tipo-boletim'] == 'portabilis_boletim_primeiro_ano_trimestral'))
    $j('#manual').closest('tr').show();
  else
    $j('#manual').closest('tr').hide();
}

var getTipoBoletimTurma = function() {
  var $turmaField = $j(this);

  if ($turmaField.val()) {

    var additionalVars = {
      id : $turmaField.val(),
    };

    var options = {
      url      : getResourceUrlBuilder.buildUrl('/module/Api/turma', 'tipo-boletim', additionalVars),
      dataType : 'json',
      data     : {},
      success  : handleGetTipoBoletimTurma,
    };

    getResource(options);
  }
}

$j('#ref_cod_turma').change(getTipoBoletimTurma);
