function fixupTabelaMatriculas() {
  var $parentTd = $j('.botaolistagem[value=" Voltar "]').closest('tr').next().children().first();
      $parentTd.empty().removeAttr('bgcolor').removeAttr('style');

  $j('<p>').html(stringUtils.toUtf8('<strong>Matrículas:</strong>')).appendTo($parentTd);

  var $table = $j('<table>').attr('id', 'matriculas').addClass('styled horizontal-expand').hide();
  var $tr    = $j('<tr>');

  $j('<th>').html('').appendTo($tr);
  $j('<th>').html('Ano').appendTo($tr);
  $j('<th>').html(stringUtils.toUtf8('Situação')).appendTo($tr);
  $j('<th>').html('Turma').appendTo($tr);
  $j('<th>').html(stringUtils.toUtf8('Série')).appendTo($tr);
  $j('<th>').html('Curso').appendTo($tr);
  $j('<th>').html('Escola').appendTo($tr);
  $j('<th>').html('Entrada').appendTo($tr);
  $j('<th>').html(stringUtils.toUtf8('Saída')).appendTo($tr);

  $tr.appendTo($table);
  $table.appendTo($parentTd);
}

fixupTabelaMatriculas();


// api client

var handleGetMatriculas = function(dataResponse) {
  try{
    handleMessages(dataResponse.msgs);

    var $matriculasTable      = $j('#matriculas');
    var transferenciaEmAberto = false;

    $j.each(dataResponse.matriculas, function(index, matricula) {
      var $tr = $j('<tr>');

      if (matricula.user_can_access) {
        var linkToMatricula = $j('<a>').attr('href', 'educar_matricula_det.php?cod_matricula=' + matricula.id)
                                       .html('Visualizar')
                                       .addClass('decorated');

      }
      else
        var linkToMatricula = '';

      $j('<td>').html(linkToMatricula).appendTo($tr).addClass('center');
      $j('<td>').html(matricula.ano).appendTo($tr);
      $j('<td>').html(matricula.situacao).appendTo($tr);
      $j('<td>').html(matricula.turma_nome).appendTo($tr);
      $j('<td>').html(matricula.serie_nome).appendTo($tr);
      $j('<td>').html(matricula.curso_nome).appendTo($tr);
      $j('<td>').html(matricula.escola_nome).appendTo($tr);
      $j('<td>').html(matricula.data_entrada).appendTo($tr);
      $j('<td>').html(matricula.data_saida).appendTo($tr);

      if (matricula.transferencia_em_aberto) {
        transferenciaEmAberto = true;
        $tr.addClass('notice');
      }

      $tr.appendTo($matriculasTable);
    });


    if(dataResponse.matriculas.length < 1) {
      var $p = $j('<p>').html(stringUtils.toUtf8('Aluno sem matrículas, ')).addClass('notice simple-block');

      $j('<a>').attr('href', 'educar_matricula_cad.php?ref_cod_aluno=' + $j('#aluno_id').val())
               .html('matricular aluno.')
               .addClass('decorated')
               .appendTo($p);

      $p.appendTo($matriculasTable.parent());
    }
    else if (transferenciaEmAberto) {
      var $p = $j('<p>').html(stringUtils.toUtf8('* Matrícula com solicitação de transferência interna em aberto, '))
                        .addClass('notice simple-block');

      $j('<a>').attr('href', 'educar_matricula_cad.php?ref_cod_aluno=' + $j('#aluno_id').val())
               .html('matricular aluno.')
               .addClass('decorated')
               .appendTo($p);

      $p.appendTo($matriculasTable.parent());
    }

    $matriculasTable.fadeIn('slow');
    $j('body,html').animate({scrollTop: $j('#matriculas').offset().top }, 900);

    $matriculasTable.find('tr:even').addClass('even');
  }
  catch(error) {
    alert('Erro ao carregar matriculas, detalhes:\n\n' + error);

    safeLog('Error details:');
    safeLog(error);

    safeLog('dataResponse details:');
    safeLog(dataResponse);

    throw error;
  }
}

var getMatriculas = function() {
  var data = {
    aluno_id : $j('#aluno_id').val()
  };

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/aluno', 'matriculas'),
    dataType : 'json',
    data     : data,
    success  : handleGetMatriculas
  };

  getResource(options);
}

// when page is ready

$j(document).ready(function() {

  getMatriculas();

}); // ready