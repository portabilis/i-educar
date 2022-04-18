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
        var ddp;
        var atividades;
        var bncc;
        var conteudos;
        var referencias;

        var submitButton = $j('#btn_enviar');
        submitButton.removeAttr('onclick');

        submitButton.click(function () {
            tentaEditarPlanoAula();
        });

        function tentaEditarPlanoAula () {
            ddp = $j('#ddp').val();
            atividades = $j('#atividades').val();
            bncc = $j('#bncc').val();
            conteudos = pegarConteudos();

            referencias = $j('#referencias').val();

            var urlForVerificarPlanoAulaSendoUsado = postResourceUrlBuilder.buildUrl('/module/Api/PlanejamentoAula', 'verificar-plano-aula-sendo-usado2', {});

            var options = {
                type     : 'POST',
                url      : urlForVerificarPlanoAulaSendoUsado,
                dataType : 'json',
                data     : {
                    planejamento_aula_id    : planejamento_aula_id,
                    conteudos               : conteudos,
                },
                success  : handleTentaEditarPlanoAula
            };

            postResource(options);
        }

        function handleTentaEditarPlanoAula (response) {
            registrosAula = response.conteudos_ids;

            if (registrosAula.length == 0) {
                editarPlanoAula();
            } else {
                openModal();
            }
        }

        function editarPlanoAula () {
            var urlForEditarPlanoAula = postResourceUrlBuilder.buildUrl('/module/Api/PlanejamentoAula', 'editar-plano-aula', {});

            var options = {
                type     : 'POST',
                url      : urlForEditarPlanoAula,
                dataType : 'json',
                data     : {
                    planejamento_aula_id    : planejamento_aula_id,
                    ddp                     : ddp,
                    atividades              : atividades,
                    bncc                    : bncc,
                    conteudos_novos         : conteudos,
                    referencias             : referencias,
                },
                success  : handleEditarPlanoAula
            };

            postResource(options);
        }

        function handleEditarPlanoAula (response) {
            if(response.result == "Edição efetuada com sucesso.") {
                messageUtils.success('Plano de aula editado com sucesso!');

                delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_planejamento_de_aula_lst.php", '_self'));
            } else {
                messageUtils.success('Erro desconhecido ocorreu.');
            }
        }

        function openModal() {
            var quantidadeRegistrosAula = registrosAula.length;

            $j("#dialog-warning-editar-plano-aula").find('#msg').html(getMessageEditarPlanoAula(quantidadeRegistrosAula));
            $j("#dialog-warning-editar-plano-aula").dialog("open");
        }

        function closeModal() {
            registrosAula = [];

            $j("#dialog-warning-editar-plano-aula").dialog('close');
        }

        function getMessageEditarPlanoAula(quantidadeRegistrosAula) {
            return ` \
                <span> \
                    Ao concluir esta ação: \
                </span><br> \
                <ul> \
                    <li> \
                        Este plano de aula será <b>editado</b>. \
                    </li> \
                    <li> \
                        Um ou mais conteúdos no plano de aula será(ão) <b>deletado(s)</b>. \
                    </li> \
                </ul> \
                <span> \
                    No entanto, haverá efeitos colaterais em \
                    <b>${quantidadeRegistrosAula}</b> registro(s) de aula. O(s) conteúdo(s) também será(ão) deletado(s) lá. O que deseja fazer? \
                </span><br> \
            `;
        }

        function verRegistrosAula () {
            for (let index = 0; index < registrosAula.length; index++) {
                const registroAula = registrosAula[index];

                const url = "http://" + window.location.host + "/intranet/educar_professores_conteudo_ministrado_cad.php?id=" + registroAula;
                urlHelper(url, '_blank');
            }
        }

        function urlHelper (href, mode) {
            Object.assign(document.createElement('a'), {
            target: mode,
            href: href,
            }).click();
        }

        function delay (time) {
            return new Promise(resolve => setTimeout(resolve, time));
        }

        function pegarConteudos () {
            var conteudos = []

            tr_conteudos = document.getElementsByName("tr_conteudos[]");
            tr_conteudos.forEach(tr_conteudo => {
                var id = tr_conteudo.children[0].children[0].id;
                var conteudoElemento = document.getElementById(id);
                var conteudoId = pegarId(conteudoElemento.name);
                var conteudoValor = conteudoElemento.value;

                var conteudo = [];
                conteudo.push(conteudoId);
                conteudo.push(conteudoValor);
                conteudos.push(conteudo);
            });

            return conteudos;
        }

        function pegarId (name) {
            let id = name;
            id = id.substring(id.indexOf('[') + 1, id.indexOf(']'));

            return id;
        }

        $j('body').append(
            '<div id="dialog-warning-editar-plano-aula' + '" style="max-height: 80vh; width: 820px; overflow: auto;">' +
            '<div id="msg" class="msg"></div>' +
            '</div>'
        );

        $j('#dialog-warning-editar-plano-aula').find(':input').css('display', 'block');

        $j("#dialog-warning-editar-plano-aula").dialog({
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
                    editarPlanoAula();
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
