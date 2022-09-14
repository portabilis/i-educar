function getRegra()
  {
    const campoInstituicao = document.getElementById('ref_cod_instituicao').value;

    const campoRegras = document.getElementById('regra_avaliacao_id');
    setAttributes(campoRegras,'Carregando regras',true);

    const campoRegrasDiferenciadas = document.getElementById('regra_avaliacao_diferenciada_id');
    setAttributes(campoRegrasDiferenciadas,'Carregando regras',true)

    getApiResource('/api/resource/evaluation-rule',RegrasInstituicao,{institution:campoInstituicao})
  }

  function EtapasCurso(cursos)
  {
    const campoEtapas = document.getElementById('etapa_curso');

    if (cursos.length) {
      setAttributes(campoEtapas,'Selecione uma etapa',false);

      for (var i = 1; i<=cursos[0].steps;i++) {
          campoEtapas.options[i] = new Option("Etapa "+i , i, false, false);
      }
    } else {
      campoEtapas.options[0].text = 'O curso não possui nenhuma etapa';
    }
  }

  var validaAnosLetivos = function(){
  let elementoAlterado = $(this);

  $j.each($j('input[name^="anos_letivos["]'), function(){
  if (this.id != elementoAlterado.id && this.value == elementoAlterado.value) {
  elementoAlterado.value = '';
  alert('Não é permitido informar o mesmo ano mais em mais de uma linha');
  elementoAlterado.focus();
}
});
}
  $j('body').on('change', 'input[name^="anos_letivos["]', validaAnosLetivos);

  function RegrasInstituicao(regras)
  {
    const campoRegras = document.getElementById('regra_avaliacao_id');
    const campoRegrasDiferenciadas = document.getElementById('regra_avaliacao_diferenciada_id');

    if (regras.length) {
      setAttributes(campoRegras,'Selecione uma regra',false)
      setAttributes(campoRegrasDiferenciadas,'Selecione uma regra',false)

      $j.each(regras, function(i, item) {
        campoRegras.options[campoRegras.options.length] = new Option(item.name,item.id,false,false);
        campoRegrasDiferenciadas.options[campoRegrasDiferenciadas.options.length] = new Option(item.name,item.id,false,false);
      });
  }
    else {
    campoRegras.options[0].text = 'A instituição não possui uma Regra de Avaliação';
    campoRegrasDiferenciadas.options[0].text = 'A instituição não possui uma Regra de Avaliação';
  }
  }

  function excluirSerieComTurmas()
  {
    document.formcadastro.reset();
    alert(stringUtils.toUtf8('Não foi possível excluir a série, pois a mesma possui turmas vinculadas.'));
  }

  document.getElementById('ref_cod_curso').onchange = function()
  {
    const campoCurso = document.getElementById('ref_cod_curso').value;
    const campoEtapas = document.getElementById('etapa_curso');

    setAttributes(campoEtapas,'Carregando etapas',true);

    getApiResource('/api/resource/course',EtapasCurso,{course:campoCurso})
  }

  /**
  * Dispara eventos durante onchange da select ref_cod_instituicao.
  */
  document.getElementById('ref_cod_instituicao').onchange = function()
  {
    // Essa ação é a padrão do item, via include
    getCurso();
  }

