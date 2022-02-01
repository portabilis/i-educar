// links pessoa pai, mãe

var $paiNomeField = $j('#pai_nome');
var $paiIdField   = $j('#pai_id');

var $maeNomeField = $j('#mae_nome');
var $maeIdField   = $j('#mae_id');

var $pessoaPaiActionBar = $j('<span>')
    .html('')
    .addClass('pessoa-links pessoa-pai-links')
    .width($paiNomeField.outerWidth() - 12)
    .appendTo($paiNomeField.parent());

var $pessoaMaeActionBar = $pessoaPaiActionBar.clone()
    .removeClass('pessoa-pai-links')
    .addClass('pessoa-mae-links')
    .appendTo($maeNomeField.parent());

var $linkToCreatePessoaPai = $j('<a>')
    .addClass('cadastrar-pessoa-pai decorated')
    .attr('id', 'cadastrar-pessoa-pai-link')
    .html('Cadastrar pessoa')
    .appendTo($pessoaPaiActionBar);

var $linkToEditPessoaPai = $j('<a>')
    .hide()
    .addClass('editar-pessoa-pai decorated')
    .attr('id', 'editar-pessoa-pai-link')
    .html('Editar pessoa')
    .appendTo($pessoaPaiActionBar);

var $linkToCreatePessoaMae = $linkToCreatePessoaPai.clone()
    .removeClass('cadastrar-pessoa-paii')
    .addClass('cadastrar-pessoa-mae')
    .attr('id', 'cadastrar-pessoa-mae-link')
    .appendTo($pessoaMaeActionBar);

var $linkToEditPessoaMae = $linkToEditPessoaPai.clone()
    .removeClass('editar-pessoa-pai')
    .addClass('editar-pessoa-mae')
    .attr('id', 'editar-pessoa-mae-link')
    .appendTo($pessoaMaeActionBar);

$j("#cadastrar-pessoa-pai-link").click(function () {
    openModalParent('pai');
});

$j("#cadastrar-pessoa-mae-link").click(function () {
    openModalParent('mae');
});

$j("#editar-pessoa-pai-link").click(function () {
    openEditModalParent('pai');
});

$j("#editar-pessoa-mae-link").click(function () {
    openEditModalParent('mae');
});

function openModalParent(parentType) {
    $j('#link_cadastro_detalhado_parent').attr('href', '/intranet/atendidos_cad.php?parent_type=' + parentType);
    $j("#dialog-form-pessoa-parent").dialog("open");
    $j(".ui-widget-overlay").click(function () {
       $j(".ui-dialog-titlebar-close").trigger('click');
    });
    $j('#nome-pessoa-parent').focus();
    $j('#falecido-parent').attr('checked', false);

    var tipoPessoa = 'pai';

    switch (parentType) {
        case 'mae':
            tipoPessoa = 'mãe';
            break;
        case 'responsavel':
            tipoPessoa = 'responsável';
            break;
        default:
            tipoPessoa = 'pai';
    }

    if (parentType == 'responsavel') {
        $j('#falecido-modal').hide();
    } else {
        $j('#falecido-modal').show();
    }

    $j('#dialog-form-pessoa-parent form h2:first-child').html('Cadastrar pessoa ' + tipoPessoa).css('margin-left', '0.75em');

    pessoaPaiOuMae = parentType;
    editar_pessoa = false;
}

function openEditModalParent(parentType) {
    $j('#link_cadastro_detalhado_parent').attr('href', '/intranet/atendidos_cad.php?cod_pessoa_fj=' + $j('#' + parentType + '_id').val() + '&parent_type=' + parentType);
    $j("#dialog-form-pessoa-parent").dialog("open");
    $j(".ui-widget-overlay").click(function () {
        $j(".ui-dialog-titlebar-close").trigger('click');
    });
    $j('#nome-pessoa-parent').focus();

    personDetails = window[parentType + '_details'];

    nameParent.val(personDetails.nome);
    estadocivilParent.val(personDetails.estadocivil);
    sexoParent.val(personDetails.sexo);
    datanascParent.val(personDetails.data_nascimento);
    falecidoParent.prop('checked', (personDetails.falecido));

    $j('#dialog-form-pessoa-parent form h2:first-child').html('Editar pessoa ' + (parentType == 'mae' ? 'mãe' : parentType)).css('margin-left', '0.75em');

    pessoaPaiOuMae = parentType;
    editar_pessoa = true;
}

