var submitButton = $j('#btn_enviar');
var serieId      =  $j('#serie_id').val();
submitButton.removeAttr('onclick');

submitButton.click(function(){
  var componentesInput = $j('[name*=carga_horaria]');
  var arrayComponentes = [];
  componentesInput.each(function(i) {
    let nome         = this.name;
    let key          = nome.split('componentes[').pop().split('][').shift();
    let check        = $j('[name="componentes['+key+'][id]"]').is(':checked');
    let id           = $j('[name="componentes['+key+'][id]"]').val();
    let carga        = $j('[name="componentes['+key+'][carga_horaria]"]').val();
    let hora_falta   = $j('[name="componentes['+key+'][hora_falta]"]').val();
    let tipo_nota    = $j('[name="componentes['+key+'][tipo_nota]"]').val();
    let anos_letivos = $j('[name="componentes['+key+'][anos_letivos]"]').val() || [];

    if (hora_falta != null) {
        hora_falta = hora_falta / 60;
    } else {
        hora_falta = null;
    }

    if(check){
      arrayComponentes.push({id : id, carga_horaria : carga, tipo_nota : tipo_nota, anos_letivos: anos_letivos, hora_falta: hora_falta});
    }
  });
  atualizaComponentesSerie(arrayComponentes);
});

function atualizaComponentesSerie(componentes){
        var seriesIds = [];
        if (serieId && serieId !== '') {
            seriesIds = [serieId];
        } else {
            var valorSerie = $j('#ref_cod_serie').val();
            if (Array.isArray(valorSerie)) {
                seriesIds = valorSerie.filter(function(id) {
                    return id && id !== 'all';
                });
            } else if (valorSerie && valorSerie !== '' && valorSerie !== 'all') {
                seriesIds = [valorSerie];
            }
        }

        var urlForAtualizaComponentesSerie = postResourceUrlBuilder.buildUrl('/module/Api/ComponentesSerie', 'atualiza-componentes-serie', {});

        var options = {
          type     : 'POST',
          url      : urlForAtualizaComponentesSerie,
          dataType : 'json',
          data     : {
            series      : JSON.stringify(seriesIds),
            serie_id    : seriesIds.length > 0 ? seriesIds[0] : '',
            componentes : JSON.stringify(componentes)
          },
          success  : handleAtualizaComponentesSerie,
          error    : handleErroAtualizaComponentesSerie
        };

        postResource(options);
}

var filaSeriesModal = [];

function executaModalProximaSerie() {
    if (filaSeriesModal.length === 0) {
        if (ModalSelectEscolas.dialogContainer && ModalSelectEscolas.dialogContainer.dialog('isOpen')) {
            ModalSelectEscolas.dialogContainer.dialog('close');
        }
        redirecionaListagem();
        return;
    }

    var proxima = filaSeriesModal.shift();
    ModalSelectEscolas.init(proxima.serieId, proxima.serieNome, proxima.componentes, 'novos-componentes', proxima.actions);
}

function handleAtualizaComponentesSerie(response) {

        if (response.msgErro) {
            let msgs = response.msgErro.split("\n");

            msgs.forEach(msg => messageUtils.error(msg));
            return;
        }

        messageUtils.success('Componentes da série alterados com sucesso!');

        filaSeriesModal = [];
        let seriesRetorno = response.series;
        if (!seriesRetorno && (response.insert || response.update)) {
            seriesRetorno = [{
                id: serieId || $j('#ref_cod_serie').val(),
                insert: response.insert,
                update: response.update
            }];
        }

        if (seriesRetorno && seriesRetorno.length > 0) {
            seriesRetorno.forEach(function(item) {
                let arrayComponentes = [];
                let actions = [];

                if (item.insert && item.insert.length > 0) {
                    arrayComponentes = arrayComponentes.concat(item.insert);
                    actions.push('novos-componentes');
                }

                if (item.update && item.update.length > 0) {
                    $j.each(item.update, function (key, componente) {
                        if (componente.anos_letivos_inseridos && componente.anos_letivos_inseridos.length > 0) {
                            componente.anos_letivos = componente.anos_letivos_inseridos;
                            arrayComponentes = arrayComponentes.concat(componente);
                            actions.push('novos-anos');
                        }
                    });
                }

                if (arrayComponentes.length > 0) {
                    let option = $j('#ref_cod_serie option[value="' + item.id + '"]');
                    let nmSerie = option.length ? option.text().trim() : '';
                    if (!nmSerie) {
                        nmSerie = $j('#ref_cod_serie option:selected').text().trim();
                    }

                    filaSeriesModal.push({
                        serieId: item.id,
                        serieNome: nmSerie,
                        componentes: arrayComponentes,
                        actions: actions
                    });
                }
            });
        }

        if (filaSeriesModal.length > 0) {
            executaModalProximaSerie();
        } else {
            redirecionaListagem();
        }
    }

