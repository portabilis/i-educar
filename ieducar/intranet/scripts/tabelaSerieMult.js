configuraCamposExibidos();

$j('#multiseriada').change(function(){
    configuraCamposExibidos();
});

function configuraCamposExibidos() {
    let turmaMultisseriada = $j('#multiseriada').is(':checked');
    
    if (turmaMultisseriada) {
        $j('#tr_ref_cod_curso').hide();
        $j('#tr_ref_cod_serie').hide();
        $j('#tr_turma_serie').show();
    } else {
        $j('#tr_ref_cod_curso').show();
        $j('#tr_ref_cod_serie').show();
        $j('#tr_turma_serie').hide();
    }
}

function atualizaOpcoesDeSerie(input) {
    let instituicaoId = $j('#ref_cod_instituicao').val();
    let escolaId = $j('#ref_cod_escola').val();
    let cursoId = input.value;
    let linha = input.id.replace(/\D/g, '');

    var url = getResourceUrlBuilder.buildUrl(
        '/module/Api/Serie',
        'series',
        {
            instituicao_id : instituicaoId,
            escola_id : escolaId,
            curso_id : cursoId,
            ativo : 1
        }
    );

    var options = {
        url      : url,
        dataType : 'json',
        success  : function(response) {
            let comboSerie = $j('select[id="mult_serie_id['+linha+']"]');
            comboSerie.empty();
            comboSerie.append('<option value="">Selecione uma série</option>');

            $j.each(response.series, function(key, serie) {
                comboSerie.append('<option value="' + serie.id + '">' + serie.nome + '</option>');
            });
        }
    };

    getResources(options);
}
