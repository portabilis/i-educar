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
        botaoExtra.setAttribute('onclick', 'validacaoRegistroAulaSelecionados()');
      else if (botaoExtra.value === " Remover validação aula(s) selecionada(s)")
        botaoExtra.setAttribute('onclick', 'removerValidacaoRegistroAulaSelecionados()');
    }

  });
})(jQuery);

function validacaoRegistroAulaSelecionados () {
  for (let aulas_checkbox of aulas_checkboxs) {
    if (aulas_checkbox.checked) {
      const frequencia_id = getId(aulas_checkbox);
      validaRegistroAula(null, frequencia_id);
    }
  }
}

function removerValidacaoRegistroAulaSelecionados () {
  for (let aulas_checkbox of aulas_checkboxs) {
    if (aulas_checkbox.checked) {
      const frequencia_id = getId(aulas_checkbox);
      removerValidacaoRegistroAula(null, frequencia_id);
    }
  }
}

function removerValidacaoRegistroAula (e, frequencia_id) {
  e?.preventDefault();

  if (frequencia_id === null) {
    alert("Registro de aula inválido")
    return;
  }

  var urlForValidaPlanoRegistroAula = postResourceUrlBuilder.buildUrl('/module/Api/ValidaPlanoRegistroAula', 'remover-validacao-registro-aula', {});

  var options = {
    type     : 'POST',
    url      : urlForValidaPlanoRegistroAula,
    dataType : 'json',
    data     : {
      frequencia_id  : frequencia_id
    },
    success  : handleRemocaoValidacaoRegistroAula
  };

  postResource(options);
}

function validaRegistroAula (e, frequencia_id) {
  e?.preventDefault();

  if (frequencia_id === null) {
    alert("Registro de aula inválido")
    return;
  }

  var urlForValidaPlanoRegistroAula = postResourceUrlBuilder.buildUrl('/module/Api/ValidaPlanoRegistroAula', 'validar-registro-aula', {});

  var options = {
    type     : 'POST',
    url      : urlForValidaPlanoRegistroAula,
    dataType : 'json',
    data     : {
      frequencia_id  : frequencia_id
    },
    success  : handleValidaRegistroAula
  };

  postResource(options);
}

function handleValidaRegistroAula (response) {
  if (!isNaN(response.result) && !isNaN(response.result) && response.result) {
    messageUtils.success('Validação efetuada com sucesso!');
    delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_validacao_registro_de_frequencia_lst.php", '_self'));
  } else {
    messageUtils.error('Houve um erro ao validar o(s) registro(s) de aula(s)');
  }
}

function handleRemocaoValidacaoRegistroAula (response) {
  if (!isNaN(response.result) && !isNaN(response.result) && response.result) {
    messageUtils.success('Validação Removida com sucesso!');
    delay(1000).then(() => urlHelper("http://" + window.location.host + "/intranet/educar_professores_validacao_registro_de_frequencia_lst.php", '_self'));
  } else {
    messageUtils.error('Houve um erro ao remover validação o(s) registro(s) de aula(s)');
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
