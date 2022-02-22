(function($){
    $(document).ready(function(){
        if (document.getElementById('frequencia')) {
            document.getElementById('frequencia').onchange = function () {
                const codfrequencia = document.getElementById('frequencia').value

                if (!codfrequencia) {
                    $('#bncc').val([]).empty().trigger('chosen:updated');
                    getResource(false);
                }

                let url = getResourceUrlBuilder.buildUrl(
                '/module/Api/BNCC',
                'bncc',
                { frequencia : codfrequencia }
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
            };
        }

        if (document.getElementById('ref_cod_turma')) {
            document.getElementById('ref_cod_turma').onchange = function () {
                const codTurma = document.getElementById('ref_cod_turma').value

                if (!codTurma) {
                    $('#bncc').val([]).empty().trigger('chosen:updated');
                    getResource(false);
                }

                let url = getResourceUrlBuilder.buildUrl(
                '/module/Api/BNCC',
                'bncc_turma',
                { turma : codTurma}
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
            };

            document.getElementById('ref_cod_componente_curricular').onchange = function () {
                const codTurma = document.getElementById('ref_cod_turma').value
                const codComponenteCurricular = document.getElementById('ref_cod_componente_curricular').value

                if (!codTurma) {
                    $('#bncc').val([]).empty().trigger('chosen:updated');
                    getResource(false);
                }

                let url = getResourceUrlBuilder.buildUrl(
                '/module/Api/BNCC',
                'bncc_turma',
                { turma : codTurma, componente_curricular: codComponenteCurricular}
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
            };
        }
    });
})(jQuery);
