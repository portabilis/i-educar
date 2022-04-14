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
                        $('#conteudos').html(
                            (Object.keys(dataResponse.pac || []).map(key => `<option value='${key}'>${dataResponse.pac[key]}</option>`)).join()
                        ).trigger('chosen:updated');
                    }
                };

                getResource(options);
            };
        }
    });
})(jQuery);

