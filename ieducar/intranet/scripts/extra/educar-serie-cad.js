

  function getRegra()
  {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

    var campoRegras = document.getElementById('regra_avaliacao_id');
    campoRegras.length = 1;
    campoRegras.disabled = true;
    campoRegras.options[0].text = 'Carregando regras';

    var campoRegrasDiferenciadas = document.getElementById('regra_avaliacao_diferenciada_id');
    campoRegrasDiferenciadas.length = 1;
    campoRegrasDiferenciadas.disabled = true;
    campoRegrasDiferenciadas.options[0].text = 'Carregando regras';

    var xml_qtd_etapas = new ajax(RegrasInstituicao);
    xml_qtd_etapas.envia("educar_serie_regra_xml.php?ins=" + campoInstituicao);
  }

  function EtapasCurso(xml_qtd_etapas)
  {
    var campoEtapas = document.getElementById('etapa_curso');
    var DOM_array = xml_qtd_etapas.getElementsByTagName('curso');

    if (DOM_array.length) {
    campoEtapas.length = 1;
    campoEtapas.options[0].text = 'Selecione uma etapa';
    campoEtapas.disabled = false;

    var etapas;
    etapas = DOM_array[0].getAttribute("qtd_etapas");

    for (var i = 1; i<=etapas;i++) {
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

  function RegrasInstituicao(xml_qtd_regras)
  {
    var campoRegras = document.getElementById('regra_avaliacao_id');
    var campoRegrasDiferenciadas = document.getElementById('regra_avaliacao_diferenciada_id');
    var DOM_array = xml_qtd_regras.getElementsByTagName('regra');

    if (DOM_array.length) {
    campoRegras.length = 1;
    campoRegras.options[0].text = 'Selecione uma regra';
    campoRegras.disabled = false;

    campoRegrasDiferenciadas.length = 1;
    campoRegrasDiferenciadas.options[0].text = 'Selecione uma regra';
    campoRegrasDiferenciadas.disabled = false;

    var loop = DOM_array.length;

    for (var i = 0; i < loop;i++) {
    campoRegras.options[i] = new Option(DOM_array[i].firstChild.data, DOM_array[i].id, false, false);
    campoRegrasDiferenciadas.options[i] = new Option(DOM_array[i].firstChild.data, DOM_array[i].id, false, false);
  }
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
    var campoCurso = document.getElementById('ref_cod_curso').value;
    var campoEtapas = document.getElementById('etapa_curso');

    campoEtapas.length = 1;
    campoEtapas.disabled = true;
    campoEtapas.options[0].text = 'Carregando etapas';

    var xml_qtd_etapas = new ajax(EtapasCurso);
    xml_qtd_etapas.envia("educar_curso_xml2.php?cur=" + campoCurso);
  }

  /**
  * Dispara eventos durante onchange da select ref_cod_instituicao.
  */
  document.getElementById('ref_cod_instituicao').onchange = function()
  {
    // Essa ação é a padrão do item, via include
    getCurso();
  }

