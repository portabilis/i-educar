(function($){
    $(document).ready(function(){
        if (document.getElementById('frequencia')) {
            document.getElementById('frequencia').onchange = function () {
                document.getElementById('conteudos_chosen').setAttribute('style', 'width: 100%');

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
                        $('#especificacoes').html(
                            (Object.keys(dataResponse.pac[0] || []).map(key => `<option value='${key}'>${dataResponse.pac[0][key]}</option>`)).join()
                        ).trigger('chosen:updated');

                        $('#conteudos').html(
                            (Object.keys(dataResponse.pac[1] || []).map(key => `<option value='${key}'>${dataResponse.pac[1][key]}</option>`)).join()
                        ).trigger('chosen:updated');
                    }
                };

                getResource(options);
            };
        }
    });
})(jQuery);