$j('body').append('<div id="dialog-form-pessoa-parent"><form><h2></h2><table><tr><td valign="top"><fieldset><label for="nome-pessoa-parent">Nome</label>    <input type="text " name="nome-pessoa-parent" id="nome-pessoa-parent" size="49" maxlength="255" class="text">    <label for="sexo-pessoa-parent">Sexo</label>  <select class="select ui-widget-content ui-corner-all" name="sexo-pessoa-parent" id="sexo-pessoa-parent" ><option value="" selected>Sexo</option><option value="M">Masculino</option><option value="F">Feminino</option></select>    <label for="estado-civil-pessoa-parent">Estado civil</label>   <select class="select ui-widget-content ui-corner-all" name="estado-civil-pessoa-parent" id="estado-civil-pessoa-parent"  ><option id="estado-civil-pessoa-parent_" value="" selected>Estado civil</option><option id="estado-civil-pessoa-parent_2" value="2">Casado(a)</option><option id="estado-civil-pessoa-parent_6" value="6">Companheiro(a)</option><option id="estado-civil-pessoa-parent_3" value="3">Divorciado(a)</option><option id="estado-civil-pessoa-parent_4" value="4">Separado(a)</option><option id="estado-civil-pessoa-parent_1" value="1">Solteiro(a)</option><option id="estado-civil-pessoa-parent_5" value="5">Vi&uacute;vo(a)</option><option id="estado-civil-pessoa-parent_7" value="7">Não informado</option></select><label for="data-nasc-pessoa-parent"> Data de nascimento </label> <input onKeyPress="formataData(this, event);" class="" placeholder="dd/mm/yyyy" type="text" name="data-nasc-pessoa-parent" id="data-nasc-pessoa-parent" value="" size="11" maxlength="10"> <div id="falecido-modal"> <label>Falecido?</label><input type="checkbox" name="falecido-parent" id="falecido-parent" style="display:inline;"> </div></fieldset><p><a id="link_cadastro_detalhado_parent">Cadastro detalhado</a></p></form></div>');

$j('#dialog-form-pessoa-parent').find(':input').css('display', 'block');

var nameParent = $j("#nome-pessoa-parent"),
    sexoParent = $j("#sexo-pessoa-parent"),
    estadocivilParent = $j("#estado-civil-pessoa-parent"),
    datanascParent = $j("#data-nasc-pessoa-parent"),
    falecidoParent = $j("#falecido-parent"),
    allFields = $j([]).add(nameParent).add(sexoParent).add(estadocivilParent).add(datanascParent).add(falecidoParent);

