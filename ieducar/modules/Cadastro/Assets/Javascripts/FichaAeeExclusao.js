(function ($) {
    $(document).ready(function () {
        var id = $j('#id').val();
        var copy = $j('#copy').val();

        if (isNaN(id) || id === '')
            return;

        if (!isNaN(id) && copy)
            return;

        var ficha_aee_id = $j('#id').val();

        var submitButton = $j('#btn_excluir');
        submitButton.removeAttr('onclick');

        submitButton.click(function () {
            if (confirm('Este procedimento irá excluir a Ficha AEE. Tem certeza que deseja continuar?')) {
                excluirFichaAee();
            }
        });


        function excluirFichaAee() {
            var urlForExcluirFichaAee = postResourceUrlBuilder.buildUrl('/module/Api/FichaAee', 'excluir-ficha-aee', {});

            var options = {
                type: 'POST',
                url: urlForExcluirFichaAee,
                dataType: 'json',
                data: {
                    ficha_aee_id: ficha_aee_id
                },
                success: handleExcluirFichaAee
            };

            postResource(options);
        }

        function handleExcluirFichaAee(response) {
            if (response.result) {
                messageUtils.success('Ficha AEE excluída com sucesso!');

                delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_ficha_aee_lst.php", '_self'));
            } else {
                messageUtils.success('Erro desconhecido ocorreu.');
            }
        }

        function urlHelper(href, mode) {
            Object.assign(document.createElement('a'), {
                target: mode,
                href: href,
            }).click();
        }

        function delay(time) {
            return new Promise(resolve => setTimeout(resolve, time));
        }

    });
})(jQuery);
