var comboCurso = $j('#ref_cod_curso');
var comboSerie = $j('#ref_cod_serie');
var instituicao_id = $j('#ref_cod_instituicao').val();
var urlBusca =  new URLSearchParams(window.location.href);
var curso_id = urlBusca.get('ref_cod_curso');
var serie_id = urlBusca.get('ref_cod_serie');
if (instituicao_id != '') {
    getCursos();
}
if (curso_id != '') {
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
});

$j("#ref_cod_curso").change(function() {
    curso_id = $j('#ref_cod_curso').val();
    if (curso_id != '') {
        getSeries();
    }else{
        comboSerie.empty();
        comboSerie.append('<option value="">Selecione uma série</option>');
    }
});

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

function handleGetSeries(response){
    var series   = response.series;
    var selected = '';
    if(series.length == 0){
        comboSerie.empty();
        comboSerie.append('<option value="">Sem opções</option>');
    }else{
        comboSerie.empty();
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
}
