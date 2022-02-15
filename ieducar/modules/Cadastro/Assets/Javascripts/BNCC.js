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
                        console.log($('#bncc'));
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
                console.log('Turma');
                const codTurma = document.getElementById('ref_cod_turma').value

                if (!codTurma) {
                    $('#bncc').val([]).empty().trigger('chosen:updated');
                    getResource(false);
                }

                let url = getResourceUrlBuilder.buildUrl(
                '/module/Api/BNCC',
                'bncc_turma',
                { turma : codTurma }
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
    });
})(jQuery);

(function a($){
    $(document).ready(function(){
        
    });
})(jQuery);