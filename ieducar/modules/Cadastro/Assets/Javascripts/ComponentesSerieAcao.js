var submitButton = $j('#btn_enviar');
var serieId      =  $j('#serie_id').val();
submitButton.removeAttr('onclick');

submitButton.click(function(){
  var componentesInput = $j('[name*=carga_horaria]');
  var arrayComponentes = [];
  componentesInput.each(function(i) {
    let nome      = this.name;
    let key       = nome.split('componentes[').pop().split('][').shift();
    let check     = $j('[name="componentes['+key+'][id]"]').is(':checked');
    let id        = $j('[name="componentes['+key+'][id]"]').val();
    let carga     = $j('[name="componentes['+key+'][carga_horaria]"]').val();
    let tipo_nota = $j('[name="componentes['+key+'][tipo_nota]"]').val();
    let anos_letivos = $j('[name="componentes['+key+'][anos_letivos]"]').val() || [];

    if(check){
      arrayComponentes.push({id : id, carga_horaria : carga, tipo_nota : tipo_nota, anos_letivos: anos_letivos});
    }
  });
  atualizaComponentesSerie(arrayComponentes);
});

function atualizaComponentesSerie(componentes){
        serieId = serieId != '' ? serieId : $j('#ref_cod_serie').val();
        var urlForAtualizaComponentesSerie = postResourceUrlBuilder.buildUrl('/module/Api/ComponentesSerie', 'atualiza-componentes-serie', {});

        var options = {
          type     : 'POST',
          url      : urlForAtualizaComponentesSerie,
          dataType : 'json',
          data     : {
            serie_id    : serieId,
            componentes : JSON.stringify(componentes)
          },
          success  : handleAtualizaComponentesSerie,
          error    : handleErroAtualizaComponentesSerie
        };

        postResource(options);
}

function handleAtualizaComponentesSerie(response) {

        if (response.msgErro) {
            messageUtils.error(response.msgErro);
        }else{
            var nmSerie = $j('#ref_cod_serie option:selected').map(function() {
                return this.text;
            }).get();

            if(response.insert){
                serieId = $j('#serie_id').val();
                ModalSelectEscolas.init(serieId, nmSerie, response.insert);

                //adicionaComponentesNasEscolas(response.insert);
            }else{
                redirecionaListagem()
            }
            messageUtils.success('Componentes da série alterados com sucesso!');
        }
    }

function handleCompleteAtualizaComponentesSerie(){

}

function handleErroAtualizaComponentesSerie(response){
    handleMessages([{type : 'error', msg : 'Erro ao alterar componentes da série: ' + response.statusText}], '');
    safeLog(response);
}

function adicionaComponentesNasEscolas(componentes){
    var url = postResourceUrlBuilder.buildUrl('/module/Api/ComponentesSerie', 'replica-componentes-adicionados-escolas', {});

    var options = {
      type     : 'POST',
      url      : url,
      dataType : 'json',
      data     : {
        serie_id    : serieId,
        componentes : JSON.stringify(componentes)
      },
      success  : handleReplicaComponentesEscola,
      error    : handleErroReplicaComponentesEscola
    };

    postResource(options);
}

function handleReplicaComponentesEscola(response){
    if(response.any_error_msg){
        messageUtils.error('Erro ao aplicar alterações para todas as escolas.');
    }
    messageUtils.success('Alterações aplicadas para todas as escolas.');
    redirecionaListagem();
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


var ModalSelectEscolas = {
    links: $j('.mostra-consulta'),
    dialogContainer: $j('#dialog-container'),
    dialog: undefined,
    serieNome: null,
    componentes: [],
    createDialog: function () {
        if (this.dialogContainer.length < 1) {
            $j('body').append(
                '<div id="dialog-container" style="max-height: 80vh; width: 820px; overflow: auto;">' +
                '<div class="msg"></div>' +
                '<div class="table"></div>' +
                '</div>'
            );

            this.dialogContainer = $j('#dialog-container');
        }

        this.dialog = this.dialogContainer.dialog({
            autoOpen: false,
            closeOnEscape: true,
            draggable: false,
            width: 820,
            modal: true,
            resizable: true,
            title: 'Seleção de escolas'
        });


    },
    makeHtml: function (data) {
        let dialog = this.dialogContainer;
        let tableContainer = dialog.find('.table');
        let msgContainer = dialog.find('.msg');

        tableContainer
            .empty()
            .hide();

        msgContainer.html(
            '<p>Você adicionou <b>' + this.componentes.length + '</b> componente(s) na série <b>' + this.serieNome + '</b><br>' +
            'Você pode aplicar para todas as escolas, não aplicar em nenhuma escola ou escolher para quais escolas deseja aplicar</p>'
        );

        msgContainer.append(this.getActionButtons());

        msgContainer.append(this.getEscolasTable(data));

        this.dialog.dialog('option', 'position', {my: 'center', at: 'center', of: window});
    },
    showMsg: function (msg) {
        let dialog = this.dialogContainer;
        let tableContainer = dialog.find('.table');
        let msgContainer = dialog.find('.msg');

        tableContainer.hide();
        msgContainer.html(msg).show();
    },
    request: function (serieId) {
        let that = this;
        let url = '/module/Api/ComponentesSerie';
        let params = {
            'oper': 'get',
            'serie': serieId,
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
    getActionButtons () {
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

        buttonsTd.append('<input type="button" class="botaolistagem" value="Aplicar em todas" onclick="adicionaComponentesNasEscolas(this.componentes)">');
        buttonsTd.append('<input type="button" class="botaolistagem" value="Não aplicar em nenhuma" onclick="redirecionaListagem()">');
        buttonsTd.append('<input type="button" class="botao" value="Selecionar escolas para aplicar" onclick="expandEscolas()">');

        buttonsTr.append(buttonsTd);

        buttonsTable.append(buttonsTr);

        return buttonsTable;
    },
    getEscolasTable: function(data){
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
            }).append('<input type="checkbox" onclick="checkAllEscolas(this)">')
        );

        trTitle.append($j('<td>').attr({
            class: 'formdktd',
        }));

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
                        id: 'escola-' + escola.id,
                        name: 'escola[]',
                        'value': escola.id
                    })
                )
            );

            tr.append($j('<td>').attr({
                class: 'formmdtd'
            }).append(
                '<label for="escola-' + escola.id + '">' + escola.nome_escola + '</label>'
            ));

            table.append(tr);
        });

        div.append(table);
        div.append('<input type="button" class="botao" value="Aplicar nas escolas selecionadas">');

        return div;
    },
    init: function (serieId, serieNome, componentes) {
        this.createDialog();
        this.serieNome = serieNome;
        this.componentes = componentes;
        this.request(serieId);
    }
};

function expandEscolas(){
    $j('#div-escolas').toggle('fast');
}

function checkAllEscolas(element) {
    var isChecked = $j(element).prop("checked");
    $j('input[name=escola\\[\\]]').prop('checked', isChecked);
}
