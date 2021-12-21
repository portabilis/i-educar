(function($){
    $(document).ready(function(){
        document.getElementById('frequencia').onchange = function () {
            console.log('Frequência');
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
    });
})(jQuery);

(function a($){
    $(document).ready(function(){
        
    });
})(jQuery);