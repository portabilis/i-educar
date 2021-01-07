$campoEscolaDestinoExterna = $j('#escola_destino_externa');
$campoEstadoEscolaDestinoExterna = $j('#estado_escola_destino_externa');
$campoMunicipioEscolaDestinoExterna = $j('#municipio_escola_destino_externa');

$campoEscolaDestinoExterna.closest("tr").hide();
$campoEstadoEscolaDestinoExterna.closest("tr").hide();
$campoMunicipioEscolaDestinoExterna.closest("tr").hide();

var msg = '<b>Novidade</b>: O processo de transferência foi simplificado!<br/>' +
  'Agora você não precisa mais informar o tipo de transferência<br/>' +
  'que será utilizado. Basta preencher os campos obrigatórios, e<br/>' +
  'o aluno ficará com a situação de transferido automaticamente.';

$j('<p>').addClass('right-top-notice notice')
  .html(stringUtils.toUtf8(msg))
  .appendTo($j('#tr_nm_aluno').closest('td'));

$j('#escola_em_outro_municipio').change(mostraEscolaOutroMunicipio);

function mostraEscolaOutroMunicipio() {
  if ($j(this).is(':checked')) {
    $campoEscolaDestinoExterna.closest("tr").show();
    $campoEstadoEscolaDestinoExterna.closest("tr").show();
    $campoMunicipioEscolaDestinoExterna.closest("tr").show();
    $j('#ref_cod_instituicao').closest("tr").hide();
    $j('#ref_cod_instituicao').closest("select").val(1);
    $j('#ref_cod_escola').closest("tr").hide();
    $j('#ref_cod_escola').val('Outra');
  } else {
    $campoEscolaDestinoExterna.closest("tr").hide();
    $campoEstadoEscolaDestinoExterna.closest("tr").hide();
    $campoMunicipioEscolaDestinoExterna.closest("tr").hide();
    $j('#ref_cod_instituicao').closest("tr").show();
    $j('#ref_cod_escola').closest("tr").show();
    $campoEscolaDestinoExterna.val("");
    $campoEstadoEscolaDestinoExterna.val("");
    $campoMunicipioEscolaDestinoExterna.val("");
  }
}
