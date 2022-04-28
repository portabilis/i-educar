(function($){
    $(document).ready(function(){
        var registrosAula = [];

        var tableElemento = document.getElementsByClassName("tableDetalhe")[0];
        var iDElemento = tableElemento.children[0].children[1].children[1];
        var planejamento_aula_id = iDElemento.innerHTML;

        var submitButton = $j('.btn_small');
        submitButton.removeAttr('onclick');

        submitButton.click(function () {
            tentaExcluirPlanoAula();
        });

        async function tentaExcluirPlanoAula () {
            var pathVerificarPlanoAulaSendoUsado = '/module/Api/PlanejamentoAula?oper=post&resource=verificar-plano-aula-sendo-usado',
            paramsVerificarPlanoAulaSendoUsado = {
                planejamento_aula_id: planejamento_aula_id
            };

            await $j.post(pathVerificarPlanoAulaSendoUsado, paramsVerificarPlanoAulaSendoUsado, function (dataResponse) {
                console.log(dataResponse);
                handleTentaExcluirPlanoAula(dataResponse);
            });
        }

        function handleTentaExcluirPlanoAula (response) {
            registrosAula = response.conteudos_ids;

            if (registrosAula.length == 0) {
                excluirPlanoAula();
            } else {
                openModal();
            }
        }

        async function excluirPlanoAula () {
            var pathExcluirPlano = '/module/Api/PlanejamentoAula?oper=post&resource=excluir-plano-aula',
            paramsExcluirPlano = {
                planejamento_aula_id: planejamento_aula_id
            };

            await $j.post(pathExcluirPlano, paramsExcluirPlano, function (dataResponse) {
                console.log(dataResponse);
                handleExcluirPlanoAula(dataResponse);
            });
        }

        function handleExcluirPlanoAula (response) {
            if(response.result) {
                messageUtils.success('Plano de aula excluído com sucesso!');

                delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_planejamento_de_aula_lst.php", '_self'));
            } else {
                messageUtils.success('Erro desconhecido ocorreu.');
            }
        }

        function openModal() {
            var quantidadeRegistrosAula = registrosAula.length;
            var quantidadeRegistrosAulaConteudos = 0;

            registrosAula.forEach(registroAula => {
                quantidadeRegistrosAulaConteudos += registroAula[1];
            });

            $j("#dialog-warning-excluir-plano-aula").find('#msg').html(getMessageExcluirPlanoAula(quantidadeRegistrosAula, quantidadeRegistrosAulaConteudos));
            $j("#dialog-warning-excluir-plano-aula").dialog("open");
        }

        function closeModal() {
            registrosAula = [];

            $j("#dialog-warning-excluir-plano-aula").dialog('close');
        }

        function getMessageExcluirPlanoAula(quantidadeRegistrosAula, quantidadeRegistrosAulaConteudos) {
            return ` \
                <span> \
                    Para concluir esta ação: \
                </span><br> \
                <ul> \
                    <li> \
                        A(s) dependência(s) abaixo deve(m) ser resolvidas primeiro: \
                    </li> \
                </ul> \
                <span> \
                    Efeitos colaterais em \
                    <b>${quantidadeRegistrosAulaConteudos}</b> conteúdo(s) alocado(s) em \
                    <b>${quantidadeRegistrosAula}</b> registro(s) de aula. Ele(s) deve(m) ser deletado(s) primeiro. \
                </span><br> \
            `;
        }

        function verRegistrosAula () {
            for (let index = 0; index < registrosAula.length; index++) {
                const registroAula = registrosAula[index];

                const url = "http://" + window.location.host + "/intranet/educar_professores_conteudo_ministrado_cad.php?id=" + registroAula[0];
                urlHelper(url, '_blank');
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

        $j('body').append(
            '<div id="dialog-warning-excluir-plano-aula' + '" style="max-height: 80vh; width: 820px; overflow: auto;">' +
            '<div id="msg" class="msg"></div>' +
            '</div>'
        );

        $j('#dialog-warning-excluir-plano-aula').find(':input').css('display', 'block');

        $j("#dialog-warning-excluir-plano-aula").dialog({
            autoOpen: false,
            closeOnEscape: false,
            draggable: false,
            width: 820,
            modal: true,
            resizable: false,
            title: 'Dependências detectadas',
            open: function(event, ui) {
                $j(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            },
            buttons: {
                // "Continuar": function () {
                //     excluirPlanoAula();
                // },
                "Fechar": function () {
                    closeModal();
                },
                "Ver registro(s) afetado(s)": function () {
                    verRegistrosAula();
                }
            }
        });
    });
})(jQuery);