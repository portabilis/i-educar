
  function getNivelEnsino(xml_nivel_ensino)
  {
    var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');
    var DOM_array = xml_nivel_ensino.getElementsByTagName('nivel_ensino');

    if (DOM_array.length) {
    campoNivelEnsino.length = 1;
    campoNivelEnsino.options[0].text = 'Selecione um nível de ensino';
    campoNivelEnsino.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
    campoNivelEnsino.options[campoNivelEnsino.options.length] = new Option(
    DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_nivel_ensino"),
    false, false
    );
  }
  }
    else {
    campoNivelEnsino.options[0].text = 'A instituição não possui nenhum nível de ensino';
  }
  }

  function getTipoEnsino(xml_tipo_ensino)
  {
    var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');
    var DOM_array = xml_tipo_ensino.getElementsByTagName('tipo_ensino');

    if (DOM_array.length) {
    campoTipoEnsino.length = 1;
    campoTipoEnsino.options[0].text = 'Selecione um tipo de ensino';
    campoTipoEnsino.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
    campoTipoEnsino.options[campoTipoEnsino.options.length] = new Option(
    DOM_array[i].firstChild.data, DOM_array[i].getAttribute('cod_tipo_ensino'),
    false, false
    );
  }
  }
    else {
    campoTipoEnsino.options[0].text = 'A instituição não possui nenhum tipo de ensino';
  }
  }

  function getTipoRegime(xml_tipo_regime)
  {
    var campoTipoRegime = document.getElementById('ref_cod_tipo_regime');
    var DOM_array = xml_tipo_regime.getElementsByTagName( "tipo_regime" );

    if(DOM_array.length)
  {
    campoTipoRegime.length = 1;
    campoTipoRegime.options[0].text = 'Selecione um tipo de regime';
    campoTipoRegime.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
    campoTipoRegime.options[campoTipoRegime.options.length] = new Option(
    DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_tipo_regime"),
    false, false
    );
  }
  }
    else {
    campoTipoRegime.options[0].text = 'A instituição não possui nenhum tipo de regime';
  }
  }

  function getHabilitacao(xml_habilitacao)
  {
    var campoHabilitacao = document.getElementById('habilitacao');
    var DOM_array = xml_habilitacao.getElementsByTagName( "habilitacao" );

    if (DOM_array.length) {
    campoHabilitacao.length = 1;
    campoHabilitacao.options[0].text = 'Selecione uma habilitação';
    campoHabilitacao.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
    campoHabilitacao.options[campoHabilitacao.options.length] = new Option(
    DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_habilitacao"),
    false, false
    );
  }
  }
    else {
    campoHabilitacao.options[0].text = 'A instituição não possui nenhuma habilitação';
  }
  }

  document.getElementById('ref_cod_instituicao').onchange = function()
  {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

    var campoNivelEnsino = document.getElementById('ref_cod_nivel_ensino');
    campoNivelEnsino.length = 1;
    campoNivelEnsino.disabled = true;
    campoNivelEnsino.options[0].text = 'Carregando nível de ensino';

    var campoTipoEnsino = document.getElementById('ref_cod_tipo_ensino');
    campoTipoEnsino.length = 1;
    campoTipoEnsino.disabled = true;
    campoTipoEnsino.options[0].text = 'Carregando tipo de ensino';

    var campoTipoRegime = document.getElementById('ref_cod_tipo_regime');
    campoTipoRegime.length = 1;
    campoTipoRegime.disabled = true;
    campoTipoRegime.options[0].text = 'Carregando tipo de regime';

    var campoHabilitacao = document.getElementById('habilitacao');
    campoHabilitacao.length = 1;
    campoHabilitacao.disabled = true;
    campoHabilitacao.options[0].text = 'Carregando habilitação';

    var xml_nivel_ensino = new ajax(getNivelEnsino);
    xml_nivel_ensino.envia("educar_nivel_ensino_xml.php?ins="+campoInstituicao);

    var xml_tipo_ensino = new ajax(getTipoEnsino);
    xml_tipo_ensino.envia("educar_tipo_ensino_xml.php?ins="+campoInstituicao);

    var xml_tipo_regime = new ajax(getTipoRegime);
    xml_tipo_regime.envia("educar_tipo_regime_xml.php?ins="+campoInstituicao);

    var xml_habilitacao = new ajax(getHabilitacao);
    xml_habilitacao.envia("educar_habilitacao_xml.php?ins="+campoInstituicao);

    if (this.value == '') {
    $('img_nivel_ensino').style.display = 'none;';
    $('img_tipo_regime').style.display = 'none;';
    $('img_tipo_ensino').style.display = 'none;';
  }
    else {
    $('img_nivel_ensino').style.display = '';
    $('img_tipo_regime').style.display = '';
    $('img_tipo_ensino').style.display = '';
  }
  }

  function fixupEtapacursoSize(){
  $j('.search-field input').css('height', '30px')
}

  $etapacurso = $j('#etapacurso');

  $etapacurso.trigger('chosen:updated');
  var testezin;

  var handleGetEtapacurso = function(dataResponse) {
  testezin = dataResponse['etapacurso'];

  $j.each(dataResponse['etapacurso'], function(id, value) {

  $etapacurso.children("[value=" + value + "]").attr('selected', '');
});

  $etapacurso.trigger('chosen:updated');
}

  var getEtapacurso = function() {

  if ($j('#cod_curso').val()!='') {

  var additionalVars = {
  curso_id : $j('#cod_curso').val(),
};

  var options = {
  url : getResourceUrlBuilder.buildUrl('/module/Api/etapacurso', 'etapacurso', additionalVars),
  dataType : 'json',
  data : {},
  success : handleGetEtapacurso,
};

  getResource(options);
}
}

  getEtapacurso();

  $j(document).ready( function(){

  fixupEtapacursoSize();
});


