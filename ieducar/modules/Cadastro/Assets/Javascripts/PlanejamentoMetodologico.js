document.getElementById('data_inicial').onchange = function () {
    const ano = document.getElementById('data_inicial').value.split('/')[2];
    const anoElement = document.getElementById('ano');
    anoElement.value = ano;

    var evt = document.createEvent('HTMLEvents');
    evt.initEvent('change', false, true);
    anoElement.dispatchEvent(evt);
};
