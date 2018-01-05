var submitButton = $j('#btn_enviar');
var serieId      =  $j('#serie_id').val();
submitButton.removeAttr('onclick');

submitButton.click(function(){
    var componentesInput = $j('[name*=carga_horaria]');
    var arrayComponentes = [];
    componentesInput.each(function(i) {
        nome      = this.name;
        key       = nome.split('componentes[').pop().split('][').shift();
        check     = $j('[name="componentes['+key+'][id]"]').is(':checked');
        id        = $j('[name="componentes['+key+'][id]"]').val();
        carga     = $j('[name="componentes['+key+'][carga_horaria]"]').val();
        tipo_nota = $j('[name="componentes['+key+'][tipo_nota]"]').val();

        if(check){
            arrayComponentes.push({id : id, carga_horaria : carga, tipo_nota : tipo_nota});
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
                if (confirm('Você adicionou ' + response.insert.length + ' novo(s) componente(s) na série ' + nmSerie + '. Deseja aplicar para todas as escolas?')) {
                    adicionaComponentesNasEscolas(response.insert);
                }
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