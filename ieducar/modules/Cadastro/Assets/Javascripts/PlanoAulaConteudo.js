(function($){
    $(document).ready(function(){
        if (document.getElementById('frequencia')) {
            document.getElementById('frequencia').onchange = function () {
                const codfrequencia = document.getElementById('frequencia').value;

                if (!codfrequencia) {
                    $('#conteudos').val([]).empty().trigger('chosen:updated');
                    getResource(false);
                }

                let url = getResourceUrlBuilder.buildUrl(
                    '/module/Api/PlanejamentoAulaConteudo',
                    'pac',
                    { frequencia : codfrequencia }
                );
            
                var options = {
                    url      : url,
                    dataType : 'json',
                    success  : function (dataResponse) {
                        // $('#especificacoes').val([]).empty().trigger('chosen:updated');
                        $('#conteudos').val([]).empty().trigger('chosen:updated');

                        if (dataResponse.pac != null) {
                            // $('#especificacoes').html(
                            //     (Object.keys(dataResponse.pac[0] || []).map(key => `<option value='${key}'>${dataResponse.pac[0][key]}</option>`)).join()
                            // ).trigger('chosen:updated');

                            $('#conteudos').html(
                                (Object.keys(dataResponse.pac[1] || [])
                                    .map(key => !dataResponse.pac[1][key][1]
                                        ? `<option value='${key}'>${dataResponse.pac[1][key][0]}</option>`
                                        : `<option value='${key}' style="color:blue">${dataResponse.pac[1][key][0]}</option>`)).join()
                            ).trigger('chosen:updated');
                        }
                    }
                    
                };
                console.log(options);
                getResource(options);
            };
        }
    });
})(jQuery);

