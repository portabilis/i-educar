var handleGetFrequencia = function(dataResponse) {

  var frequencia = parseFloat(dataResponse['frequencia']).toFixed(2);
  var frequencia_final = '';

  if (isNaN(frequencia))
    frequencia= 'N&atilde;o informado';
  else
    frequencia_final = '%';

  $j('#frequencia').html('Frequ&ecirc;ncia: '+frequencia+frequencia_final)
                   .show()
                   .css('display', 'inline');

  $j('#frequencia').addClass( (frequencia>=75 ? 'frequencia-acima' : 'frequencia-abaixo') );
}

$j('<div>').html('')
    .addClass('div-frequencia')
    .attr('id','frequencia')
    .appendTo($j('#matricula').closest('td')).hide();

var getFrequenciaMatricula = function() {
  $j('#frequencia').removeClass('frequencia-acima frequencia-abaixo');

  if($j('#matricula').val()=='')
    $j('#matricula_id').val('');

  var $matriculaField = $j('#matricula_id');
  
  if ($matriculaField.val()!='') {    

    var additionalVars = {
      id : $matriculaField.val(),
    };

    var options = {
      url      : getResourceUrlBuilder.buildUrl('/module/Api/matricula', 'frequencia', additionalVars),
      dataType : 'json',
      data     : {},
      success  : handleGetFrequencia,
    };

    getResource(options);
  }else{
    $j('#frequencia').hide();
  }
}

$j('#matricula').blur(getFrequenciaMatricula);