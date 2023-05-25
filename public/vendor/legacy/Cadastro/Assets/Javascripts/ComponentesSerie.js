var modoCadastro   = $j('#retorno').val() == 'Novo';
var modoEdicao     = $j('#retorno').val() == 'Editar';
var instituicao_id = $j('#ref_cod_instituicao').val();
var curso_id       = $j('#curso_id').val();
var serie_id       = $j('#serie_id').val();
var fieldArea      = $j('#ref_cod_area_conhecimento');
var comboCurso     = $j('#ref_cod_curso');
var comboSerie     = $j('#ref_cod_serie');
var chosenOldArray = [];
var guardaAreas    = [];

if(modoEdicao){
    $j('#ref_cod_instituicao').attr('disabled', 'true');
    $j('#ref_cod_curso').attr('disabled', 'true');
    $j('#ref_cod_serie').attr('disabled', 'true');
    getCursos();
    getSeries();
}

$j("#ref_cod_instituicao").change(function() {
    instituicao_id = $j('#ref_cod_instituicao').val();
    if (instituicao_id != '') {
        getCursos();
    }else{
        comboCurso.empty();
        comboCurso.append('<option value="">Selecione um curso</option>');
    }
    updateAreaConhecimento();
});

$j("#ref_cod_curso").change(function() {
    curso_id = $j('#ref_cod_curso').val();
    if (curso_id != '') {
        getSeriesSemComponentesViculados();
    }else{
        comboSerie.empty();
        comboSerie.append('<option value="">Selecione uma série</option>');
    }
});

$j("#ref_cod_area_conhecimento").change(function() {
    var chosenArray = $j("#ref_cod_area_conhecimento").chosen().val();
    if (!chosenOldArray) {
        chosenOldArray = [];
    }
    if(chosenArray && chosenOldArray){
        if (chosenArray.length > chosenOldArray.length) {
            chosenArray.forEach(function(area) {
              let nome_area = $j(this).find("option[value='" + area + "']").text();
                if (!$j('#area_conhecimento_' + area).length && area != '') {
                    $j('#componentes').append(htmlCabecalhoAreaConhecimento(area, nome_area));
                }
            }, this);
        }else{
            chosenOldArray.forEach(function(area) {
                var areaExcluida = '';
                if($j.inArray(area,chosenArray) == -1){
                    areaExcluida = area;
                }
                $j('#area_conhecimento_'+areaExcluida).remove();
                $j('.area_conhecimento_'+areaExcluida).remove();
            }, this);
        }
    }else{
        $j('#componentes').empty();
    }
    chosenOldArray = chosenArray;
} );

function verificaComponenteBloqueado(element) {

  const id = element.id.split('_').last()
  const input = document.getElementById('componente_' + id);

  return input.hasAttribute('disabled')
}

function disableComponent(elements, isChecked, isAnosLetivos = false) {
  elements.each(function () {
    let isBlock = verificaComponenteBloqueado(this)

    if (isBlock) {
      return;
    }
    $j(this).prop("disabled", !isChecked);
  })

  if (!isChecked) {
    $j(this).val('');
  }

  if (isAnosLetivos) {
    $j(this).trigger("chosen:updated");
  }
}

function checkAll(id) {
    const isChecked = $j('#check-all-'+id).is(':checked');

    $j( '.check_componente_area_' + id).each(function () {
      let isDisabled = $j(this).prop('disabled');

      if (isDisabled ) {
        return;
      }

      $j(this).prop( "checked", isChecked );
    });

    const cargasHorarias = $j( '.area_conhecimento_' + id + ' .carga_horaria');
    const tiposNotas = $j( '.area_conhecimento_' + id + ' .tipo_nota');
    const anosLetivos = $j( '.area_conhecimento_' + id + ' .anos_letivos');
    const horasFalta = $j( '.area_conhecimento_' + id + ' .hora_falta');

    disableComponent(cargasHorarias, isChecked);
    disableComponent(tiposNotas, isChecked);
    disableComponent(horasFalta, isChecked);
    disableComponent(anosLetivos, isChecked, true);
}

