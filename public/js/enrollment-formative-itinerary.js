habilitaComposicaoItinerario();
habilitaCamposFormacaoTecnica();
habilitaCampoCursoTecnico();

$j('#itinerary_type').change(habilitaComposicaoItinerario);
$j('#itinerary_composition').change(habilitaCamposFormacaoTecnica);
$j('#itinerary_course').change(habilitaCampoCursoTecnico);

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
    }
    habilitaCamposFormacaoTecnica();
}

function habilitaCamposFormacaoTecnica() {
    let types = [];
    let compositions = [];

    if ($j('#itinerary_type').val()) {
        types = $j('#itinerary_type').val();
    }

    if ($j('#itinerary_composition').val()) {
        compositions = $j('#itinerary_composition').val();
    }

    if (compositions.includes('5') && types.includes('5')) {
        $j('#itinerary_course').removeAttr('disabled');
        addSpanRequiredField('tr_itinerary_course');
        if ($j('#show_concomitant_itinerary').val() == '1') {
            $j('#concomitant_itinerary').removeAttr('disabled');
            addSpanRequiredField('tr_concomitant_itinerary');
        }
    } else {
        if ($j('#show_concomitant_itinerary').val() == '1') {
            $j('#concomitant_itinerary').attr('disabled', 'disabled');
            $j('#concomitant_itinerary').val('');
            removeSpanRequiredField('tr_concomitant_itinerary');
        } else {
            $j('#tr_concomitant_itinerary').hide();
        }
        $j('#itinerary_course').attr('disabled', 'disabled');
        $j('#itinerary_course').val('');
        removeSpanRequiredField('tr_itinerary_course');
    }
}

function habilitaCampoCursoTecnico() {
    let types = [];

    if ($j('#itinerary_course').val()) {
        types = $j('#itinerary_course').val();
    }

    if (types.includes('1')) {
        $j('#technical_course').removeAttr('disabled');
        addSpanRequiredField('tr_technical_course');
    } else {
        removeSpanRequiredField('tr_technical_course');
        $j('#technical_course').attr('disabled', 'disabled');
        $j('#technical_course').val('');
    }
}

function addSpanRequiredField(trElement) {
    removeSpanRequiredField(trElement);
    $j('#' + trElement + ' td:nth-child(1)').append('<span class="campo_obrigatorio">*</span>');
}

function removeSpanRequiredField(trElement) {
    $j('#' + trElement + ' td:nth-child(1) span').remove();
}
