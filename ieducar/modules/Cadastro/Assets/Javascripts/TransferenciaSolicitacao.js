$j(document).ready(function () {

    const $campoEscolaDestinoExterna = $j('#escola_destino_externa');
    const $campoEstadoEscolaDestinoExterna = $j('#estado_escola_destino_externa');
    const $campoMunicipioEscolaDestinoExterna = $j('#municipio_escola_destino_externa');
    const $campoCodInstituicao = $j('#ref_cod_instituicao');
    const $campoCodEscola = $j('#ref_cod_escola');
    const $campoEscolaOutroMunicipio = $j('#escola_em_outro_municipio');
    const $submitButton = $j('#btn_enviar');

    $campoEscolaDestinoExterna.closest("tr").hide();
    $campoEstadoEscolaDestinoExterna.closest("tr").hide();
    $campoMunicipioEscolaDestinoExterna.closest("tr").hide();

    const msg = '<b>Novidade</b>: O processo de transferência foi simplificado!<br/>' +
        'Agora você não precisa mais informar o tipo de transferência<br/>' +
        'que será utilizado. Basta preencher os campos obrigatórios, e<br/>' +
        'o aluno ficará com a situação de transferido automaticamente.';

    $j('<p>').addClass('right-top-notice notice')
        .html(stringUtils.toUtf8(msg))
        .appendTo($j('#tr_nm_aluno').closest('td'));

    $campoCodInstituicao.makeRequired();
    $campoCodEscola.makeRequired();
    $campoEscolaOutroMunicipio.change(mostraEscolaOutroMunicipio);

    function mostraEscolaOutroMunicipio() {
        if ($j(this).is(':checked')) {
            $campoEscolaDestinoExterna.closest("tr").show();
            $campoEscolaDestinoExterna.makeRequired();
            $campoEstadoEscolaDestinoExterna.closest("tr").show();
            $campoEstadoEscolaDestinoExterna.makeRequired();
            $campoMunicipioEscolaDestinoExterna.closest("tr").show();
            $campoMunicipioEscolaDestinoExterna.makeRequired();
            $campoCodInstituicao.closest("tr").hide();
            $campoCodEscola.closest("tr").hide();
        } else {
            $campoEscolaDestinoExterna.closest("tr").hide();
            $campoEscolaDestinoExterna.makeUnrequired();
            $campoEstadoEscolaDestinoExterna.closest("tr").hide();
            $campoEstadoEscolaDestinoExterna.makeUnrequired();
            $campoMunicipioEscolaDestinoExterna.closest("tr").hide();
            $campoMunicipioEscolaDestinoExterna.makeUnrequired();
            $campoCodInstituicao.closest("tr").show();
            $campoCodInstituicao.makeRequired();
            $campoCodEscola.closest("tr").show();
            $campoCodEscola.makeRequired();
            $campoEscolaDestinoExterna.val("");
            $campoEstadoEscolaDestinoExterna.val("");
            $campoMunicipioEscolaDestinoExterna.val("");
        }
    }

    $submitButton.removeAttr('onclick');
    $submitButton.click(validaSubmit);

    function validaSubmit() {
        if (!$campoEscolaOutroMunicipio.is(':checked')) {
            if ($campoCodInstituicao.closest("select").val() === '') {
                return alert('É necessário informar a instituição');
            }
            if ($campoCodEscola.closest("select").val() === '') {
                return alert('É necessário informar a escola');
            }
        } else {
            if ($campoEscolaDestinoExterna.val() == '') {
                return alert('É necessário informar o nome da escola.');
            }

            if ($campoEstadoEscolaDestinoExterna.val() == '') {
                return alert('É necessário informar o estado da escola.');
            }

            if ($campoMunicipioEscolaDestinoExterna.val() == '') {
                return alert('É necessário informar o município da escola.');
            }

        }
        acao();
    }
});