function reloadChosenAnosLetivos($element){
  if ($element.parent().find('.chosen-container').length > 0) {
      $element.chosen('destroy');
  }
  $element.chosen({
    no_results_text: "Sem resultados para ",
    width: '231px',
    placeholder_text_multiple: "Selecione as opções",
    placeholder_text_single: "Selecione uma opção"
  });
}


function verificaRetorno(response) {
  if (response.msgErro) {
    let msgs = response.msgErro.split("\n");
    msgs.forEach(msg => messageUtils.error(msg));
  }
}

function removeComponent(componente_id) {

  serieId = serieId != '' ? serieId : $j('#ref_cod_serie').val();
  let urlForAtualizaComponentesSerie = postResourceUrlBuilder.buildUrl('/module/Api/ComponentesSerie', 'remove-componentes-serie', {});

  let options = {
    type     : 'POST',
    url      : urlForAtualizaComponentesSerie,
    dataType : 'json',
    data     : {
      serie_id    : serieId,
      componente : componente_id
    },
    success: verificaRetorno
  };

  postResource(options);

}

function defaultModal(componenteId) {
  makeDialog({
    content: 'Tem certeza que deseja remover o compoente? Cajo não exista lancamentos no i-Diario ' +
      'o componente será removido',
    title: 'Atenção!',
    maxWidth: 600,
    width: 600,
    size: 500,
    close: function () {
      habilitaComponente(componenteId)
      $j('#dialog-container').dialog('destroy');
    },
    buttons: [{
      text: 'Ok',
      click: function () {
        removeComponent(componenteId)
        desabilitaComponente(componenteId)
        $j('#dialog-container').dialog('destroy');
      }
    },
    {
      text: 'Cancelar',
      click: function () {
        habilitaComponente(componenteId)
        $j('#dialog-container').dialog('destroy');
      }
    },]
  });
}

function habilitaComponente(componenteId) {
  $j( '#componente_' + componenteId).attr('checked','checked')
  $j( '#carga_horaria_' + componenteId ).prop("disabled", false);
  $j( '#hora_falta_' + componenteId ).prop("disabled", false);
  $j( '#tipo_nota_' + componenteId ).prop("disabled", false);
  $j( '#anos_letivos_' + componenteId ).prop("disabled", false);

  reloadChosenAnosLetivos($j( '#anos_letivos_' + componenteId ))
}

function desabilitaComponente(componenteId) {
    $j( '#carga_horaria_' + componenteId ).prop("disabled", true).val('');
    $j( '#hora_falta_' + componenteId ).prop("disabled", true);
    $j( '#tipo_nota_' + componenteId ).prop("disabled", true).val('');
    $j( '#anos_letivos_' + componenteId ).prop("disabled", true).val('');
    reloadChosenAnosLetivos($j( '#anos_letivos_' + componenteId ));
}

function habilitaCampos(componente_id){
  const isChecked = !$j( '#componente_' + componente_id).is(':checked');

  if (isChecked) {
    defaultModal(componente_id)
  } else {
    habilitaComponente(componente_id)
  }
}

function cloneValues(area_id, componente_id, classe){
    var valor = $j('#' + classe + '_' + componente_id).val();
    var classeClone = '.area_conhecimento_' + area_id + ' .' + classe;

    $j(classeClone+':enabled').each(function(componente) {
        $j(this).val(valor);
        if (classe == 'anos_letivos') {
          $j(this).trigger('chosen:updated');
        }
    }, this);
}

async function expandClose(id){
    const expand = !$j('.area_conhecimento_'+id).is(':visible');
    const loading = document.getElementById('load_' + id);
    const arrow = document.getElementById('expandClose_' + id);
    $j('.area_conhecimento_'+id).toggle('fast');

    if(expand) {
      $j('#expandClose_'+id).css('background-image','url(/intranet/imagens/arrow-up2.png)');
      if (document.getElementsByClassName('area_conhecimento_' + id).length === 0) {
        loading.style.display = 'block'
        arrow.style.display = 'none'
        await carregaComponentesDaArea(id)
        loading.style.display = 'none'
        arrow.style.display = 'block'
      }
    } else {
      $j('#expandClose_'+id).css('background-image','url(/intranet/imagens/arrow-down2.png)');
    }
}

