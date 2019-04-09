$j('body').append(htmlFormModal());
$j('#modal_school_managers').find(':input').css('display', 'block');

$j("#modal_school_managers").dialog({
    autoOpen: false,
    height: 'auto',
    width: 'auto',
    modal: true,
    resizable: false,
    draggable: false,
    title: 'Informações adicionais',
    buttons: {
        "Gravar": function () {
            fillHiddenInputs();
            $j(this).dialog("close");
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
    $j("#modal_school_managers").dialog("open");
}

function fillHiddenInputs() {
    let roleId = $j("#managers_role_id").val(),
        accessCriteriaId = $j("#managers_access_criteria_id").val(),
        accessCriteriaIdDescription = $j("#managers_access_criteria_description").val(),
        linkTypeId = $j("#managers_link_type_id").val();

    $j('input[id^="managers_role_id[' + idLastLineUsed + ']').val(roleId);
    $j('input[id^="managers_access_criteria_id[' + idLastLineUsed + ']').val(accessCriteriaId);
    $j('input[id^="managers_access_criteria_description[' + idLastLineUsed + ']').val(accessCriteriaIdDescription);
    $j('input[id^="managers_link_type_id[' + idLastLineUsed + ']').val(linkTypeId);
}

function fillInputs() {
    let roleId = $j('input[id^="managers_role_id[' + idLastLineUsed + ']').val(),
        accessCriteriaId = $j('input[id^="managers_access_criteria_id[' + idLastLineUsed + ']').val(),
        accessCriteriaIdDescription = $j('input[id^="managers_access_criteria_description[' + idLastLineUsed + ']').val(),
        linkTypeId = $j('input[id^="managers_link_type_id[' + idLastLineUsed + ']').val();

    $j("#managers_role_id").val(roleId);
    $j("#managers_access_criteria_id").val(accessCriteriaId);
    $j("#managers_access_criteria_description").val(accessCriteriaIdDescription);
    $j("#managers_link_type_id").val(linkTypeId);
}

function htmlFormModal() {
    return `<div id="modal_school_managers">
                <form>
                    <label for="managers_role_id">Cargo do(a) gestor(a)</label>
                    <select class="select ui-widget-content ui-corner-all" name="managers_role_id" id="managers_role_id">
                        <option value="">Selecione</option>
                        <option value="1">Diretor(a)</option>
                        <option value="2">Outro cargo</option>
                    </select>
                    <label for="managers_access_criteria_id">Critério de acesso ao cargo</label>
                    <select class="select ui-widget-content ui-corner-all" name="managers_access_criteria_id" id="managers_access_criteria_id">
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
                    <input type="text" name="managers_access_criteria_description" id="managers_access_criteria_description" size="49" maxlength="255" class="text">
                    <label for="managers_link_type_id">Tipo de vínculo</label>
                    <select class="select ui-widget-content ui-corner-all" name="managers_link_type_id" id="managers_link_type_id">
                        <option value="">Selecione</option>
                        <option value="1">Concursado/efetivo/estável</option>
                        <option value="2">Contrato temporário</option>
                        <option value="3">Contrato terceirizado</option>
                        <option value="4">Contrato CLT</option>
                    </select>
                </form>
            </div>`;
}