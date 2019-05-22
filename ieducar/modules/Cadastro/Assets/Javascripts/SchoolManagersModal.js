$j('body').append(htmlFormModal());
$j('#modal_school_managers').find(':input').css('display', 'block');

var idLastLineUsed = null;

$j("#modal_school_managers").dialog({
    autoOpen: false,
    height: 'auto',
    width: 'auto',
    modal: true,
    resizable: false,
    draggable: false,
    title: 'Dados adicionais do(a) gestor(a)',
    buttons: {
        "Gravar": function () {
            if (validateAccessCriteriaId() && validateAccessCriteriaDescription() && validateLinkType()) {
                fillHiddenInputs();
                $j(this).dialog("close");
            }
        },
        "Cancelar": function () {
            $j(this).dialog("close");
        }
    },
    create: function () {
        $j(this)
            .closest(".ui-dialog")
            .find(".ui-button-text:first")
            .addClass("btn-green");
    },
    close: function () {

    },
    hide: {
        effect: "clip",
        duration: 500
    },
    show: {
        effect: "clip",
        duration: 500
    }
});

function modalOpen(thisElement) {
    var elementLine = $j(thisElement).closest('td').attr('id');
    var line = elementLine.replace(/\D/g, '');
    idLastLineUsed = line;
    fillInputs();
    addEventsManagerInputs();
    $j("#modal_school_managers").dialog("open");
    changeManagerRole($j('select[id="managers_role_id[' + idLastLineUsed + ']"]'));
}

function fillHiddenInputs() {
    let accessCriteriaId = $j("#managers_access_criteria_id").val(),
        accessCriteriaIdDescription = $j("#managers_access_criteria_description").val(),
        linkTypeId = $j("#managers_link_type_id").val(),
        email = $j("#managers_email").val();

    $j('input[id^="managers_access_criteria_id[' + idLastLineUsed + ']').val(accessCriteriaId);
    $j('input[id^="managers_access_criteria_description[' + idLastLineUsed + ']').val(accessCriteriaIdDescription);
    $j('input[id^="managers_link_type_id[' + idLastLineUsed + ']').val(linkTypeId);
    $j('input[id^="managers_email[' + idLastLineUsed + ']').val(email);
}

function fillInputs() {
    let accessCriteriaId = $j('input[id^="managers_access_criteria_id[' + idLastLineUsed + ']').val(),
        accessCriteriaIdDescription = $j('input[id^="managers_access_criteria_description[' + idLastLineUsed + ']').val(),
        linkTypeId = $j('input[id^="managers_link_type_id[' + idLastLineUsed + ']').val(),
        email = $j('input[id^="managers_email[' + idLastLineUsed + ']').val();

    $j("#managers_access_criteria_id").val(accessCriteriaId);
    $j("#managers_access_criteria_description").val(accessCriteriaIdDescription);
    $j("#managers_link_type_id").val(linkTypeId);
    $j("#managers_email").val(email);
}

function htmlFormModal() {
    return `<div id="modal_school_managers">
                <form>
                    <label for="managers_access_criteria_id">Critério de acesso ao cargo</label>
                    <select class="geral" name="managers_access_criteria_id" id="managers_access_criteria_id">
                        <option value="">Selecione</option>
                        <option value="1">Proprietário(a) ou sócio(a)-proprietário(a) da escola</option>
                        <option value="2">Exclusivamente por indicação/escolha da gestão</option>
                        <option value="3">Processo seletivo qualificado e escolha/nomeação da gestão</option>
                        <option value="4">Concurso público específico para o cargo de gestor escolar</option>
                        <option value="5">Exclusivamente por processo eleitoral com a participação da comunidade escolar</option>
                        <option value="6">Processo seletivo qualificado e eleição com a participação da comunidade escolar</option>
                        <option value="7">Outros</option>
                    </select>
                    <label for="managers_access_criteria_description">Especificação do critério de acesso</label>
                    <input type="text" name="managers_access_criteria_description" id="managers_access_criteria_description" size="62" maxlength="100" class="text">
                    <label for="managers_link_type_id">Tipo de vínculo</label>
                    <select class="select ui-widget-content ui-corner-all" name="managers_link_type_id" id="managers_link_type_id">
                        <option value="">Selecione</option>
                        <option value="1">Concursado/efetivo/estável</option>
                        <option value="2">Contrato temporário</option>
                        <option value="3">Contrato terceirizado</option>
                        <option value="4">Contrato CLT</option>
                    </select>
                    <label for="managers_email">E-mail</label>
                    <input type="text" name="managers_email" id="managers_email" size="62" maxlength="50" class="text">
                </form>
            </div>`;
}