function getCursos(){
    var url = getResourceUrlBuilder.buildUrl('/module/Api/Curso',
                                             'cursos',
                                             { instituicao_id : instituicao_id, ativo : 1 }
    );
    var options = {
        url      : url,
        dataType : 'json',
        success  : handleGetCursos
    };
    getResources(options);
}

function handleGetCursos(response){
    var cursos   = response.cursos;
    var selected = '';

    comboCurso.empty();
    comboCurso.append('<option value="">Selecione um curso</option>');

    for (var i = 0; i <= cursos.length - 1; i++) {
        if (cursos[i].id == curso_id) {
            selected = 'selected';
        }else{
            selected = ''
        }
        comboCurso.append('<option value="' + cursos[i].id + '"' + selected + '>' + cursos[i].nome + '</option>');
    }
}

function getSeries(){
    var url = getResourceUrlBuilder.buildUrl('/module/Api/Serie',
                                             'series-curso',
                                             { curso_id : curso_id }
    );
    var options = {
        url      : url,
        dataType : 'json',
        success  : handleGetSeries
    };
    getResources(options);
}

function getSeriesSemComponentesViculados(){
    var url = getResourceUrlBuilder.buildUrl('/module/Api/Serie',
                                             'series-curso-sem-componentes',
                                             { curso_id : curso_id }
    );
    var options = {
        url      : url,
        dataType : 'json',
        success  : handleGetSeries
    };
    getResources(options);
}

function handleGetSeries(response){
    var series   = response.series;
    var selected = '';
    comboSerie.empty();
    if(series === undefined || series.length == 0){
        comboSerie.append('<option value="">Sem opções</option>');
    }else{
        comboSerie.append('<option value="">Selecione uma série</option>');
    }
    for (var i = 0; i <= series.length - 1; i++) {
        if (series[i].id == serie_id) {
            selected = 'selected';
        }else{
            selected = ''
        }
        comboSerie.append('<option value="' + series[i].id + '"' + selected + '>' + series[i].nome + '</option>');
    }

    updateAreaConhecimento();
}

function carregaDadosComponentesSerie(area_conhecimento_id){
    let url = getResourceUrlBuilder.buildUrl(
      '/module/Api/ComponenteCurricular',
      'componentes-curriculares-serie',
      {
        instituicao_id: instituicao_id,
        serie_id: serie_id,
        area_conhecimento: area_conhecimento_id
      }
    );
    let options = {
        url      : url,
        dataType : 'json',
        success  : handleCarregaDadosComponentesSerie
    };
    getResources(options);
}

function handleCarregaDadosComponentesSerie(response){
    const componentes = response.disciplinas;
    componentes.forEach(function(componente) {
      $j( '#componente_' + componente.id).prop( "checked", true );
      $j( '#carga_horaria_' + componente.id ).val(componente.carga_horaria).prop("disabled", false);
      if(componente.hora_falta != null) {
          $j( '#hora_falta_' + componente.id ).val(Math.round(componente.hora_falta * 60)).prop("disabled", false);
      }
      $j( '#hora_falta_' + componente.id ).prop("disabled", false);
      $j( '#tipo_nota_' + componente.id ).val(componente.tipo_nota).prop("disabled", false);
      $j( '#anos_letivos_' + componente.id ).val(componente.anos_letivos || []).prop("disabled", false);

      reloadChosenAnosLetivos($j( '#anos_letivos_' + componente.id ));

      let textBase = 'Contém restrições em: ';
      let contemRestricao = false;
      let tiposRestricao = [];

      if (componente.contem_notas) {
        contemRestricao = true
        tiposRestricao.push('notas')
      }

      if (componente.contem_faltas) {
        contemRestricao = true
        tiposRestricao.push('falta')
      }

      if (componente.contem_paracer) {
        contemRestricao = true
        tiposRestricao.push('pareceres')
      }

      if (componente.contem_componente_curricular_turma) {
        contemRestricao = true
        tiposRestricao.push('configurado na turma')
      }

      textBase += tiposRestricao.join(', ')

      let icon = '<i class="ml-5 fa fa-question-circle" title="' + textBase +'"></i>';
      if (contemRestricao) {
        $j(icon).insertAfter('#label_componente_' + componente.id)
        $j( '#componente_' + componente.id).prop( "checked", true ).prop("disabled", true);
      }
    }, this);
}

