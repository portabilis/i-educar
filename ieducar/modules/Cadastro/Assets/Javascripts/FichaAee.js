(function($){
    $(document).ready(function(){
        var id = $j('#id').val();
        var copy = $j('#copy').val();

        if (isNaN(id) || id === '')
            return;

        if (!isNaN(id) && copy)
            return;

        document.getElementById('data').onchange = function () {
            const ano = document.getElementById('data').value.split('/')[2];
            const anoElement = document.getElementById('ano');
            anoElement.value = ano;

            var evt = document.createEvent('HTMLEvents');
            evt.initEvent('change', false, true);
            anoElement.dispatchEvent(evt);
        };
    });
})(jQuery);
