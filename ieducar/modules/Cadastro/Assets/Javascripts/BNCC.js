(function($){
    $(document).ready(function(){
        if (document.getElementById('frequencia')) {
            document.getElementById('frequencia').onchange = function () {
                pegarBNCCFrequencia();
            };
        }

        if (document.getElementById('ref_cod_turma') && document.getElementById('ref_cod_componente_curricular')) {
            document.getElementById('ref_cod_componente_curricular').onchange = function () {
                document.getElementById('bncc_chosen').setAttribute('style', 'width: 100%');

                const codTurma = document.getElementById('ref_cod_turma').value
                const codComponenteCurricular = document.getElementById('ref_cod_componente_curricular').value

                if (!codTurma || !codComponenteCurricular) {
                    $('#bncc').val([]).empty().trigger('chosen:updated');
                    getResource(false);

                    return;
                }

                let url = getResourceUrlBuilder.buildUrl(
                    '/module/Api/BNCC',
                    'bncc_turma',
                    { turma : codTurma, componente_curricular: codComponenteCurricular }
                );

                var options = {
                    url      : url,
                    dataType : 'json',
                    success  : function (dataResponse) {
                        console.log($('#bncc'));
                        $('#bncc').html(
                            (Object.keys(dataResponse.bncc || []).map(key => `<option value='${key}'>${dataResponse.bncc[key]}</option>`)).join()
                        ).trigger('chosen:updated');
                    }
                };

                getResource(options);
            };
        }

        function pegarBNCCFrequencia () {
            const codfrequencia = document.getElementById('frequencia').value
            const codcampoExperiencia = document.getElementById('campoExperiencia').value

            if (!codfrequencia) {
                $('#bncc').val([]).empty().trigger('chosen:updated');
                getResource(false);
            }

            let url = getResourceUrlBuilder.buildUrl(
            '/module/Api/BNCC',
            'bncc',
                {
                    frequencia : codfrequencia,
                    campoExperiencia: codcampoExperiencia
                }
            );

            var options = {
                url      : url,
                dataType : 'json',
                success  : function (dataResponse) {
                    $('#bncc').html(
                        (Object.keys(dataResponse.bncc || []).map(key => `<option value='${key}'>${dataResponse.bncc[key]}</option>`)).join()
                    ).trigger('chosen:updated');
                }
            };

            getResource(options);
        }
    });
})(jQuery);
