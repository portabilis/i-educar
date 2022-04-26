(function($){
    $(document).ready(function(){
        var tableElemento = document.getElementsByClassName("tableDetalhe")[0];
        var iDElemento = tableElemento.children[0].children[1].children[1];
        var planejamento_aula_id = iDElemento.innerHTML;

        var submitButton = $j('.btn_small');
        submitButton.removeAttr('onclick');

        submitButton.click(function () {
            excluirPlanoAula();
        });

        async function excluirPlanoAula () {
            var pathExcluirPlano = '/module/Api/PlanejamentoAula?oper=post&resource=excluir-plano-aula',
            paramsExcluirPlano = {
                planejamento_aula_id: planejamento_aula_id
            };

            await $j.post(pathExcluirPlano, paramsExcluirPlano, function (dataResponse) {
                console.log(dataResponse);

                if(dataResponse.result) {
                    messageUtils.success('Plano de aula excluído com sucesso!');
    
                    delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_planejamento_de_aula_lst.php", '_self'));
                } else {
                    messageUtils.success('Erro desconhecido ocorreu.');
                }
            });
        }

        function delay(time) {
            return new Promise(resolve => setTimeout(resolve, time));
        }

        function urlHelper(href, mode) {
            Object.assign(document.createElement('a'), {
            target: mode,
            href: href,
            }).click();
        }
    });
})(jQuery);