$j("#dialog-form-pessoa-parent").dialog({
    autoOpen: false,
    height: 'auto',
    width: 'auto',
    modal: true,
    resizable: false,
    draggable: false,
    buttons: {
        "Gravar": function () {
            var bValid = true;
            allFields.removeClass("ui-state-error");

            bValid = bValid && checkLength(nameParent, "nome", 3, 255);
            bValid = bValid && checkSelect(sexoParent, "sexo");
            bValid = bValid && checkSelect(estadocivilParent, "estado civil");

            if ($j("#data-nasc-pessoa-parent").val() != '') {
                bValid = bValid && checkRegexp(datanascParent, /(^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$)/i, "O campo data de nascimento deve ser preenchido no formato dd/mm/yyyy.");
            }

            if (bValid) {
                postPessoa(
                    $(this),
                    nameParent,
                    nameParent.val(),
                    sexoParent.val(),
                    estadocivilParent.val(),
                    datanascParent.val(),
                    null,
                    (editar_pessoa ? $j('#' + pessoaPaiOuMae + '_id').val() : null),
                    pessoaPaiOuMae,
                    null,
                    null,
                    null,
                    null,
                    falecidoParent.is(':checked')
                );
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
        allFields.val("").removeClass("error");
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

$j('#link_cadastro_detalhado').click(function (e) {
    e.preventDefault();
    windowUtils.open(this.href);
    $j("#dialog-form-pessoa-aluno").dialog("close");
});

$j('#link_cadastro_detalhado_parent').click(function (e) {
    e.preventDefault();
    windowUtils.open(this.href);
    $j("#dialog-form-pessoa-parent").dialog("close");
});

function postPessoa(
    $container,
    $pessoaField,
    nome,
    sexo,
    estadocivil,
    datanasc,
    naturalidade,
    pessoa_id,
    parentType,
    ddd_telefone_1,
    telefone_1,
    ddd_telefone_mov,
    telefone_mov,
    falecido
) {
    var data = {
        nome: nome,
        sexo: sexo,
        estadocivil: estadocivil,
        datanasc: datanasc,
        naturalidade: naturalidade,
        pessoa_id: pessoa_id,
        ddd_telefone_1: ddd_telefone_1,
        telefone_1: telefone_1,
        ddd_telefone_mov: ddd_telefone_mov,
        telefone_mov: telefone_mov,
        falecido: falecido
    };

    var options = {
        url: postResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa', {}),
        dataType: 'json',
        data: data,
        success: function (dataResponse) {
            if (dataResponse['any_error_msg']) {
                dataResponse['msgs'].forEach(msgObject => {
                    messageUtils.error(msgObject['msg']);
                });
            } else {
                $j("#dialog-form-pessoa-parent").dialog('close');

                if (parentType == 'mae') {
                    afterChangePessoaParent(dataResponse.pessoa_id, 'mae');
                }

                else if (parentType == 'pai') {
                    afterChangePessoaParent(dataResponse.pessoa_id, 'pai');
                }
            }
        }
    };

    postResource(options);
}

function checkLength(o, n, min, max) {
    if (o.val().length > max || o.val().length < min) {
        o.addClass("error");

        messageUtils.error("Tamanho do " + n + " deve ter entre " +  min + " e " + max + " caracteres.");
        return false;
    } else {
        return true;
    }
}

function checkSelect(comp, name) {
    if (comp.val() == '') {
        comp.addClass("error");
        messageUtils.error("Selecione um(a) " + name + ".");
        return false;
    } else {
        return true;
    }
}

function afterChangePessoaParent(pessoaId, parentType) {
    $tempField = $paiNomeField;
    var $parente = '';

    switch (parentType) {
        case 'mae':
            $tempField = $maeNomeField;
            $parente = 'mãe';
            break;
        default:
            $tempField = $paiNomeField;
            $parente = 'pai';
    }

    if (editar_pessoa) {
        messageUtils.success('Pessoa ' + $parente + ' alterada com sucesso', $tempField);
    } else {
        messageUtils.success('Pessoa ' + $parente + ' cadastrada com sucesso', $tempField);
    }

    getPersonParentDetails(pessoaId, parentType);

    if ($tempField.is(':active')) {
        $tempField.focus();
    }
}

function checkRegexp(o, regexp, n) {
    if (!( regexp.test(o.val()) )) {
        o.addClass("error");
        messageUtils.error(n);
        return false;
    } else {
        return true;
    }
}

var getPersonParentDetails = function (personId, parentType) {
    var additionalVars = {
        id: personId
    };

    var options = {
        url: getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa-parent', additionalVars),
        dataType: 'json',
        data: {},
        success: function (data) {
            handleGetPersonParentDetails(data, parentType)
        }
    };

    getResource(options);
};

var handleGetPersonParentDetails = function (dataResponse, parentType) {
    window[parentType + '_details'] = dataResponse;

    if (dataResponse.id) {
        if (parentType == 'mae') {
            $maeNomeField.val(dataResponse.id + ' - ' + dataResponse.nome);
            $maeIdField.val(dataResponse.id);
            changeVisibilityOfLinksToPessoaMae();
        } else {
            $paiNomeField.val(dataResponse.id + ' - ' + dataResponse.nome);
            $paiIdField.val(dataResponse.id);
            changeVisibilityOfLinksToPessoaPai();
        }
    }
};

// pessoa links callbacks
var changeVisibilityOfLinksToPessoaParent = function (parentType) {
    var $nomeField = $j(buildId(parentType + '_nome'));
    var $idField = $j(buildId(parentType + '_id'));
    var $linkToEdit = $j('.pessoa-' + parentType + '-links .editar-pessoa-' + parentType);

    if ($nomeField.val() && $idField.val()) {
        $linkToEdit.show().css('display', 'inline');
    } else {
        $nomeField.val('')
        $idField.val('');

        $linkToEdit.hide();
    }
};

$paiNomeField.focusout(changeVisibilityOfLinksToPessoaPai);
$maeNomeField.focusout(changeVisibilityOfLinksToPessoaMae);

var getPersonDetails = function (personId) {
    var additionalVars = {
        id: personId,
    };

    var options = {
        url: getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa', additionalVars),
        dataType: 'json',
        data: {},
        success: handleGetPersonDetails
    };
    getResource(options);
};

var handleGetPersonDetails = function (dataResponse) {
    if (dataResponse.id == $j('#pai_id').val()){
        pai_details = dataResponse;
    } else {
        mae_details = dataResponse;
    }
}

var pai_details = getPersonDetails($j('#pai_id').val());
var mae_details = getPersonDetails($j('#mae_id').val());

$j('#pai_nome').change(function(value) {
    paiId = value.target.value;
    paiId = paiId.split(' ')[0];
    pai_details = getPersonDetails(paiId);
});

$j('#mae_nome').change(function(value) {
    maeId = value.target.value;
    maeId = maeId.split(' ')[0];
    mae_details = getPersonDetails(maeId);
});
