habilitaComposicaoItinerario();
habilitaCamposFormacaoTecnica();

$j('#itinerary_type').change(habilitaComposicaoItinerario);
$j('#itinerary_composition').change(habilitaCamposFormacaoTecnica);

function habilitaComposicaoItinerario() {
    let types = [];

    if ($j('#itinerary_type').val()) {
        types = $j('#itinerary_type').val();
    }

    if (types.includes('6')) {
        $j('#itinerary_composition').removeAttr('disabled');
        $j('#itinerary_composition').trigger('chosen:updated');
        addSpanRequiredField('tr_itinerary_composition');
    } else {
        $j('#itinerary_composition').attr('disabled', 'disabled');
        $j('#itinerary_composition').val([]).trigger('chosen:updated');
        removeSpanRequiredField('tr_itinerary_composition');
        habilitaCamposFormacaoTecnica();
    }
}

function habilitaCamposFormacaoTecnica() {
    let compositions = [];

    if ($j('#itinerary_composition').val()) {
        compositions = $j('#itinerary_composition').val();
    }

    if (compositions.includes('5')) {
        $j('#itinerary_course').removeAttr('disabled');
        $j('#concomitant_itinerary').removeAttr('disabled');
        addSpanRequiredField('tr_itinerary_course');
        addSpanRequiredField('tr_concomitant_itinerary');
    } else {
        $j('#itinerary_course').attr('disabled', 'disabled');
        $j('#concomitant_itinerary').attr('disabled', 'disabled');
        $j('#itinerary_course').val('');
        $j('#concomitant_itinerary').val('');
        removeSpanRequiredField('tr_itinerary_course');
        removeSpanRequiredField('tr_concomitant_itinerary');
    }
}

function addSpanRequiredField(trElement) {
    removeSpanRequiredField(trElement);
    $j('#' + trElement + ' td:nth-child(1)').append('<span class="campo_obrigatorio">*</span>');
}

function removeSpanRequiredField(trElement) {
    $j('#' + trElement + ' td:nth-child(1) span').remove();
}