async function carregaComponentesDaArea(id) {
    const url = getResourceUrlBuilder.buildUrl(
      '/module/Api/ComponenteCurricular',
      'componentes-curriculares',
      {
        instituicao_id: instituicao_id,
        area_conhecimento_id: id
      }
    );
    const options = {
        url      : url,
        dataType : 'json',
        success  : handleCarregaComponentesDaArea
    };
    await getPromise(options);
}

function handleCarregaComponentesDaArea(response) {
    var componentes          = response.disciplinas;
    var urlRequisicao        = new URLSearchParams(this.url);
    var area_conhecimento_id = urlRequisicao.get('area_conhecimento_id');

    for (var i = componentes.length - 1; i >= 0 ; i--) {
        var firstLine = i == 0;
        $j(htmlComponentesAreaConhecimento(
          componentes[i].area_conhecimento_id,
          componentes[i].id,
          componentes[i].nome,
          firstLine)).insertAfter('#area_conhecimento_' + componentes[i].area_conhecimento_id);
    }

    $j(htmlSubCabecalhoAreaConhecimento(area_conhecimento_id)).insertAfter('#area_conhecimento_' + area_conhecimento_id);

    if(serie_id != '') {
        carregaDadosComponentesSerie(area_conhecimento_id);
    }
    reloadChosenAnosLetivos($j('.anos_letivos'));
}

function handleGetAreaConhecimento(response) {
    var areaConhecimentoField = $j('#ref_cod_area_conhecimento');

    var selectOptions = {};

    response['areas'].forEach((area) => {
        selectOptions[area.id] = area.nome_agrupador;
    }, {});

    updateChozen(areaConhecimentoField, selectOptions);

    if (serie_id != '') {
        getAreaConhecimentoSerie();
    }
}

function updateAreaConhecimento(){
      var instituicao_id = $j('#ref_cod_instituicao').val();
      var areaConhecimentoField = $j('#ref_cod_area_conhecimento');

      clearValues(areaConhecimentoField);
      if (instituicao_id != '') {

        var url = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areas-de-conhecimento', {
          instituicao_id : instituicao_id
        });

        var options = {
          url : url,
          dataType : 'json',
          success  : handleGetAreaConhecimento
        };

        getResources(options);
      }
}

function handleGetAreaConhecimentoSerie(response) {
    $j('#ref_cod_area_conhecimento').val('').trigger('liszt:updated');
  $j.each(response['options'], function(index, item) {
        $j("#ref_cod_area_conhecimento").children("[value=" + item.id + "]").attr('selected', '');
        $j("#ref_cod_area_conhecimento").chosen().trigger("chosen:updated");
        let anos_letivos = item.anos_letivos.replace('{', '').replace('}', '');
        $j('#componentes').append(htmlCabecalhoAreaConhecimento(item.id, item.nome, anos_letivos));
    });
  chosenOldArray = $j("#ref_cod_area_conhecimento").chosen().val();
}


function getAreaConhecimentoSerie(){
    var url = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areaconhecimento-serie', {
        serie_id : serie_id
    });
    var options = {
        url : url,
        dataType : 'json',
        success  : handleGetAreaConhecimentoSerie
    };
    getResources(options);
}

function htmlCabecalhoAreaConhecimento(id, nome, anos_letivos = null) {

    let label = '';
    if (anos_letivos !== null) {
      label = '<label></label> <i class="ml-5 fa fa-info-circle" title="Usado em: '  + anos_letivos  + '"></i>';
    }
    return `<tr id="area_conhecimento_` + id + `"
                class="area_conhecimento_title">
                <td colspan="2">` + nome + ` ` + label + `
               </td>
                <td class="td_check_all">
                </td>
                <td colspan="2" style="text-align: right;">
                     <div id="expandClose_` + id + `"
                          onClick="expandClose(` + id + `)"
                          style="background-image: url(/intranet/imagens/arrow-down2.png);
                                 width: 15px;
                                 height: 15px;
                                 background-size: cover;
                                 float: right;
                                 cursor: pointer;">
                     </div>
                     <div
                        id="load_` + id + `"
                        style="display: none">
                        <div>
                            <svg
                              class="x-spinner-mat"
                              width="20px"
                              height="20px"
                              viewBox="25 25 50 50"
                              style="text-color: #47728f"
                            >
                              <circle
                                class="path"
                                cx="50"
                                cy="50"
                                r="20"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="5"
                                stroke-miterlimit="10">
                              </circle>
                            </svg>
                          </div>
                     </div>
                </td>
            </tr>`;
}

