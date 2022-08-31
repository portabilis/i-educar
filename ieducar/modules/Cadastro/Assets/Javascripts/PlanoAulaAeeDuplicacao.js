(function($){
    $(document).ready(function(){
        var id = $j('#id').val();
        var copy = $j('#copy').val();

        if (!isNaN(id) && copy) {
            var submitButton = $j('#btn_enviar');
            submitButton.removeAttr('onclick');

            submitButton.click(function () {
                cadastrarPlanoAulaDuplicata();
            });

            function cadastrarPlanoAulaDuplicata () {
                acao();
            }
        }
    });
})(jQuery);
