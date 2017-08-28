var instituicao_id = $j('#ref_cod_instituicao').val();
var curso_id = $j('#curso_id').val();
var serie_id = $j('#serie_id').val();
var chosenOldArray = [];
var guardaAreas = [];

if (instituicao_id != '') {
    getCursos(instituicao_id);
    $j('#ref_cod_instituicao').attr('disabled', 'true');
    updateAreaConhecimento();  
}

if (curso_id != '') {
    getSeries(curso_id);
    $j('#ref_cod_curso').attr('disabled', 'true');    
}

if (serie_id != '') {
    $j('#ref_cod_serie').attr('disabled', 'true');
    montaElementosEdicao(); 
}

$j("#ref_cod_instituicao").change(function() {
    var instituicao_id = $j('#ref_cod_instituicao').val();
    getCursos(instituicao_id);
    updateAreaConhecimento();
});

$j("#ref_cod_curso").change(function() {
    var curso_id = $j('#ref_cod_curso').val();
    getSeries(curso_id);
});

function getCursos(instituicao_id) {
  var searchPath = '../module/Api/Curso?oper=get&resource=cursos';
  var params = {instituicao_id : instituicao_id}

  if (instituicao_id != '') {
    $j.get(searchPath, params, function(data){
        var cursos     = data.cursos;
        var curso_id   = $j('#curso_id').val();
        var comboCurso = $j('#ref_cod_curso');
        var selected   = '';
        
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
    });
  }else{
    var comboCurso = $j('#ref_cod_curso');
    comboCurso.empty();
    comboCurso.append('<option value="">Selecione um curso</option>');
  }
}

function getSeries(curso_id) {
  var searchPath = '../module/Api/Serie?oper=get&resource=series-curso';
  var params = {curso_id : curso_id}

  if (curso_id != '') {
    $j.get(searchPath, params, function(data){
        var series     = data.series;
        var serie_id   = $j('#serie_id').val();
        var comboSerie = $j('#ref_cod_serie');
        var selected   = '';

        comboSerie.empty();
        comboSerie.append('<option value="">Selecione uma série</option>');
        
        for (var i = 0; i <= series.length - 1; i++) {

            if (series[i].id == serie_id) {
                selected = 'selected';
            }else{
                selected = ''
            }

            comboSerie.append('<option value="' + series[i].id + '"' + selected + '>' + series[i].nome + '</option>');
        }
    });
  }else{
    var comboSerie = $j('#ref_cod_serie');
    comboSerie.empty();
    comboSerie.append('<option value="">Selecione uma série</option>');
  }
}

function getComponentesCurricularesSerie(){
    var searchPath = '../module/Api/ComponenteCurricular?oper=get&resource=componentes-curriculares-serie';
    var params     = {instituicao_id : instituicao_id,
                      serie_id : serie_id}

    $j.get(searchPath, params, function(data){
        componentes = data.disciplinas;
        
        componentes.forEach(function(componente) {
            $j( '#componente_' + componente.id).prop( "checked", true );
            $j( '#carga_horaria_' + componente.id ).val(componente.carga_horaria);
        }, this);
    });
}


//carrega areas e componentes
function montaElementosEdicao(){
    var instituicao_id = $j('#ref_cod_instituicao').val();
    var serie_id   = $j('#serie_id').val();
    var searchPath = '../module/Api/ComponenteCurricular?oper=get&resource=componentes-curriculares-serie';
    var params     = {instituicao_id : instituicao_id,
                      serie_id : serie_id}

    $j.get(searchPath, params, function(data){
        var componentes = data.disciplinas;

        var areas = {};

        componentes.forEach(function(area) {
            areas[area.area_conhecimento_id] = area.nome_area;
        }, {});
        
        $j.each(areas, function(id, area) {
            $j('#componentes').append(`<tr id="area_conhecimento_` + id + `" class="area_conhecimento_title">
                                        </tr>`);
            getComponentesCurriculares(id);
        });
    });
}