function htmlSubCabecalhoAreaConhecimento(id){
    return `<tr class="area_conhecimento_` + id + `">
                <td colspan="2">
                    <label>
                        <input onClick="checkAll(` + id + `)" id="check-all-` + id + `" type="checkbox"/>
                        <b>Nome</b>
                    </label>
                </td>
                <td>
                    <b>Carga horária</b>
                </td>
                <td>
                    <b>Hora falta (min)</b>
                </td>
                <td>
                    <b>Tipo de nota</b>
                </td>
                <td>
                    <b>Anos letivos</b>
                </td>
            </tr>`;
}

let _optionsAnoLetivo = undefined;

function optionsAnoLetivo() {
  if(_optionsAnoLetivo === undefined ){
    _optionsAnoLetivo = JSON.parse(decodeURIComponent($j('#sugestao_anos_letivos').val()));
    _optionsAnoLetivo = _optionsAnoLetivo.map((ano) => `<option>${ano}</option>`).join('');
  }
  return _optionsAnoLetivo;
}

function htmlComponentesAreaConhecimento(id, componente_id, componente_nome, firstLine){

    var iconCloneCargaHoraria = '';
    var iconCloneTipoNota = '';
    var iconCloneAnosLetivos = '';

    if(firstLine) {
        iconCloneCargaHoraria = `<a class="clone-values"
                                    onclick="cloneValues(` + id + `,` + componente_id + `, 'carga_horaria')">
                                    <i class="fa fa-clone" aria-hidden="true"></i>
                                 </a>`;
        iconCloneTipoNota = `<a class="clone-values"
                                onclick="cloneValues(` + id + `,` + componente_id + `, 'tipo_nota')">
                                <i class="fa fa-clone" aria-hidden="true"></i>
                             </a>`;
        iconCloneAnosLetivos = `<a class="clone-values"
                                onclick="cloneValues(${id}, ${componente_id}, 'anos_letivos')">
                                <i class="fa fa-clone" aria-hidden="true"></i>
                             </a>`;
    }

    return `<tr class="area_conhecimento_` + id + `">
                <td colspan="2">
                    <label
                        id="label_componente_` + componente_id + `"
                    >
                        <input type="checkbox"
                               name="componentes[` + id + componente_id + `][id]"
                               class="check_componente_area_`+ id +`"
                               id="componente_` + componente_id + `"
                               value="` + componente_id + `"
                               onclick="habilitaCampos(` + componente_id + ',' + id + ` )">` +
                        componente_nome +
                    `</label>
                </td>
                <td>
                    <input type="text"
                           size="5"
                           maxlength="5"
                           name="componentes[` + id + componente_id + `][carga_horaria]"
                           class="carga_horaria"
                           id="carga_horaria_` + componente_id + `"
                           value=""
                           disabled>
                           ` + iconCloneCargaHoraria + `
                </td>
                <td>
                    <input type="text"
                           size="5"
                           maxlength="5"
                           name="componentes[` + id + componente_id + `][hora_falta]"
                           class="carga_horaria"
                           id="hora_falta_` + componente_id + `"
                           value=""
                           disabled>

                </td>
                <td>
                    <select name="componentes[` + id + componente_id + `][tipo_nota]"
                            class="tipo_nota"
                            id="tipo_nota_` + componente_id + `"
                            disabled>
                        <option value="">Selecione</option>
                        <option value="1">Numérica</option>
                        <option value="2">Conceitual</option>
                    </select>
                    ` + iconCloneTipoNota + `
                </td>
                <td>
                    <select name="componentes[` + id + componente_id + `][anos_letivos]"
                            class="anos_letivos"
                            style="width: 231px;"
                            disabled="disabled"
                            multiple="multiple"
                            id="anos_letivos_` + componente_id + `"
                             >`+
                    optionsAnoLetivo()
                    +` </select>
                    ` + iconCloneAnosLetivos + `
                </td>
            </tr>`;
}
