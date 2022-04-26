(function($){
    $(document).ready(function(){
        var id = $j('#id').val();
        var copy = $j('#copy').val();

        if (isNaN(id) || id === '')
            return;

        if (!isNaN(id) && copy)
            return;

        var registrosAula = [];

        var planejamento_aula_id = $j('#id').val();

        var submitButton = $j('#btn_excluir');
        submitButton.removeAttr('onclick');

        submitButton.click(function () {
            tentaExcluirPlanoAula();
        });

        function tentaExcluirPlanoAula () {
            var urlForVerificarPlanoAulaSendoUsado = postResourceUrlBuilder.buildUrl('/module/Api/PlanejamentoAula', 'verificar-plano-aula-sendo-usado', {});

            var options = {
                type     : 'POST',
                url      : urlForVerificarPlanoAulaSendoUsado,
                dataType : 'json',
                data     : {
                    planejamento_aula_id    : planejamento_aula_id,
                },
                success  : handleTentaExcluirPlanoAula
            };

            postResource(options);
        }

        function handleTentaExcluirPlanoAula (response) {
            registrosAula = response.conteudos_ids;

            if (registrosAula.length == 0) {
                excluirPlanoAula();
            } else {
                openModal();
            }
        }

        function excluirPlanoAula () {
            var urlForExcluirPlanoAula = postResourceUrlBuilder.buildUrl('/module/Api/PlanejamentoAula', 'excluir-plano-aula', {});

            var options = {
                type     : 'POST',
                url      : urlForExcluirPlanoAula,
                dataType : 'json',
                data     : {
                    planejamento_aula_id    : planejamento_aula_id,
                },
                success  : handleExcluirPlanoAula
            };

            postResource(options);
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
                    Ao concluir esta ação: \
                </span><br> \
                <ul> \
                    <li> \
                        Este plano de aula será <b>deletado</b>. \
                    </li> \
                </ul> \
                <span> \
                    No entanto, haverá efeitos colaterais em \
                    <b>${quantidadeRegistrosAulaConteudos}</b> conteúdo(s) alocado(s) em \
                    <b>${quantidadeRegistrosAula}</b> registro(s) de aula. Eles também serão deletados. O que deseja fazer? \
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
                "Continuar": function () {
                    excluirPlanoAula();
                },
                "Cancelar": function () {
                    closeModal();
                },
                "Ver registro(s) afetado(s)": function () {
                    verRegistrosAula();
                }
            }
        });
    });
})(jQuery);
