document.getElementById('frequencia').onchange = function () {
    const frequencia = document.getElementById('frequencia').value;
    console.log(frequencia);
    document.getElementById('frequencia_').value = frequencia;

    if (!frequencia) {
        $('#bncc').val([]).empty().trigger('chosen:updated');
        getResource(false);
    }

    let url = getResourceUrlBuilder.buildUrl(
        '/module/Api/BNCC',
        'bncc',
        { frequencia : frequencia }
    );

    var options = {
        url      : url,
        dataType : 'json',
        success  : function (dataResponse) {
            console.log(dataResponse.bncc)
            $('#bncc').html(
            (dataResponse.bncc||[]).map(code => `<option value='${code}'>${code}</option>`).join()
            ).trigger('chosen:updated');
        }
    };

    getResource(options);
};

(function($){
    $(document).ready(function(){
        console.log('adawda');
        // const codSerie = document.getElementById('cod_serie').value
        // const codComponenteCurricular = document.getElementById('cod_componente_curricular').value

        // if (!codSerie || !codComponenteCurricular) {
        //     $('#bncc').val([]).empty().trigger('chosen:updated');
        //     getResource(false);
        // }

        // let url = getResourceUrlBuilder.buildUrl(
        // '/module/Api/BNCC',
        // 'bncc',
        // { cod_serie : codSerie, cod_componente_curricular: codComponenteCurricular }
        // );

        // var options = {
        //     url      : url,
        //     dataType : 'json',
        //     success  : function (dataResponse) {
        //         $('#bncc').html(
        //         (dataResponse.bncc||[]).map(code => `<option value='${code}'>${code}</option>`).join()
        //         ).trigger('chosen:updated');
        //     }
        // };

        // getResource(options);
    });
})(jQuery);