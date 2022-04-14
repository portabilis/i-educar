(function($){
    $(document).ready(function(){
        var id = $j('#id').val();
        var copy = $j('#copy').val();

        if (isNaN(id) || id === '')
            return;

        if (!isNaN(id) && copy)
            return;

        const desativado = document.getElementById('ref_cod_turma').value != '' && document.getElementById('fase_etapa').value != '';
        document.getElementById('data_inicial').disabled = desativado;
        document.getElementById('data_final').disabled = desativado;

        document.getElementById('data_inicial').onchange = function () {
            const ano = document.getElementById('data_inicial').value.split('/')[2];
            const anoElement = document.getElementById('ano');
            anoElement.value = ano;

            var evt = document.createEvent('HTMLEvents');
            evt.initEvent('change', false, true);
            anoElement.dispatchEvent(evt);
        };
    });
})(jQuery);