function getComponentesCurriculares(area_conhecimento_id){
    var instituicao_id = $j('#ref_cod_instituicao').val();
    var searchPath = '../module/Api/ComponenteCurricular?oper=get&resource=componentes-curriculares';
    var params     = {instituicao_id : instituicao_id,
                      area_conhecimento_id : area_conhecimento_id}

    $j.get(searchPath, params, function(data){
        var componentes = data.disciplinas;
        var areas = {};
        componentes.forEach(function(area) {
                areas[area.area_conhecimento_id] = area.nome_area;
        }, {});

        $j.each(areas, function(id, area) {
            $j('#area_conhecimento_' + id).append(`<td colspan"2">` + area + `</td>`);
        });

        for (var i = 0; i <= componentes.length - 1; i++) {
            
            $j(`<tr class="area_conhecimento_` + componentes[i].area_conhecimento_id + `">
                    <td>
                        <label><input type="checkbox" name="componentes[` + componentes[i].area_conhecimento_id + i + `][id]" class="check-componente" id="componente_` + componentes[i].id + `" value="` + componentes[i].id + `">` + componentes[i].nome + `</label>
                    </td>
                    <td>
                        <input type="text" size="5" maxlength="5" name="componentes[` + componentes[i].area_conhecimento_id + i + `][carga_horaria]" class="check-carga-horaria" id="carga_horaria_` + componentes[i].id + `" value="">
                    </td>
                </tr>`).insertAfter('#area_conhecimento_' + componentes[i].area_conhecimento_id);
        }
        if(serie_id != ''){
            getComponentesCurricularesSerie();
        }
    });
}

function handleGetAreaConhecimento(response) {
    var areaConhecimentoField = $j('#ref_cod_area_conhecimento');

    var selectOptions = {};

    response['areas'].forEach((area) => {
    selectOptions[area.id] = area.nome
    }, {});
    
    updateChozen(areaConhecimentoField, selectOptions);

    if (serie_id != '') { 
        updateAreaConhecimentoSerie();
    }
}

function updateAreaConhecimento(){
      var instituicao_id = $j('#ref_cod_instituicao').val();
      var areaConhecimentoField = $j('#ref_cod_area_conhecimento');

      clearValues(areaConhecimentoField);
      if (instituicao_id != '') {

        var urlForGetAreaConhecimento = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areas-de-conhecimento', {
          instituicao_id : instituicao_id
        });

        var options = {
          url : urlForGetAreaConhecimento,
          dataType : 'json',
          success  : handleGetAreaConhecimento
        };

        getResources(options);
      }
}

function handleGetAreaConhecimentoSerie(response) {
    $j('#ref_cod_area_conhecimento').val('').trigger('liszt:updated');
    $j.each(response['options'], function(id,nome) {
        $j("#ref_cod_area_conhecimento").children("[value=" + id + "]").attr('selected', '');
        $j("#ref_cod_area_conhecimento").chosen().trigger("chosen:updated");
    });
    chosenOldArray = $j("#ref_cod_area_conhecimento").chosen().val();
}

function updateAreaConhecimentoSerie(){

      if (serie_id != '') {

        var urlForGetAreaConhecimentoSerie = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areaconhecimento-serie', {
          serie_id : serie_id
        });

        var options = {
          url : urlForGetAreaConhecimentoSerie,
          dataType : 'json',
          success  : handleGetAreaConhecimentoSerie
        };

        getResources(options);
      }
}

$j("#ref_cod_area_conhecimento").change(function() {
    var chosenArray = $j("#ref_cod_area_conhecimento").chosen().val();
    if (!chosenOldArray) {
        chosenOldArray = [];
    }
    if(chosenArray && chosenOldArray){
        if (chosenArray.length > chosenOldArray.length) {
            chosenArray.forEach(function(area) {
                if (!$j('#area_conhecimento_' + area).length && area != '') {
                    $j('#componentes').append(`<tr id="area_conhecimento_` + area + `" class="area_conhecimento_title">
                                            </tr>`);
                    getComponentesCurriculares(area);
                }
            }, this);
        }else{
            chosenOldArray.forEach(function(area) {
                var areaExcluida = '';
                if($j.inArray(area,chosenArray) == -1){
                    areaExcluida = area;
                };
                $j('#area_conhecimento_'+areaExcluida).remove();
                $j('.area_conhecimento_'+areaExcluida).remove();
            }, this);
        }
    }else{
        $j('#componentes').empty();
    }
    chosenOldArray = chosenArray;
} );