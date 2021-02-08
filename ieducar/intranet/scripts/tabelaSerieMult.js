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