function handleErroAtualizaComponentesSerie(response){
    handleMessages([{type : 'error', msg : 'Erro ao alterar componentes da série: ' + response.statusText}], '');
    safeLog(response);
}

function adicionaComponentesTodasEscolas(dialogId, targetSerieId){
    var url = postResourceUrlBuilder.buildUrl('/module/Api/ComponentesSerie', 'replica-componentes-adicionados-escolas', {});

    var currentSerieId = targetSerieId || ModalSelectEscolas.serieId || serieId;

    var options = {
      type     : 'POST',
      url      : url,
      dataType : 'json',
      data     : {
        serie_id    : currentSerieId,
        componentes : $j('#json-componentes').val()
      },
      success  : function(response) {
          if(response.any_error_msg){
              messageUtils.error('Erro ao aplicar alterações para todas as escolas.');
          }
          messageUtils.success('Alterações aplicadas para todas as escolas.');
          executaModalProximaSerie();
      },
      error    : handleErroReplicaComponentesEscola
    };

    postResource(options);
}

function handleErroReplicaComponentesEscola(response){
    handleMessages([{type : 'error', msg : 'Erro ao aplicar alterações para todas as escolas: ' + response.statusText}], '');
    safeLog(response);
}

// Limpa mensagens de erro
var postResource = function(options, errorCallback){
    $j.ajax(options).error();
};

var deleteButton = $j('#btn_excluir');
deleteButton.removeAttr('onclick');

deleteButton.click(function(){
    if (confirm('Deseja excluir os componentes da série? Isso também excluirá de todas as escolas e turmas do ano atual.')) {
        excluiComponentesDaSerie();
    }else{
        redirecionaListagem();
    }
});

function excluiComponentesDaSerie(){
    var url = postResourceUrlBuilder.buildUrl('/module/Api/ComponentesSerie', 'exclui-componentes-serie', {});

    var options = {
      type     : 'POST',
      url      : url,
      dataType : 'json',
      data     : {
        serie_id    : serieId
      },
      success  : handleExcluiComponentesDaSerie,
      error    : handleErroExcluiComponentesDaSerie
    };

    postResource(options);
}

function handleExcluiComponentesDaSerie(response){
    if(response.any_error_msg){
        return messageUtils.error('Erro ao excluir componentes da série.');
    }
    messageUtils.success('Componentes excluídos com sucesso.');
    redirecionaListagem();
}

function handleErroExcluiComponentesDaSerie(response){
    handleMessages([{type : 'error', msg : 'Erro ao aplicar alterações para todas as escolas: ' + response.statusText}], '');
    safeLog(response);
}


function redirecionaListagem(){
    window.location.href = "/intranet/educar_componentes_serie_lst.php";
}


