
  function getNivelEnsino()
  {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');

    campoNivelEnsino.length = 1;
    for (var j = 0; j < nivel_ensino.length; j++) {
    if (nivel_ensino[j][2] == campoInstituicao) {
    campoNivelEnsino.options[campoNivelEnsino.options.length] = new Option(
    nivel_ensino[j][1], nivel_ensino[j][0], false, false
    );
  }
  }
  }

  function getTipoEnsino()
  {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');

    campoTipoEnsino.length = 1;
    for (var j = 0; j < tipo_ensino.length; j++) {
    if (tipo_ensino[j][2] == campoInstituicao) {
    campoTipoEnsino.options[campoTipoEnsino.options.length] = new Option(
    tipo_ensino[j][1], tipo_ensino[j][0], false, false
    );
  }
  }
  }

  document.getElementById('ref_cod_instituicao').onchange = function()
  {
    getNivelEnsino();
    getTipoEnsino();
  }