function addEventsManagerInputs() {
    $j.each($j('#managers_access_criteria_id'), function (index, field) {
        field.on('change', function () {
            changeAccessCriteria(this);
        });
        changeAccessCriteria(this);
    });
}

function changeManagerRole(field) {
    let accessCriteria = $j('#managers_access_criteria_id'),
        linkType = $j('#managers_link_type_id');

    if ($j(field).val() == SCHOOL_MANAGER_ROLE.DIRETOR.toString()) {
        accessCriteria.prop('disabled', false);
    } else {
        accessCriteria.prop('disabled', true);
        accessCriteria.val('');
    }

    if ($j(field).val() == SCHOOL_MANAGER_ROLE.DIRETOR.toString() && $j('#dependencia_administrativa').val() != DEPENDENCIA_ADMINISTRATIVA.PRIVADA) {
        linkType.prop('disabled', false);
    } else {
        linkType.prop('disabled', true);
    }

    changeAccessCriteria(accessCriteria)
}

function changeAccessCriteria(field) {
    let accessCriteriaDescription = $j('#managers_access_criteria_description');

    if ($j(field).val() == SCHOOL_MANAGER_ACCESS_CRITERIA.OUTRO.toString()) {
        accessCriteriaDescription.prop('disabled', false);
    } else {
        accessCriteriaDescription.prop('disabled', true);
        accessCriteriaDescription.val('');
    }
}

function validateAccessCriteriaId() {
    if (!obrigarCamposCenso){
        return true;
    }

    if ($j('select[id="managers_role_id[' + idLastLineUsed + ']"]').val() != SCHOOL_MANAGER_ROLE.DIRETOR.toString()) {
        return true;
    }

    if ($j('#managers_access_criteria_id').val() == '') {
        messageUtils.error("O campo: <b>Critério de acesso ao cargo</b> deve ser preenchido quando o campo: <b>Cargo</b> for: <b>Diretor</b>");
        return false;
    }

    return true;
}

function validateAccessCriteriaDescription() {
    if (!obrigarCamposCenso){
        return true;
    }

    if ($j('#managers_access_criteria_id').val() != SCHOOL_MANAGER_ACCESS_CRITERIA.OUTRO.toString()) {
        return true;
    }

    if ($j('#managers_access_criteria_description').val() == '') {
        messageUtils.error("O campo: <b>Especificação do critério de acesso</b> deve ser preenchido quando o campo: <b>Critério de acesso ao cargo</b> for: <b>Outros</b>");
        return false;
    }

    return true;
}

function validateLinkType() {
    if (!obrigarCamposCenso){
        return true;
    }

    if ($j('select[id="managers_role_id[' + idLastLineUsed + ']"]').val() != SCHOOL_MANAGER_ROLE.DIRETOR.toString()) {
        return true;
    }

    if ($j('#dependencia_administrativa').val() == DEPENDENCIA_ADMINISTRATIVA.PRIVADA) {
        return true;
    }

    if ($j('#managers_link_type_id').val() == '') {
        messageUtils.error("O campo: <b>Tipo de vínculo</b> deve ser preenchido quando o campo: <b>Cargo</b> for: <b>Diretor</b> e o campo: <b>Dependência administrativa</b> não for: <b>Privada</b>");
        return false;
    }

    return true;
}