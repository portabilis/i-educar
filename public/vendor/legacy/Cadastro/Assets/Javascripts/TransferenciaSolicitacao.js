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

    $campoCodInstituicao.makeRequired();
    $campoCodEscola.makeRequired();
    $campoEscolaOutroMunicipio.change(mostraEscolaOutroMunicipio);

    function mostraEscolaOutroMunicipio() {
        if ($j(this).is(':checked')) {
            $campoEscolaDestinoExterna.closest("tr").show();
            $campoEstadoEscolaDestinoExterna.closest("tr").show();
            $campoMunicipioEscolaDestinoExterna.closest("tr").show();
            $campoCodInstituicao.closest("tr").hide();
            $campoCodEscola.closest("tr").hide();
        } else {
            $campoEscolaDestinoExterna.closest("tr").hide();
            $campoEstadoEscolaDestinoExterna.closest("tr").hide();
            $campoMunicipioEscolaDestinoExterna.closest("tr").hide();
            $campoCodInstituicao.closest("tr").show();
            $campoCodInstituicao.makeRequired();
            $campoCodEscola.closest("tr").show();
            $campoCodEscola.makeRequired();
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
        }
        acao();
    }
});