ModalSelectEscolas = {
    links: $j('.mostra-consulta'),
    dialogContainer: undefined,
    dialog: undefined,
    serieNome: null,
    componentes: [],
    idContainer: null,
    createDialog: function () {
        this.dialogContainer = $j('#dialog-container-' + this.idContainer);

        if (this.dialogContainer.length < 1) {
            $j('body').append(
                '<div id="dialog-container-' + this.idContainer + '" style="max-height: 80vh; width: 820px; overflow: auto;">' +
                '<div class="msg"></div>' +
                '<div class="table"></div>' +
                '</div>'
            );

            this.dialogContainer = $j('#dialog-container-' + this.idContainer);

            this.dialog = this.dialogContainer.dialog({
                autoOpen: false,
                closeOnEscape: false,
                draggable: false,
                width: 820,
                modal: true,
                resizable: true,
                title: 'Seleção de escolas',
                open: function(event, ui) {
                    $j(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
                }
            });
        }
    },
    makeHtml: function (data) {
        let dialog = this.dialogContainer;
        let tableContainer = dialog.find('.table');
        let msgContainer = dialog.find('.msg');

        tableContainer
            .empty()
            .hide();

        msgContainer.html(
            this.getContainerMessage()
        );

        msgContainer.append(this.getActionButtons());

        msgContainer.append(this.getEscolasTable(data));

        this.dialog.dialog('option', 'position', {my: 'center', at: 'center top', of: window});
    },
    getContainerMessage: function () {
        let msg = 'Foram feitas as seguintes alterações:<br>';
        msg += '<ul>';
        if (this.actions.includes('novos-componentes')) {
            msg += '<li>Foram adicionados <b>' + this.componentes.length + '</b> componente(s) na série <b>' + this.serieNome + '</b></li>';
        }

        if (this.actions.includes('novos-anos')) {
            msg += '<li>Foram adicionados novos anos letivos para <b>' + this.componentes.length + '</b> componente(s) na série <b>' + this.serieNome + '</b></li>';
        }

        msg += '</ul>';

        return msg + 'Você pode aplicar as mesmas alterações dessa série para todas as escolas, não aplicar em nenhuma escola ou escolher para quais escolas deseja aplicar</p>';

    },
    showMsg: function (msg) {
        let dialog = this.dialogContainer;
        let tableContainer = dialog.find('.table');
        let msgContainer = dialog.find('.msg');

        tableContainer.hide();
        msgContainer.html(msg).show();
    },
    request: function () {
        let that = this;
        let url = '/module/Api/ComponentesSerie';
        let params = {
            'oper': 'get',
            'serie': this.serieId,
            'resource': 'get-escolas-by-serie'
        };

        if (!this.dialogContainer.dialog('isOpen')) {
            this.dialogContainer.dialog('open');
        }

        this.showMsg('Aguarde');

        $j.getJSON(url, params, function (response) {
            that.makeHtml(response.escolas);
        });
    },
    getActionButtons() {
        let buttonsTable = $j('<table>').attr({
            'class': 'tablecadastro',
            'width': '100%',
            'border': '0',
            'cellpadding': '2',
            'cellspacing': '0'
        });

        let buttonsTr = $j('<tr>').attr({
            'class': 'linhaBotoes'
        });

        let buttonsTd = $j('<td>').attr({
            'colspan': '2',
            'align': 'center'
        });

        buttonsTd.append('<input type="button" class="botaolistagem" value="Aplicar em todas" onclick="adicionaComponentesTodasEscolas(\''+this.idContainer+'\', ' + this.serieId + ')">');
        buttonsTd.append('<input type="button" class="botaolistagem" value="Aplicar somente na série" onclick="executaModalProximaSerie();">');
        buttonsTd.append('<input type="button" class="botao" value="Selecionar escolas para aplicar" onclick="expandEscolas()">');

        buttonsTr.append(buttonsTd);

        buttonsTable.append(buttonsTr);

        return buttonsTable;
    },
    getEscolasTable: function (data) {
        let div = $j('<div>').attr({
            'style': 'display:none',
            'id': 'div-escolas'
        });

        let table = $j('<table>').attr({
            'class': 'tablelistagem',
            'cellspacing': '1',
            'cellpadding': '4',
            'border': '0',
            'style': 'width: 100%;'
        });

        let trTitle = $j('<tr>');

        trTitle.append($j('<td>').attr({
                class: 'formdktd',
                align: 'center'
            }).append('<input type="checkbox" onclick="checkAllEscolas(this)" checked>')
        );

        trTitle.append($j('<td>').attr({
                class: 'formdktd',
            }).append('Escolas')
        );

        table.append(trTitle);

        $j.each(data, function (key, escola) {
            let tr = $j('<tr>');

            tr.append(
                $j('<td>').attr({
                    class: 'formmdtd',
                    align: 'center'
                }).append(
                    $j('<input>').attr({
                        type: 'checkbox',
                        id: 'escola-' + escola.cod_escola,
                        name: 'escola[]',
                        'value': escola.cod_escola,
                        checked: true
                    })
                )
            );

            tr.append($j('<td>').attr({
                class: 'formmdtd'
            }).append(
                '<label for="escola-' + escola.cod_escola + '">' + escola.nome_escola + '</label>'
            ));

            table.append(tr);
        });

        div.append(table);
        div.append('<input type="button" class="botao" value="Aplicar nas escolas selecionadas" onclick="atualizaComponentesEscolas(' + this.serieId + ', \''+this.idContainer+'\')">');

        return div;
    },
    init: function (serieId, serieNome, componentes, idContainer, actions) {
        this.serieNome = serieNome;
        this.componentes = componentes;
        this.serieId = serieId;
        this.idContainer = idContainer;
        this.actions = actions;
        $j('#json-componentes').val(JSON.stringify(componentes));

        this.createDialog();
        this.request();
    }
};

function expandEscolas() {
    $j('#div-escolas').toggle('fast');
}

function checkAllEscolas(element) {
    let isChecked = $j(element).prop("checked");
    $j('input[name=escola\\[\\]]').prop('checked', isChecked);
}

$j('body').append($j('<input>').attr({
    type: 'hidden',
    id: 'json-componentes'
}));

function atualizaComponentesEscolas (serieId, dialogId) {
    let componentes = $j('#json-componentes').val();
    let escolasInput = $j('input[name=escola\\[\\]]');
    let arrayEscolas = [];
    escolasInput.each(function(key, input) {
        if ($j(input).prop('checked') === true) {
            let id = $j(input).val();
            arrayEscolas.push({id : id});
        }
    });

    let url = '/module/Api/ComponentesSerie';
    let params = {
        'oper': 'post',
        'resource': 'atualiza-componentes-escolas',
        'serie': serieId,
        'componentes': componentes,
        'escolas': JSON.stringify(arrayEscolas)
    };

    $j.post(url, params, function (response) {
        messageUtils.success('Alterações aplicadas para todas as escolas selecionadas');
        executaModalProximaSerie();
    });
}

function closeDialog(dialogId) {
    $j('#dialog-container-' + dialogId).dialog('close');
}
