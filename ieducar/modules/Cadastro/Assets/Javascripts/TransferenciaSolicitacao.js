$j(document).ready(function () {
  $campoEscolaDestinoExterna = $j('#escola_destino_externa');
  $campoEstadoEscolaDestinoExterna = $j('#estado_escola_destino_externa');
  $campoMunicipioEscolaDestinoExterna = $j('#municipio_escola_destino_externa');

  $campoEscolaDestinoExterna.closest("tr").hide();
  $campoEstadoEscolaDestinoExterna.closest("tr").hide();
  $campoMunicipioEscolaDestinoExterna.closest("tr").hide();

  let msg = '<b>Novidade</b>: O processo de transferência foi simplificado!<br/>' +
    'Agora você não precisa mais informar o tipo de transferência<br/>' +
    'que será utilizado. Basta preencher os campos obrigatórios, e<br/>' +
    'o aluno ficará com a situação de transferido automaticamente.';

  $j('<p>').addClass('right-top-notice notice')
    .html(stringUtils.toUtf8(msg))
    .appendTo($j('#tr_nm_aluno').closest('td'));

  $j('#ref_cod_instituicao').makeRequired();
  $j('#ref_cod_escola').makeRequired();
  $j('#escola_em_outro_municipio').change(mostraEscolaOutroMunicipio);

  function mostraEscolaOutroMunicipio() {
    if ($j(this).is(':checked')) {
      $campoEscolaDestinoExterna.closest("tr").show();
      $campoEstadoEscolaDestinoExterna.closest("tr").show();
      $campoMunicipioEscolaDestinoExterna.closest("tr").show();
      $j('#ref_cod_instituicao').closest("tr").hide();
      $j('#ref_cod_escola').closest("tr").hide();
    } else {
      $campoEscolaDestinoExterna.closest("tr").hide();
      $campoEstadoEscolaDestinoExterna.closest("tr").hide();
      $campoMunicipioEscolaDestinoExterna.closest("tr").hide();
      $j('#ref_cod_instituicao').closest("tr").show();
      $j('#ref_cod_instituicao').makeRequired();
      $j('#ref_cod_escola').closest("tr").show();
      $j('#ref_cod_escola').makeRequired();
      $campoEscolaDestinoExterna.val("");
      $campoEstadoEscolaDestinoExterna.val("");
      $campoMunicipioEscolaDestinoExterna.val("");
    }
  }

  function validaSubmit() {
    if (!$j('#escola_em_outro_municipio').is(':checked')) {
      if ($j('#ref_cod_instituicao').closest("select").val() === '') {
        return alert('É necessário informar a instituição');
      }
      if ($j('#ref_cod_escola').closest("select").val() === '') {
        return alert('É necessário informar a escola');
      }
    }
    acao();
  }
  });
