(function($){
    $(document).ready(function(){
        var id = $j('#id').val();
        var copy = $j('#copy').val();

        if (isNaN(id) || id === '')
            return;

        if (!isNaN(id) && copy)
            return;

        var registrosAula = [];

        var planejamento_aula_aee_id = $j('#id').val();

        var submitButton = $j('#btn_excluir');
        submitButton.removeAttr('onclick');

        submitButton.click(function () {
          if(confirm('Este procedimento irá excluir o plano de aula AEE. Tem certeza que deseja continuar?')) {
            tentaExcluirPlanoAulaAee();
          }
        });

        function tentaExcluirPlanoAulaAee () {
            var urlForVerificarPlanoAulaAeeSendoUsado = postResourceUrlBuilder.buildUrl('/module/Api/PlanejamentoAulaAee', 'verificar-plano-aula-aee-sendo-usado', {});

            var options = {
                type     : 'POST',
                url      : urlForVerificarPlanoAulaAeeSendoUsado,
                dataType : 'json',
                data     : {
                    planejamento_aula_aee_id    : planejamento_aula_aee_id,
                },
                success  : handleTentaExcluirPlanoAulaAee
            };

            postResource(options);
        }

        function handleTentaExcluirPlanoAulaAee (response) {
            registrosAula = response.conteudos_ids;

            if (registrosAula.length == 0) {
                excluirPlanoAulaAee();
            } else {
                openModal();
            }
        }

        function excluirPlanoAulaAee () {
            var urlForExcluirPlanoAulaAee = postResourceUrlBuilder.buildUrl('/module/Api/PlanejamentoAulaAee', 'excluir-plano-aula-aee', {});

            var options = {
                type     : 'POST',
                url      : urlForExcluirPlanoAulaAee,
                dataType : 'json',
                data     : {
                    planejamento_aula_aee_id    : planejamento_aula_aee_id,
                },
                success  : handleExcluirPlanoAulaAee
            };

            postResource(options);
        }

        function handleExcluirPlanoAulaAee (response) {
            if(response.result) {
                messageUtils.success('Plano de aula AEE excluído com sucesso!');

                delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_planejamento_de_aula_aee_lst.php", '_self'));
            } else {
                messageUtils.success('Erro desconhecido ocorreu.');
            }
        }

        function openModal() {
            var quantidadeRegistrosAula = registrosAula.length;

            $j("#dialog-warning-excluir-plano-aula").find('#msg').html(getMessageExcluirPlanoAulaAee(quantidadeRegistrosAula));
            $j("#dialog-warning-excluir-plano-aula").dialog("open");
        }

        function closeModal() {
            registrosAula = [];

            $j("#dialog-warning-excluir-plano-aula").dialog('close');
        }

        function getMessageExcluirPlanoAulaAee(quantidadeRegistrosAula) {
          return ` \
                <span> \
                    Não é possível prosseguir com a exclusão porque <b> um ou mais conteúdos </b> estão sendo utilizados em \
                    <b>${quantidadeRegistrosAula}</b> registro(s) de aula. O que deseja fazer? \
                </span><br> \
            `;
        }

        function verRegistrosAula () {
            for (let index = 0; index < registrosAula.length; index++) {
                const registroAula = registrosAula[index];

                const url = "http://" + window.location.host + "/intranet/educar_professores_frequencia_cad.php?id=" + registroAula;
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
