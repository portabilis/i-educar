var aulas_checkboxs = [];

(function($){
  $(document).ready(function(){
    var aulas_checkbox = document.getElementById('aulas_checkbox[]');

    aulas_checkboxs = document.getElementsByName('aulas_checkbox[]');

    if (aulas_checkbox) {
      aulas_checkbox.onchange = function () {
        aulas_checkboxs.forEach(_aulas_checkboxs => {
          _aulas_checkboxs.checked = aulas_checkbox.checked;
        });
      };
    }

    var botoesExtras = document.getElementsByClassName('botoes-selecao-usuarios-servidores');
    for (let botaoExtra of botoesExtras) {
      if (botaoExtra.value === " Validar aula(s) selecionada(s)")
        botaoExtra.setAttribute('onclick', 'validacaoPlanoAulaSelecionados()');
      else if (botaoExtra.value === " Remover validação aula(s) selecionada(s)")
        botaoExtra.setAttribute('onclick', 'removerValidacaoPlanoAulaSelecionados()');
    }

  });
})(jQuery);

function validacaoPlanoAulaSelecionados () {
  for (let aulas_checkbox of aulas_checkboxs) {
    if (aulas_checkbox.checked) {
      const planejamento_aula_id = getId(aulas_checkbox);
      validaPlanoAula(null, planejamento_aula_id);
    }
  }
}

function removerValidacaoPlanoAulaSelecionados () {
  for (let aulas_checkbox of aulas_checkboxs) {
    if (aulas_checkbox.checked) {
      const planejamento_aula_id = getId(aulas_checkbox);
      removerValidacaoPlanoAula(null, planejamento_aula_id);
    }
  }
}

function removerValidacaoPlanoAula (e, planejamento_aula_id) {
  e?.preventDefault();

  if (planejamento_aula_id === null) {
    alert("Plano de aula inválido")
    return;
  }

  var urlForValidaPlanoRegistroAula = postResourceUrlBuilder.buildUrl('/module/Api/ValidaPlanoRegistroAula', 'remover-validacao-planejamento-aula', {});

  var options = {
    type     : 'POST',
    url      : urlForValidaPlanoRegistroAula,
    dataType : 'json',
    data     : {
      planejamento_aula_id  : planejamento_aula_id
    },
    success  : handleRemocaoValidacaoPlanoAula
  };

  postResource(options);
}

function validaPlanoAula (e, planejamento_aula_id) {
  e?.preventDefault();

  if (planejamento_aula_id === null) {
    alert("Plano de aula inválido")
    return;
  }

  var urlForValidaPlanoRegistroAula = postResourceUrlBuilder.buildUrl('/module/Api/ValidaPlanoRegistroAula', 'validar-planejamento-aula', {});

  var options = {
    type     : 'POST',
    url      : urlForValidaPlanoRegistroAula,
    dataType : 'json',
    data     : {
      planejamento_aula_id  : planejamento_aula_id
    },
    success  : handleValidaPlanoAula
  };

  postResource(options);
}

function handleValidaPlanoAula (response) {
  if (!isNaN(response.result) && !isNaN(response.result) && response.result) {
    messageUtils.success('Validação efetuada com sucesso!');
    delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_validacao_planejamento_de_aula_lst.php", '_self'));
  } else {
    messageUtils.error('Houve um erro ao validar o(s) planejamento(s) de aula(s)');
  }
}

function handleRemocaoValidacaoPlanoAula (response) {
  if (!isNaN(response.result) && !isNaN(response.result) && response.result) {
    messageUtils.success('Validação Removida com sucesso!');
    delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_validacao_planejamento_de_aula_lst.php", '_self'));
  } else {
    messageUtils.error('Houve um erro ao remover validação o(s) planejamento(s) de aula(s)');
  }
}

function getId (checkbox) {
  let id = checkbox.id;
  id = id.substring(id.indexOf('[') + 1, id.indexOf(']'));

  return id;
}

function delay (time) {
  return new Promise(resolve => setTimeout(resolve, time));
}

function urlHelper (href, mode) {
  Object.assign(document.createElement('a'), {
    target: mode,
    href: href,
  }).click();
}
