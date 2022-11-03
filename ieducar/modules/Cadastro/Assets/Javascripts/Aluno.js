var url = window.location.href;
var modoCadastro = url.indexOf("id=") == -1;

const aluno_inep_id = $j("#aluno_inep_id");

const ignoreValidation = [
  '000.000.000-00'
];

if (modoCadastro) {
  $j("[name^=tr_historico_altura_peso]").remove();
}

$j("#autorizado_um").closest("tr").show();
$j("#parentesco_um").closest("tr").show();

$j('input[id^="historico_altura"]').mask("0.00", { reverse: true });
$j('input[id^="historico_peso"]').mask("000.00", { reverse: true });

$j("#autorizado_um").change(abriCampoDois);
$j("#autorizado_dois").change(abriCampoTres);
$j("#autorizado_tres").change(abriCampoQuatro);
$j("#autorizado_quatro").change(abriCampoCinco);

function abriCampoDois() {
  $j("#autorizado_dois").closest("tr").show();
  $j("#parentesco_dois").closest("tr").show();
}

function abriCampoTres() {
  $j("#autorizado_tres").closest("tr").show();
  $j("#parentesco_tres").closest("tr").show();
}

function abriCampoQuatro() {
  $j("#autorizado_quatro").closest("tr").show();
  $j("#parentesco_quatro").closest("tr").show();
}

function abriCampoCinco() {
  $j("#autorizado_cinco").closest("tr").show();
  $j("#parentesco_cinco").closest("tr").show();
}

let obrigarCamposCenso = $j("#obrigar_campos_censo").val() == "1";
let obrigarDocumentoPessoa = $j("#obrigar_documento_pessoa").val() == "1";

var editar_pessoa = false;
var person_details;
var pai_details;
var mae_details;

var pessoaPaiOuMae;
var $idField = $j("#id");
var $nomeField = $j("#pessoa_nome");
var $cpfField = $j("#id_federal");

var $resourceNotice = $j("<span>")
  .html("")
  .addClass("error resource-notice")
  .hide()
  .width($nomeField.outerWidth() - 12)
  .insertBefore($idField.parent());

var $pessoaNotice = $resourceNotice.clone().appendTo($nomeField.parent());

var $cpfNotice = $j("<span>")
  .html("")
  .addClass("error resource-notice")
  .hide()
  .width($j("#pessoa_nome").outerWidth() - 12)
  .appendTo($cpfField.parent());

var $loadingLaudoMedico = $j("<img>")
  .attr("src", "imagens/indicator.gif")
  .css("margin-top", "3px")
  .hide()
  .insertBefore($j("#span-laudo_medico"));

var $arrayLaudoMedico = [];
var $arrayUrlLaudoMedico = [];
var $arrayDataLaudoMedico = [];

function excluirLaudoMedico(event) {
  $arrayUrlLaudoMedico.splice(event.data.i - 1, 1);
  $j("#laudo_medico").val("").removeClass("success");
  messageUtils.notice("Laudo médico excluído com sucesso!");
  $j("#laudo" + event.data.i).hide();
  montaUrlLaudoMedico();
}

function laudoMedicoObrigatorio() {
  $j("#laudo_medico").addClass("error");
  messageUtils.error(
    "Deve ser anexado um laudo médico para alunos com deficiências"
  );
}

function addLaudoMedico(url, data) {
  $index = $arrayLaudoMedico.length;
  $id = $index + 1;
  $arrayUrlLaudoMedico[$index] = url;
  $arrayDataLaudoMedico[$index] = data;

  var dataLaudoMedico = "";

  if (data) {
    dataLaudoMedico = " adicionado em " + data;
  }

  $arrayLaudoMedico[$arrayLaudoMedico.length] = $j("<div>")
    .append(
      $j("<span>")
        .html("Laudo " + $id + dataLaudoMedico + ":")
        .attr("id", "laudo" + $id)
        .append(
          $j("<a>")
            .html("Excluir")
            .addClass("decorated")
            .attr("id", "link_excluir_laudo_medico_" + $id)
            .css("cursor", "pointer")
            .css("margin-left", "10px")
            .click({ i: $id }, excluirLaudoMedico)
        )
        .append(
          $j("<a>")
            .html("Visualizar")
            .addClass("decorated")
            .attr("id", "link_visualizar_laudo_medico_" + $id)
            .attr("target", "_blank")
            .attr("href", linkUrlPrivada(url))
            .css("cursor", "pointer")
            .css("margin-left", "10px")
        )
    )
    .insertBefore($j("#laudo_medico"));

  montaUrlLaudoMedico();
}

function montaUrlLaudoMedico() {
  var url = "";

  for (var i = 0; i < $arrayUrlLaudoMedico.length; i++) {
    if ($arrayUrlLaudoMedico[i]) {
      var dataLaudo = "";
      var urlLaudo = $arrayUrlLaudoMedico[i];

      if ($arrayDataLaudoMedico[i]) {
        dataLaudo = '"data" : "' + $arrayDataLaudoMedico[i] + '",';
      }
      url += "{" + dataLaudo + '"url" : "' + urlLaudo + '"},';
    }
  }

  if (url.substring(url.length - 1, url.length) == ",") {
    url = url.substring(0, url.length - 1);
  }

  $j("#url_laudo_medico").val("[" + url + "]");
}

function codigoInepInvalido() {
  aluno_inep_id.addClass("error");
  messageUtils.error("O código INEP do aluno deve conter 12 dígitos.");
}

function certidaoNascimentoInvalida() {
  $j("#certidao_nascimento").addClass("error");
  messageUtils.error(
    "O campo referente a certidão de nascimento deve conter exatos 32 dígitos."
  );
}

function possuiDocumentoObrigatorio() {
  var cpf = $j("#id_federal").val();
  var rg = $j("#rg").val();
  var certidaoCivil =
    $j("#termo_certidao_civil").val() &&
    $j("#folha_certidao_civil").val() &&
    $j("#livro_certidao_civil").val();
  var certidaoNascimentoNovoFormato = $j("#certidao_nascimento").val();
  var certidaoCasamentoNovoFormato = $j("#certidao_casamento").val();

  return (
    cpf ||
    rg ||
    certidaoCivil ||
    certidaoCasamentoNovoFormato ||
    certidaoNascimentoNovoFormato
  );
}

function certidaoCasamentoInvalida() {
  $j("#certidao_casamento").addClass("error");
  messageUtils.error(
    "O campo referente a certidão de casamento deve conter exatos 32 dígitos."
  );
}

var newSubmitForm = function (event) {
  if ($j("#deficiencias").val().length > 1) {
    let laudos = $j("#url_laudo_medico").val();
    let temLaudos = false;

    if (laudos.length > 0) {
      temLaudos = JSON.parse(laudos).length > 0;
    }

    var additionalVars = {
      deficiencias: $j("#deficiencias").val(),
    };

    var options = {
      url: getResourceUrlBuilder.buildUrl(
        "/module/Api/aluno",
        "deve-obrigar-laudo-medico",
        additionalVars
      ),
      dataType: "json",
      data: {},
      success: function (response) {
        if (
          response.result &&
          $j("#url_laudo_medico_obrigatorio").length > 0 &&
          !temLaudos
        ) {
          return laudoMedicoObrigatorio();
        } else {
          return formularioValido();
        }
      },
    };
    getResource(options);
  } else {
    return formularioValido();
  }
};

function handleShowSubmit() {
  $submitButton.removeAttr("disabled").show();
};

function formularioValido() {
  if (obrigarDocumentoPessoa && !possuiDocumentoObrigatorio()) {
    messageUtils.error(
      "É necessário o preenchimento de pelo menos um dos seguintes documentos: CPF, RG ou Certidão civil."
    );
    return false;
  }

  var codigoInep = $j("#aluno_inep_id").val();

  if (codigoInep && codigoInep.length != 12) {
    return codigoInepInvalido();
  }

  var tipoCertidaoNascimento =
    $j("#tipo_certidao_civil").val() == "certidao_nascimento_novo_formato";
  var tipoCertidaoCasamento =
    $j("#tipo_certidao_civil").val() == "certidao_casamento_novo_formato";

  if (
    tipoCertidaoNascimento &&
    $j("#certidao_nascimento").val().length < 32
  ) {
    return certidaoNascimentoInvalida();
  } else if (
    tipoCertidaoCasamento &&
    $j("#certidao_casamento").val().length < 32
  ) {
    return certidaoCasamentoInvalida();
  }

  if (
    $j("#aluno_estado_id").val() !== "" &&
    !(
      $j("#aluno_estado_id").val().length === 13 ||
      $j("#aluno_estado_id").val().length === 11
    )
  ) {
    messageUtils.error(
      "O campo Código rede estadual (RA) deve conter exatos 13 ou 11 dígitos."
    );
    return false;
  }

  $tipoTransporte = $j("#tipo_transporte");

  if ($tipoTransporte.val() != "nenhum") {
    veiculoTransporte = $j("#veiculo_transporte_escolar").val();
    if (
      obrigarCamposCenso &&
      (veiculoTransporte == "" || veiculoTransporte == null)
    ) {
      messageUtils.error("O campo Veículo utilizado deve ser preenchido");
      return false;
    }
  }

  observacoes_aluno = $j("#observacoes_aluno").val();

  if (!validaObrigatoriedadeRecursosTecnologicos()) {
    return false;
  }

  submitFormExterno();
}

function validaObrigatoriedadeRecursosTecnologicos() {
  let obrigarRecursosTecnologicos =
    $j("#obrigar_recursos_tecnologicos").val() == "1";
  let recursosTecnologicos = $j("#recursos_tecnologicos__").val();

  if (Array.isArray(recursosTecnologicos)) {
    recursosTecnologicos = recursosTecnologicos.toString();
  }

  if (obrigarRecursosTecnologicos && !recursosTecnologicos) {
    messageUtils.error(
      "É necessário informar o campo: <strong>Possui acesso à recursos tecnológicos?</strong> da aba: <strong>Moradia</strong>."
    );
    return false;
  }

  return true;
}

var $loadingDocumento = $j("<img>")
  .attr("src", "imagens/indicator.gif")
  .css("margin-top", "3px")
  .hide()
  .insertBefore($j("#span-documento"));

var $arrayDocumento = [];
var $arrayUrlDocumento = [];
var $arrayDataDocumento = [];

function excluirDocumento(event) {
  $arrayUrlDocumento.splice(event.data.i - 1, 1);
  $j("#documento").val("").removeClass("success");
  messageUtils.notice("Documento excluído com sucesso!");
  $j("#documento" + event.data.i).hide();
  montaUrlDocumento();
}

function addDocumento(url, data) {
  $index = $arrayDocumento.length;
  $id = $index + 1;
  $arrayUrlDocumento[$index] = url;
  $arrayDataDocumento[$index] = data;

  var dataDocumento = "";

  if (data) {
    dataDocumento = " adicionado em " + data;
  }

  $arrayDocumento[$arrayDocumento.length] = $j("<div>")
    .append(
      $j("<span>")
        .html("Documento " + $id + dataDocumento + ":")
        .attr("id", "documento" + $id)
        .append(
          $j("<a>")
            .html("Excluir")
            .addClass("decorated")
            .attr("id", "link_excluir_documento_" + $id)
            .css("cursor", "pointer")
            .css("margin-left", "10px")
            .click({ i: $id }, excluirDocumento)
        )
        .append(
          $j("<a>")
            .html("Visualizar")
            .addClass("decorated")
            .attr("id", "link_visualizar_documento_" + $id)
            .attr("target", "_blank")
            .attr("href", linkUrlPrivada(url))
            .css("cursor", "pointer")
            .css("margin-left", "10px")
        )
    )
    .insertBefore($j("#documento"));

  montaUrlDocumento();
}

function montaUrlDocumento() {
  var url = "";

  for (var i = 0; i < $arrayUrlDocumento.length; i++) {
    if ($arrayUrlDocumento[i]) {
      var dataDocumento = "";
      var urlDocumento = $arrayUrlDocumento[i];

      if ($arrayDataDocumento[i]) {
        dataDocumento = '"data" : "' + $arrayDataDocumento[i] + '",';
      }

      url += "{" + dataDocumento + '"url" : "' + urlDocumento + '"},';
    }
  }

  if (url.substring(url.length - 1, url.length) == ",") {
    url = url.substring(0, url.length - 1);
  }

  $j("#url_documento").val("[" + url + "]");
}

var $paiNomeField = $j("#pai_nome");
var $paiIdField = $j("#pai_id");

var $maeNomeField = $j("#mae_nome");
var $maeIdField = $j("#mae_id");

var $responsavelNomeField = $j("#responsavel_nome");
var $responsavelIdField = $j("#responsavel_id");

var $pessoaPaiActionBar = $j("<span>")
  .html("")
  .addClass("pessoa-links pessoa-pai-links")
  .width($paiNomeField.outerWidth() - 12)
  .appendTo($paiNomeField.parent());

var $pessoaMaeActionBar = $pessoaPaiActionBar
  .clone()
  .removeClass("pessoa-pai-links")
  .addClass("pessoa-mae-links")
  .appendTo($maeNomeField.parent());

var $pessoaResponsavelActionBar = $pessoaPaiActionBar
  .clone()
  .removeClass("pessoa-pai-links")
  .addClass("pessoa-responsavel-links")
  .appendTo($responsavelNomeField.parent());

var $linkToCreatePessoaPai = $j("<a>")
  .addClass("cadastrar-pessoa-pai decorated")
  .attr("id", "cadastrar-pessoa-pai-link")
  .html("Cadastrar pessoa")
  .appendTo($pessoaPaiActionBar);

var $linkToEditPessoaPai = $j("<a>")
  .hide()
  .addClass("editar-pessoa-pai decorated")
  .attr("id", "editar-pessoa-pai-link")
  .html("Editar pessoa")
  .appendTo($pessoaPaiActionBar);

var $linkToCreatePessoaMae = $linkToCreatePessoaPai
  .clone()
  .removeClass("cadastrar-pessoa-pai")
  .attr("id", "cadastrar-pessoa-mae-link")
  .addClass("cadastrar-pessoa-mae")
  .appendTo($pessoaMaeActionBar);

var $linkToEditPessoaMae = $linkToEditPessoaPai
  .clone()
  .removeClass("editar-pessoa-pai")
  .addClass("editar-pessoa-mae")
  .attr("id", "editar-pessoa-mae-link")
  .appendTo($pessoaMaeActionBar);

var $linkToCreatePessoaResponsavel = $linkToCreatePessoaPai
  .clone()
  .removeClass("cadastrar-pessoa-pai")
  .attr("id", "cadastrar-pessoa-responsavel-link")
  .addClass("cadastrar-pessoa-responsavel")
  .appendTo($pessoaResponsavelActionBar)
  .css("display", "none");

var $linkToEditPessoaResponsavel = $linkToEditPessoaPai
  .clone()
  .removeClass("editar-pessoa-pai")
  .addClass("editar-pessoa-responsavel")
  .attr("id", "editar-pessoa-responsavel-link")
  .appendTo($pessoaResponsavelActionBar)
  .css("display", "none");

$j(".tableDetalheLinhaSeparador").closest("tr").attr("id", "stop");

$j("td .formdktd:first").append(
  '<div id="tabControl"><ul><li><div id="tab1" class="alunoTab"> <span class="tabText">Dados pessoais</span></div></li><li><div id="tab2" class="alunoTab"> <span class="tabText">Ficha m\u00e9dica</span></div></li><li><div id="tab4" class="alunoTab"> <span class="tabText">Moradia</span></div></li><li><div id="tab5" class="alunoTab" style="width: 125px;"> <span class="tabText" style="">Dados educacenso</span></div></li><li><div id="tab6" class="alunoTab"> <span class="tabText" style="">Projetos</span></div></li></ul></div>'
);

$j("#tab1").addClass("alunoTab-active").removeClass("alunoTab");

$j(".tablecadastro >tbody  > tr").each(function (index, row) {
  if (index > $j("#tr_observacao_aluno").index() - 1) {
    if (row.id != "stop") {
      row.hide();
    } else {
      return false;
    }
  }
});

$j(
  "#restricao_atividade_fisica, #acomp_medico_psicologico, #medicacao_especifica, #tratamento_medico, #doenca_congenita, #alergia_alimento, #alergia_medicamento, #fratura_trauma, #plano_saude, #aceita_hospital_proximo, #vacina_covid"
).addClass("temDescricao");

resourceOptions.handlePost = function (dataResponse) {
  $nomeField.attr("disabled", "disabled");
  $j(".pessoa-links .cadastrar-pessoa").hide();

  if (!dataResponse.any_error_msg) {
    window.setTimeout(function () {
      document.location =
        "/intranet/educar_aluno_det.php?cod_aluno=" + resource.id();
    }, 500);
  } else {
    $submitButton.removeAttr("disabled").val("Gravar");
  }
};

resourceOptions.handlePut = function (dataResponse) {
  if (!dataResponse.any_error_msg) {
    window.setTimeout(function () {
      document.location =
        "/intranet/educar_aluno_det.php?cod_aluno=" + resource.id();
    }, 500);
  } else {
    $submitButton.removeAttr("disabled").val("Gravar");
  }
};

var tipo_resp;

resourceOptions.handleGet = function (dataResponse) {
  handleMessages(dataResponse.msgs);
  $resourceNotice.hide();

  if (dataResponse.id && !dataResponse.ativo) {
    $submitButton.attr("disabled", "disabled").hide();
    $deleteButton.attr("disabled", "disabled").hide();

    var msg =
      "Este cadastro foi desativado em <b>" +
      dataResponse.destroyed_at +
      " </b><br/>pelo usuário <b>" +
      dataResponse.destroyed_by +
      "</b>, ";

    $resourceNotice.html(msg).slideDown("fast");

    $j("<a>")
      .addClass("decorated")
      .attr("href", "#")
      .click(resourceOptions.enable)
      .html("reativar cadastro.")
      .appendTo($resourceNotice);
  } else {
    $deleteButton.removeAttr("disabled").show();
  }

  if (dataResponse.pessoa_id) {
    getPersonDetails(dataResponse.pessoa_id);
  }

  $idField.val(dataResponse.id);

  $beneficios = $j("#beneficios");

  $j.each(dataResponse.beneficios, function (id, nome) {
    $beneficios.children("[value=" + id + "]").attr("selected", "");
  });

  $beneficios.trigger("chosen:updated");

  if (dataResponse.historico_altura_peso.length == 0) {
    $j("[name^=tr_historico_altura_peso]").remove();
  }

  $j.each(dataResponse.historico_altura_peso, function (i, object) {
    if (i > 0) {
      $j("#btn_add_tab_add_1").click();
    }

    $j("#data_historico\\[" + i + "\\]").val(object.data_historico);
    $j("#historico_altura\\[" + i + "\\]").val(object.altura);
    $j("#historico_peso\\[" + i + "\\]").val(object.peso);
  });

  $j.each(dataResponse.projetos, function (i, object) {
    if (i > 0) {
      $j("#btn_add_tab_add_2").click();
    }

    $j("#projeto_cod_projeto\\[" + i + "\\]").val(
      object.projeto_cod_projeto
    );
    $j("#projeto_data_inclusao\\[" + i + "\\]").val(
      object.projeto_data_inclusao
    );
    $j("#projeto_data_desligamento\\[" + i + "\\]").val(
      object.projeto_data_desligamento
    );
    $j("#projeto_turno\\[" + i + "\\]").val(object.projeto_turno);
  });

  aluno_inep_id.val(dataResponse.aluno_inep_id);
  $j("#aluno_estado_id").val(dataResponse.aluno_estado_id);
  $j("#codigo_sistema").val(dataResponse.codigo_sistema);
  tipo_resp = dataResponse.tipo_responsavel;
  $j("#religiao_id").val(dataResponse.religiao_id);
  $j("#tipo_transporte").val(dataResponse.tipo_transporte);
  $j("#alfabetizado").attr("checked", dataResponse.alfabetizado);
  document.getElementById("emancipado").checked = dataResponse.emancipado;
  $j("#autorizado_um").val(dataResponse.autorizado_um);
  $j("#parentesco_um").val(dataResponse.parentesco_um);
  $j("#autorizado_dois").val(dataResponse.autorizado_dois);
  $j("#parentesco_dois").val(dataResponse.parentesco_dois);
  $j("#autorizado_tres").val(dataResponse.autorizado_tres);
  $j("#parentesco_tres").val(dataResponse.parentesco_tres);
  $j("#autorizado_quatro").val(dataResponse.autorizado_quatro);
  $j("#parentesco_quatro").val(dataResponse.parentesco_quatro);
  $j("#autorizado_cinco").val(dataResponse.autorizado_cinco);
  $j("#parentesco_cinco").val(dataResponse.parentesco_cinco);

  if ($j("#autorizado_um").val() == "") {
    $j("#autorizado_dois").closest("tr").hide();
    $j("#autorizado_dois").closest("tr").hide();
  } else {
    $j("#autorizado_dois").closest("tr").show();
    $j("#autorizado_dois").closest("tr").show();
  }

  if ($j("#autorizado_dois").val() == "") {
    $j("#autorizado_tres").closest("tr").hide();
    $j("#autorizado_tres").closest("tr").hide();
  } else {
    $j("#autorizado_tres").closest("tr").show();
    $j("#autorizado_tres").closest("tr").show();
  }

  if ($j("#autorizado_tres").val() == "") {
    $j("#autorizado_quatro").closest("tr").hide();
    $j("#autorizado_quatro").closest("tr").hide();
  } else {
    $j("#autorizado_quatro").closest("tr").show();
    $j("#autorizado_quatro").closest("tr").show();
  }

  if ($j("#autorizado_quatro").val() == "") {
    $j("#autorizado_cinco").closest("tr").hide();
    $j("#parentesco_cinco").closest("tr").hide();
  } else {
    $j("#parentesco_cinco").closest("tr").show();
    $j("#parentesco_cinco").closest("tr").show();
  }

  if (dataResponse.url_laudo_medico) {
    var arrayLaudo = JSON.parse(dataResponse.url_laudo_medico);

    for (var i = 0; i < arrayLaudo.length; i++) {
      addLaudoMedico(arrayLaudo[i].url, arrayLaudo[i].data);
    }
  }

  if (dataResponse.url_documento) {
    var arrayDocumento = JSON.parse(dataResponse.url_documento);

    for (var i = 0; i < arrayDocumento.length; i++) {
      addDocumento(arrayDocumento[i].url, arrayDocumento[i].data);
    }
  }

  $j("#sus").val(dataResponse.sus);

  if (dataResponse.alergia_medicamento == "S") {
    $j("#alergia_medicamento").attr("checked", true);
    $j("#alergia_medicamento").val("on");
  }

  if (dataResponse.alergia_alimento == "S") {
    $j("#alergia_alimento").attr("checked", true);
    $j("#alergia_alimento").val("on");
  }

  if (dataResponse.doenca_congenita == "S") {
    $j("#doenca_congenita").attr("checked", true);
    $j("#doenca_congenita").val("on");
  }

  if (dataResponse.fumante == "S") {
    $j("#fumante").attr("checked", true);
    $j("#fumante").val("on");
  }

  if (dataResponse.doenca_caxumba == "S") {
    $j("#doenca_caxumba").attr("checked", true);
    $j("#doenca_caxumba").val("on");
  }

  if (dataResponse.doenca_sarampo == "S") {
    $j("#doenca_sarampo").attr("checked", true);
    $j("#doenca_sarampo").val("on");
  }

  if (dataResponse.doenca_rubeola == "S") {
    $j("#doenca_rubeola").attr("checked", true);
    $j("#doenca_rubeola").val("on");
  }

  if (dataResponse.doenca_catapora == "S") {
    $j("#doenca_catapora").attr("checked", true);
    $j("#doenca_catapora").val("on");
  }

  if (dataResponse.doenca_escarlatina == "S") {
    $j("#doenca_escarlatina").attr("checked", true);
    $j("#doenca_escarlatina").val("on");
  }

  if (dataResponse.doenca_coqueluche == "S") {
    $j("#doenca_coqueluche").attr("checked", true);
    $j("#doenca_coqueluche").val("on");
  }

  if (dataResponse.epiletico == "S") {
    $j("#epiletico").attr("checked", true);
    $j("#epiletico").val("on");
  }

  if (dataResponse.epiletico_tratamento == "S") {
    $j("#epiletico_tratamento").attr("checked", true);
    $j("#epiletico_tratamento").val("on");
  }

  if (dataResponse.hemofilico == "S") {
    $j("#hemofilico").attr("checked", true);
    $j("#hemofilico").val("on");
  }

  if (dataResponse.hipertenso == "S") {
    $j("#hipertenso").attr("checked", true);
    $j("#hipertenso").val("on");
  }

  if (dataResponse.asmatico == "S") {
    $j("#asmatico").attr("checked", true);
    $j("#asmatico").val("on");
  }

  if (dataResponse.diabetico == "S") {
    $j("#diabetico").attr("checked", true);
    $j("#diabetico").val("on");
  }

  if (dataResponse.insulina == "S") {
    $j("#insulina").attr("checked", true);
    $j("#insulina").val("on");
  }

  if (dataResponse.tratamento_medico == "S") {
    $j("#tratamento_medico").attr("checked", true);
    $j("#tratamento_medico").val("on");
  }

  if (dataResponse.medicacao_especifica == "S") {
    $j("#medicacao_especifica").attr("checked", true);
    $j("#medicacao_especifica").val("on");
  }

  if (dataResponse.acomp_medico_psicologico == "S") {
    $j("#acomp_medico_psicologico").attr("checked", true);
    $j("#acomp_medico_psicologico").val("on");
  }

  if (dataResponse.restricao_atividade_fisica == "S") {
    $j("#restricao_atividade_fisica").attr("checked", true);
    $j("#restricao_atividade_fisica").val("on");
  }

  if (dataResponse.fratura_trauma == "S") {
    $j("#fratura_trauma").attr("checked", true);
    $j("#fratura_trauma").val("on");
  }

  if (dataResponse.plano_saude == "S") {
    $j("#plano_saude").attr("checked", true);
    $j("#plano_saude").val("on");
  }

  if (dataResponse.aceita_hospital_proximo == "S") {
    $j("#aceita_hospital_proximo").attr("checked", true);
    $j("#aceita_hospital_proximo").val("on");
  }

  if (dataResponse.vacina_covid == "S") {
    $j("#vacina_covid").attr("checked", true);
    $j("#vacina_covid").val("on");
  }
  $j("#desc_vacina_covid").val(dataResponse.desc_vacina_covid);
  $j("#altura").val(dataResponse.altura);
  $j("#peso").val(dataResponse.peso);
  $j("#grupo_sanguineo").val(dataResponse.grupo_sanguineo);
  $j("#fator_rh").val(dataResponse.fator_rh);
  $j("#desc_alergia_medicamento").val(dataResponse.desc_alergia_medicamento);
  $j("#desc_alergia_alimento").val(dataResponse.desc_alergia_alimento);
  $j("#desc_doenca_congenita").val(dataResponse.desc_doenca_congenita);
  $j("#doenca_outras").val(dataResponse.doenca_outras);
  $j("#desc_tratamento_medico").val(dataResponse.desc_tratamento_medico);
  $j("#desc_medicacao_especifica").val(
    dataResponse.desc_medicacao_especifica
  );
  $j("#desc_acomp_medico_psicologico").val(
    dataResponse.desc_acomp_medico_psicologico
  );
  $j("#desc_restricao_atividade_fisica").val(
    dataResponse.desc_restricao_atividade_fisica
  );
  $j("#desc_fratura_trauma").val(dataResponse.desc_fratura_trauma);
  $j("#desc_plano_saude").val(dataResponse.desc_plano_saude);
  $j("#desc_aceita_hospital_proximo").val(
    dataResponse.desc_aceita_hospital_proximo
  );


  $j("#responsavel").val(dataResponse.responsavel);
  $j("#responsavel_parentesco").val(dataResponse.responsavel_parentesco);
  $j("#responsavel_parentesco_telefone").val(
    dataResponse.responsavel_parentesco_telefone
  );
  $j("#responsavel_parentesco_celular").val(
    dataResponse.responsavel_parentesco_celular
  );

  /***********************************************
   CAMPOS DA MORADIA
   ************************************************/

  if (dataResponse.empregada_domestica == "S") {
    $j("#empregada_domestica").attr("checked", true);
    $j("#empregada_domestica").val("on");
  }

  if (dataResponse.automovel == "S") {
    $j("#automovel").attr("checked", true);
    $j("#automovel").val("on");
  }

  if (dataResponse.motocicleta == "S") {
    $j("#motocicleta").attr("checked", true);
    $j("#motocicleta").val("on");
  }

  if (dataResponse.geladeira == "S") {
    $j("#geladeira").attr("checked", true);
    $j("#geladeira").val("on");
  }

  if (dataResponse.fogao == "S") {
    $j("#fogao").attr("checked", true);
    $j("#fogao").val("on");
  }

  if (dataResponse.maquina_lavar == "S") {
    $j("#maquina_lavar").attr("checked", true);
    $j("#maquina_lavar").val("on");
  }

  if (dataResponse.microondas == "S") {
    $j("#microondas").attr("checked", true);
    $j("#microondas").val("on");
  }

  if (dataResponse.video_dvd == "S") {
    $j("#video_dvd").attr("checked", true);
    $j("#video_dvd").val("on");
  }

  if (dataResponse.televisao == "S") {
    $j("#televisao").attr("checked", true);
    $j("#televisao").val("on");
  }

  if (dataResponse.ddd_telefone == "S") {
    $j("#ddd_telefone").attr("checked", true);
    $j("#ddd_telefone").val("on");
  }

  if (dataResponse.telefone == "S") {
    $j("#telefone").attr("checked", true);
    $j("#telefone").val("on");
  }

  if (dataResponse.ddd_celular == "S") {
    $j("#ddd_celular").attr("checked", true);
    $j("#ddd_celular").val("on");
  }

  if (dataResponse.agua_encanada == "S") {
    $j("#agua_encanada").attr("checked", true);
    $j("#agua_encanada").val("on");
  }

  if (dataResponse.poco == "S") {
    $j("#poco").attr("checked", true);
    $j("#poco").val("on");
  }

  if (dataResponse.energia == "S") {
    $j("#energia").attr("checked", true);
    $j("#energia").val("on");
  }

  if (dataResponse.esgoto == "S") {
    $j("#esgoto").attr("checked", true);
    $j("#esgoto").val("on");
  }

  if (dataResponse.fossa == "S") {
    $j("#fossa").attr("checked", true);
    $j("#fossa").val("on");
  }

  if (dataResponse.lixo == "S") {
    $j("#lixo").attr("checked", true);
    $j("#lixo").val("on");
  }

  /**************
   PROVA INEP
   ***************/

  if (dataResponse.recursos_prova_inep) {
    var recursosProvaInep = dataResponse.recursos_prova_inep;
    recursosProvaInep = recursosProvaInep.replace(/{|}/gi, "");
    recursosProvaInep = recursosProvaInep.split(",");
    $j("#recursos_prova_inep__").val(recursosProvaInep);
    $j("#recursos_prova_inep__").trigger("chosen:updated");
  }

  $j("#recebe_escolarizacao_em_outro_espaco")
    .val(dataResponse.recebe_escolarizacao_em_outro_espaco)
    .change();

  $j("#quartos").val(dataResponse.quartos);
  $j("#sala").val(dataResponse.sala);
  $j("#copa").val(dataResponse.copa);
  $j("#banheiro").val(dataResponse.banheiro);
  $j("#garagem").val(dataResponse.garagem);
  $j("#casa_outra").val(dataResponse.casa_outra);
  $j("#quant_pessoas").val(dataResponse.quant_pessoas);
  $j("#renda").val(dataResponse.renda);
  $j("#moradia").val(dataResponse.moradia).change();
  $j("#material").val(dataResponse.material).change();
  $j("#moradia_situacao").val(dataResponse.moradia_situacao).change();

  if (dataResponse.recursos_tecnologicos) {
    var recursosTecnologicos = JSON.parse(
      dataResponse.recursos_tecnologicos
    );
    $j("#recursos_tecnologicos__").val(recursosTecnologicos);
    $j("#recursos_tecnologicos__").trigger("chosen:updated");
  }

  $j("#justificativa_falta_documentacao")
    .val(dataResponse.justificativa_falta_documentacao)
    .change();

  if ($j("#transporte_rota").length > 0) {
    valPonto = dataResponse.ref_cod_ponto_transporte_escolar;
    $j("#transporte_rota").val(
      dataResponse.ref_cod_rota_transporte_escolar
    );
    chamaGetPonto();

    $j("#transporte_observacao").val(dataResponse.observacao);

    if (dataResponse.ref_idpes_destino) {
      $j("#pessoaj_transporte_destino").val(
        dataResponse.ref_idpes_destino +
        " - " +
        dataResponse.nome_destino
      );
      $j("#pessoaj_id").val(dataResponse.ref_idpes_destino);
    }
  }

  camposTransporte();

  setTimeout(function () {
    $veiculo_transporte_escolar = $j("#veiculo_transporte_escolar");
    $veiculo_transporte_escolar.val(
      dataResponse.veiculo_transporte_escolar
    );
    $veiculo_transporte_escolar.trigger("chosen:updated");
  }, 550);

  verificaObrigatoriedadeRg();
};

var changeVisibilityOfLinksToPessoaParent = function (parentType) {
  var $nomeField = $j(buildId(parentType + "_nome"));
  var $idField = $j(buildId(parentType + "_id"));
  var $linkToEdit = $j(
    ".pessoa-" + parentType + "-links .editar-pessoa-" + parentType
  );

  if ($nomeField.val() && $idField.val()) {
    $linkToEdit.show().css("display", "inline");
  } else {
    $nomeField.val("");
    $idField.val("");

    $linkToEdit.hide();
  }
};

var changeVisibilityOfLinksToPessoaPai = function () {
  changeVisibilityOfLinksToPessoaParent("pai");
};

var changeVisibilityOfLinksToPessoaMae = function () {
  changeVisibilityOfLinksToPessoaParent("mae");
};

var changeVisibilityOfLinksToPessoaResponsavel = function () {
  changeVisibilityOfLinksToPessoaParent("responsavel");
};

var simpleSearchPaiOptions = {
  autocompleteOptions: { close: changeVisibilityOfLinksToPessoaPai },
};

var simpleSearchMaeOptions = {
  autocompleteOptions: { close: changeVisibilityOfLinksToPessoaMae },
};

var simpleSearchResponsavelOptions = {
  autocompleteOptions: { close: changeVisibilityOfLinksToPessoaResponsavel },
};

$paiIdField.change(changeVisibilityOfLinksToPessoaPai);
$maeIdField.change(changeVisibilityOfLinksToPessoaMae);
$responsavelIdField.change(changeVisibilityOfLinksToPessoaResponsavel);

var checkJustificativa = function () {
  var certidaoNascimento = $j("#certidao_nascimento").val().trim();
  var nisPisPasep = $j("#nis_pis_pasep").val().trim();
  var cpf = $j("#id_federal").val().trim();

  if (certidaoNascimento || nisPisPasep || cpf) {
    disableJustificativaFields();
  } else {
    enableJustificativaFields();
  }
};

$j("#certidao_nascimento").on("change", checkJustificativa);
$j("#nis_pis_pasep").on("change", checkJustificativa);
$j("#id_federal").on("change", checkJustificativa);

let verificaCampoZonaResidencia = () => {
  let $field = $j("#zona_localizacao_censo");
  let isBrasil = $j("#pais_residencia").val() == "76";
  if (isBrasil) {
    $field.removeAttr("disabled");

    if (obrigarCamposCenso) {
      $field.makeRequired();
    }
  } else {
    $field.val("");
    $field.makeUnrequired();
    $field.attr("disabled", "disabled");
  }
};

$j("#pais_residencia").change(verificaCampoZonaResidencia);

var handleGetPersonDetails = function (dataResponse) {
  handleMessages(dataResponse.msgs);
  $pessoaNotice.hide();

  person_details = dataResponse;

  mae_details = dataResponse.mae_details;

  

  pai_details = dataResponse.pai_details;

  var alunoId = dataResponse.aluno_id;

  if (alunoId && alunoId != resource.id()) {
    $submitButton.attr("disabled", "disabled").hide();

    $pessoaNotice
      .html(
        "Esta pessoa já possui o aluno código " +
        alunoId +
        " cadastrado, "
      )
      .slideDown("fast");

    $j("<a>")
      .addClass("decorated")
      .attr("href", resource.url(alunoId))
      .attr("target", "_blank")
      .html("acessar cadastro.")
      .appendTo($pessoaNotice);
  } else {
    $j(".pessoa-links .editar-pessoa").show().css("display", "inline");

    handleShowSubmit();
  }

  $j("#pessoa_id").val(dataResponse.id);
  var nameFull = dataResponse.id + " - " + dataResponse.nome;

  if (dataResponse.nome_social) {
    nameFull =
      dataResponse.id +
      " - " +
      dataResponse.nome_social +
      " - Nome de registro: " +
      dataResponse.nome;
  }

  $nomeField.val(nameFull);

  var nomePai = dataResponse.nome_pai;
  var nomeMae = dataResponse.nome_mae;
  var nomeResponsavel = dataResponse.nome_responsavel;

  if (dataResponse.pai_id) {
    pai_details.nome = nomePai;
    $j("#pai_nome").val(dataResponse.pai_id + " - " + nomePai);
    $j("#pai_id").val(dataResponse.pai_id);
  } else {
    $j("#pai_nome").val("");
    $j("#pai_id").val("");
  }

  $j("#pai_id").trigger("change");

  if (dataResponse.mae_id) {
    mae_details.nome = nomeMae;
    $j("#mae_nome").val(dataResponse.mae_id + " - " + nomeMae);
    $j("#mae_id").val(dataResponse.mae_id);
  } else {
    $j("#mae_nome").val("");
    $j("#mae_id").val("");
  }

  $j("#mae_id").trigger("change");

  if (dataResponse.responsavel_id) {

    $j("#responsavel_nome").val(
      dataResponse.responsavel_id + " - " + nomeResponsavel
    );
    $j("#responsavel_id").val(dataResponse.responsavel_id);
  } else {
    $j("#responsavel_nome").val("");
    $j("#responsavel_id").val("");
  }

  $j("#responsavel_id").trigger("change");

  if (dataResponse.responsavel_id) {
    nomeResponsavel = dataResponse.responsavel_id + " - " + nomeResponsavel;
  }

  $j("#data_nascimento").val(dataResponse.data_nascimento);
  $j("#rg").val(dataResponse.rg);

  $j("#orgao_emissao_rg").val(dataResponse.orgao_emissao_rg);
  $j("#uf_emissao_rg").val(dataResponse.uf_emissao_rg);

  $j("#responsavel_nome").val(nomeResponsavel);
  $j("#responsavel_id").val(dataResponse.responsavel_id);

  $j("#religiao_id").val(dataResponse.religiao_id);

  $deficiencias = $j("#deficiencias");

  $j.each(dataResponse.deficiencias, function (id, nome) {
    $deficiencias.children("[value=" + id + "]").attr("selected", "");
  });

  $deficiencias.trigger("chosen:updated");

  function habilitaRecursosProvaInep() {
    var deficiencias = $j("#deficiencias").val();

    var additionalVars = {
      deficiencias: deficiencias,
    };

    var options = {
      url: getResourceUrlBuilder.buildUrl(
        "/module/Api/aluno",
        "deve-habilitar-campo-recursos-prova-inep",
        additionalVars
      ),
      dataType: "json",
      data: {},
      success: function (response) {
        if (response.result) {
          $j("#recursos_prova_inep__")
            .prop("disabled", false)
            .trigger("chosen:updated");
        } else {
          $j("#recursos_prova_inep__")
            .prop("disabled", true)
            .val([])
            .trigger("chosen:updated");
        }
      },
    };
    getResource(options);
  }

  habilitaRecursosProvaInep();

  $j("#deficiencias").on("change", habilitaRecursosProvaInep);

  $j("#tipo_responsavel").find("option").remove().end();

  if ($j("#pai").val() == "" && $j("#mae").val() == "") {
    $j("#tipo_responsavel").append(
      '<option value="outra_pessoa" selected >Outra pessoa</option>'
    );
    $j("#responsavel_nome").show();
    $j("#cadastrar-pessoa-responsavel-link").show();
  } else if ($j("#pai").val() == "") {
    $j("#tipo_responsavel").append(
      '<option value="mae" selected >M&atilde;e</option>'
    );
    $j("#tipo_responsavel").append(
      '<option value="outra_pessoa" >Outra pessoa</option>'
    );
  } else if ($j("#mae").val() == "") {
    $j("#tipo_responsavel").append(
      '<option value="pai" selected >Pai</option>'
    );
    $j("#tipo_responsavel").append(
      '<option value="outra_pessoa" >Outra pessoa</option>'
    );
  } else {
    $j("#tipo_responsavel").append(
      '<option value="mae" selected >M&atilde;e</option>'
    );
    $j("#tipo_responsavel").append(
      '<option value="pai" selected >Pai</option>'
    );
    $j("#tipo_responsavel").append(
      '<option value="pai_mae" >Pai e M&atilde;e</option>'
    );
    $j("#tipo_responsavel").append(
      '<option value="outra_pessoa" >Outra pessoa</option>'
    );
  }

  verificaCampoZonaResidencia();

  $j("#tipo_responsavel").val(tipo_resp).change();

  if (dataResponse.possui_documento) {
    disableJustificativaFields();
  } else {
    enableJustificativaFields();
  }

  $j("#certidao_nascimento").val(dataResponse.certidao_nascimento);
  $j("#certidao_casamento").val(dataResponse.certidao_casamento);
  $j("#termo_certidao_civil").val(dataResponse.num_termo);
  $j("#livro_certidao_civil").val(dataResponse.num_livro);
  $j("#folha_certidao_civil").val(dataResponse.num_folha);

  if (
    dataResponse.certidao_nascimento != null &&
    dataResponse.certidao_nascimento.trim()
  ) {
    $j("#tipo_certidao_civil")
      .val("certidao_nascimento_novo_formato")
      .change();
  } else if (
    dataResponse.certidao_casamento != null &&
    dataResponse.certidao_casamento.trim()
  ) {
    $j("#tipo_certidao_civil")
      .val("certidao_casamento_novo_formato")
      .change();
  } else {
    $j("#tipo_certidao_civil").val(dataResponse.tipo_cert_civil).change();
  }

  var cpf = dataResponse.cpf;
  $j("#nis_pis_pasep").val(dataResponse.nis_pis_pasep);

  var mascara = null;

  if (cpf) {
    mascara = cpf.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
  }

  $j("#id_federal").val(mascara);
  $j("#data_emissao_rg").val(dataResponse.data_emissao_rg);

  $j("#uf_emissao_certidao_civil").val(dataResponse.sigla_uf_cert_civil);
  $j("#data_emissao_certidao_civil").val(
    dataResponse.data_emissao_cert_civil
  );
  $j("#cartorio_emissao_certidao_civil").val(
    dataResponse.cartorio_cert_civil
  );
  checkJustificativa();

  canShowParentsFields();
};

var checkTipoCertidaoCivil = function () {
  var $certidaoCivilFields = $j(
    "#termo_certidao_civil, #livro_certidao_civil, #folha_certidao_civil"
  ).hide();
  var $certidaoNascimentoField = $j("#certidao_nascimento").hide();
  var $certidaoCasamentoField = $j("#certidao_casamento").hide();
  var tipoCertidaoCivil = $j("#tipo_certidao_civil").val();

  $certidaoCivilFields.makeUnrequired();
  $certidaoNascimentoField.makeUnrequired();
  $certidaoCasamentoField.makeUnrequired();

  $j("#uf_emissao_certidao_civil").makeUnrequired();
  $j("#data_emissao_certidao_civil").makeUnrequired();

  if ($j.inArray(tipoCertidaoCivil, ["91", "92"]) > -1) {
    $certidaoCivilFields.show();
    if (obrigarCamposCenso) {
      $j("#uf_emissao_certidao_civil").makeRequired();
      $j("#data_emissao_certidao_civil").makeRequired();
      $certidaoCivilFields.makeRequired();
    }
    $j("#tr_tipo_certidao_civil td:first span").html("Tipo certidão civil");
  } else if (tipoCertidaoCivil == "certidao_nascimento_novo_formato") {
    if (obrigarCamposCenso) {
      $certidaoNascimentoField.makeRequired();
    }
    $certidaoNascimentoField.show();
    $j("#tr_tipo_certidao_civil td:first span").html("Tipo certidão civil");
  } else if (tipoCertidaoCivil == "certidao_casamento_novo_formato") {
    if (obrigarCamposCenso) {
      $certidaoCasamentoField.makeRequired();
    }
    $certidaoCasamentoField.show();
    $j("#tr_tipo_certidao_civil td:first span").html("Tipo certidão civil");
  }

  $j("#tipo_certidao_civil").makeUnrequired();

  if (tipoCertidaoCivil.length && obrigarCamposCenso) {
    $j("#tipo_certidao_civil").makeRequired();
  }
};

function disableJustificativaFields() {
  $jField = $j("#justificativa_falta_documentacao");
  $jField.attr("disabled", "disabled");
}

function enableJustificativaFields() {
  $jField = $j("#justificativa_falta_documentacao");
  $jField.removeAttr("disabled");
}

var handleGetPersonParentDetails = function (dataResponse, parentType) {
  window[parentType + "_details"] = dataResponse;

  if (dataResponse.id) {
    if (parentType == "mae") {
      $maeNomeField.val(dataResponse.id + " - " + dataResponse.nome);
      $maeIdField.val(dataResponse.id);
      changeVisibilityOfLinksToPessoaMae();
    } else if (parentType == "responsavel") {
      $responsavelNomeField.val(
        dataResponse.id + " - " + dataResponse.nome
      );
      $responsavelIdField.val(dataResponse.id);
      changeVisibilityOfLinksToPessoaResponsavel();
    } else {
      $paiNomeField.val(dataResponse.id + " - " + dataResponse.nome);
      $paiIdField.val(dataResponse.id);
      changeVisibilityOfLinksToPessoaPai();
    }
  }
};

checkTipoCertidaoCivil();
$j("#tipo_certidao_civil").change(checkTipoCertidaoCivil);

var getPersonDetails = function (personId) {
  var additionalVars = {
    id: personId,
  };

  var options = {
    url: getResourceUrlBuilder.buildUrl(
      "/module/Api/pessoa",
      "pessoa",
      additionalVars
    ),
    dataType: "json",
    data: {},
    success: handleGetPersonDetails,
  };
  getResource(options);
};

var getPersonParentDetails = function (personId, parentType) {
  var additionalVars = {
    id: personId,
  };

  var options = {
    url: getResourceUrlBuilder.buildUrl(
      "/module/Api/pessoa",
      "pessoa-parent",
      additionalVars
    ),
    dataType: "json",
    data: {},
    success: function (data) {
      handleGetPersonParentDetails(data, parentType);
    },
  };

  getResource(options);
};

var updatePersonDetails = function () {
  canShowParentsFields();

  if ($j("#pessoa_nome").val() && $j("#pessoa_id").val()) {
    getPersonDetails($j("#pessoa_id").val());
  } else {
    clearPersonDetails();
  }
};

if (
  $j("#person").val() &&
  !$j("#pessoa_nome").val() &&
  !$j("#pessoa_id").val()
) {
  getPersonDetails($j("#person").val());
}

var clearPersonDetails = function () {
  $j("#pessoa_id").val("");
  $j("#pai").val("");
  $j("#mae").val("");
  $j("#responsavel").val("");
  $j(".pessoa-links .editar-pessoa").hide();
};

var simpleSearchPessoaOptions = {
  autocompleteOptions: { close: updatePersonDetails },
};

function pegaDominio() {
  var url = location.href;
  url = url.split("/");
  return url[2];
}

function afterChangePessoa(targetWindow, parentType, parentId, parentName) {
  if (targetWindow != null) {
    targetWindow.close();

    if (parentType == null) {
      dominio = pegaDominio();
      url = $j("#id").val()
        ? location.origin +
        "/module/Cadastro/aluno?id=" +
        $j("#id").val()
        : location.origin + "/module/Cadastro/aluno?person=" + parentId;
      setTimeout("document.location = url", 5);
    }
  }

  var $tempIdField;
  var $tempNomeField;

  if (parentType) {
    $tempIdField = $j(buildId(parentType + "_id"));
    $tempNomeField = $j(buildId(parentType + "_nome"));
  } else {
    $tempIdField = $j("pessoa_id");
    $tempNomeField = $nomeField;
  }

  if (targetWindow == null || parentType != null) {
    window.setTimeout(function () {
      messageUtils.success("Pessoa alterada com sucesso", $tempNomeField);

      $tempIdField.val(parentId);

      if (!parentType) {
        getPersonDetails(parentId);
      } else {
        $tempNomeField.val(parentId + " - " + parentName);
      }

      if ($tempNomeField.is(":active")) {
        $tempNomeField.focus();
      }

      changeVisibilityOfLinksToPessoaParent(parentType);
    }, 500);
  }
}

function afterChangePessoaParent(pessoaId, parentType) {
  $tempField = $paiNomeField;
  var $parente = "";

  switch (parentType) {
    case "mae":
      $tempField = $maeNomeField;
      $parente = "m\u00e3e";
      break;
    case "responsavel":
      $tempField = $responsavelNomeField;
      $parente = "respons\u00e1vel";
      break;
    default:
      $tempField = $paiNomeField;
      $parente = "pai";
  }

  if (editar_pessoa) {
    messageUtils.success(
      "Pessoa " + $parente + " alterada com sucesso",
      $tempField
    );
  } else {
    messageUtils.success(
      "Pessoa " + $parente + " cadastrada com sucesso",
      $tempField
    );
  }

  getPersonParentDetails(pessoaId, parentType);

  if ($tempField.is(":active")) {
    $tempField.focus();
  }
}

function canShowParentsFields() {
  if ($j("#pessoa_id").val()) {
    $paiNomeField.removeAttr("disabled");
    $maeNomeField.removeAttr("disabled");
  } else {
    $paiNomeField.attr("disabled", "true");
    $maeNomeField.attr("disabled", "true");
  }
}

(function ($) {
  $(document).ready(function () {
    function currentDate() {
      var today = new Date();
      var dd = today.getDate();
      var mm = today.getMonth() + 1;
      var yyyy = today.getFullYear();

      if (dd < 10) {
        dd = "0" + dd;
      }

      if (mm < 10) {
        mm = "0" + mm;
      }

      return dd + "/" + mm + "/" + yyyy;
    }

    $j("#laudo_medico").on("change", prepareUpload);

    $j("#documento").on("change", prepareUploadDocumento);

    $j("#deficiencias").trigger("chosen:updated");

    function prepareUpload(event) {
      $j("#laudo_medico").removeClass("error");
      uploadFiles(event.target.files);
    }

    function prepareUploadDocumento(event) {
      $j("#documento").removeClass("error");
      uploadFilesDocumento(event.target.files);
    }

    function uploadFiles(files) {
      if (files && files.length > 0) {
        $j("#laudo_medico").attr("disabled", "disabled");
        $j("#btn_enviar")
          .attr("disabled", "disabled")
          .val("Aguarde...");
        $loadingLaudoMedico.show();
        messageUtils.notice("Carregando laudo médico...");

        var data = new FormData();
        $j.each(files, function (key, value) {
          data.append(key, value);
        });

        $j.ajax({
          url: "/intranet/upload.php?files",
          type: "POST",
          data: data,
          cache: false,
          dataType: "json",
          processData: false,
          contentType: false,
          success: function (dataResponse) {
            if (dataResponse.error) {
              $j("#laudo_medico").val("").addClass("error");
              messageUtils.error(dataResponse.error);
            } else {
              messageUtils.success(
                "Laudo médico carregado com sucesso"
              );
              $j("#laudo_medico").addClass("success");
              addLaudoMedico(
                dataResponse.file_url,
                currentDate()
              );
            }
          },
          error: function () {
            $j("#laudo_medico").val("").addClass("error");
            messageUtils.error("Não foi possível enviar o arquivo");
          },
          complete: function () {
            $j("#laudo_medico").removeAttr("disabled");
            $loadingLaudoMedico.hide();
            $j("#btn_enviar").removeAttr("disabled").val("Gravar");
          },
        });
      }
    }

    function uploadFilesDocumento(files) {
      if (files && files.length > 0) {
        $j("#documento").attr("disabled", "disabled");
        $j("#btn_enviar")
          .attr("disabled", "disabled")
          .val("Aguarde...");
        $loadingDocumento.show();
        messageUtils.notice("Carregando documento...");

        var data = new FormData();
        $j.each(files, function (key, value) {
          data.append(key, value);
        });

        $j.ajax({
          url: "/intranet/upload.php?files",
          type: "POST",
          data: data,
          cache: false,
          dataType: "json",
          processData: false,
          contentType: false,
          success: function (dataResponse) {
            if (dataResponse.error) {
              $j("#documento").val("").addClass("error");
              messageUtils.error(dataResponse.error);
            } else {
              messageUtils.success(
                "Documento carregado com sucesso"
              );
              $j("#documento").addClass("success");
              addDocumento(dataResponse.file_url, currentDate());
            }
          },
          error: function () {
            $j("#documento").val("").addClass("error");
            messageUtils.error("Não foi possível enviar o arquivo");
          },
          complete: function () {
            $j("#documento").removeAttr("disabled");
            $loadingDocumento.hide();
            $j("#btn_enviar").removeAttr("disabled").val("Gravar");
          },
        });
      }
    }

    canShowParentsFields();

    var $pessoaActionBar = $j("<span>")
      .html("")
      .addClass("pessoa-links")
      .width($nomeField.outerWidth() - 12)
      .appendTo($nomeField.parent());

    $j("<a>")
      .hide()
      .addClass("cadastrar-pessoa decorated")
      .attr("id", "cadastrar-pessoa-link")
      .html("Cadastrar pessoa")
      .appendTo($pessoaActionBar);

    $j("<a>")
      .hide()
      .addClass("editar-pessoa decorated")
      .attr("id", "editar-pessoa-link")
      .html("Editar pessoa")
      .appendTo($pessoaActionBar);

    if (resource.isNew()) {
      $nomeField.focus();
      $j(".pessoa-links .cadastrar-pessoa")
        .show()
        .css("display", "inline");
    } else $nomeField.attr("disabled", "disabled");

    var checkTipoResponsavel = function () {
      if ($j("#tipo_responsavel").val() == "outra_pessoa") {
        $j("#responsavel_nome").show();
        $j("#cadastrar-pessoa-responsavel-link").show();
      } else {
        $j("#responsavel_nome").hide();
        $j("#cadastrar-pessoa-responsavel-link").hide();
      }
    };

    checkTipoResponsavel();
    $j("#tipo_responsavel").change(checkTipoResponsavel);

    var checkMoradia = function () {
      if ($j("#moradia").val() == "C") {
        $j("#material").show();
        $j("#casa_outra").hide();
      } else if ($j("#moradia").val() == "O") {
        $j("#material").hide();
        $j("#casa_outra").show();
      } else {
        $j("#casa_outra").hide();
        $j("#material").hide();
      }
    };
    checkMoradia();
    $j("#moradia").change(checkMoradia);

    $j("#tab1").click(function () {
      $j(".alunoTab-active").toggleClass("alunoTab-active alunoTab");
      $j("#tab1").toggleClass("alunoTab alunoTab-active");
      $j(".tablecadastro >tbody  > tr").each(function (index, row) {
        if (index > $j("#tr_observacao_aluno").index() - 1) {
          if (row.id != "stop") row.hide();
          else return false;
        } else {
          row.show();
        }
      });

      if (typeof camposTransporte == "function") {
        camposTransporte();
      }
    });

    var first_click_medica = true;

    $j("#tab2").click(function () {
      $j(".alunoTab-active").toggleClass("alunoTab-active alunoTab");
      $j("#tab2").toggleClass("alunoTab alunoTab-active");
      $j(".tablecadastro >tbody  > tr").each(function (index, row) {
        $j("#tr_historico_altura_peso_tit td").removeClass();
        if (row.id != "stop") {
          if (
            index > $j("#tr_observacao_aluno").index() - 1 &&
            index <
            $j("#tr_responsavel_parentesco_celular").index() - 1
          ) {
            if (first_click_medica)
              $j("#" + row.id)
                .find("td")
                .toggleClass("formlttd formmdtd");
            row.show();
          } else if (index > 0) {
            row.hide();
          }
        } else return false;
      });

      $j(".temDescricao").each(function (i, obj) {
        $j("#desc_" + obj.id).prop(
          "disabled",
          !$j("#" + obj.id).prop("checked")
        );
      });

      first_click_medica = false;
    });

    var first_click_moradia = true;

    $j("#tab4").click(function () {
      $j(".alunoTab-active").toggleClass("alunoTab-active alunoTab");
      $j("#tab4").toggleClass("alunoTab alunoTab-active");
      $j(".tablecadastro >tbody  > tr").each(function (index, row) {
        if (row.id != "stop") {
          if (
            index >
            $j("#tr_responsavel_parentesco_celular").index() -
            1 &&
            index < $j("#tr_fossa").index()
          ) {
            row.show();
          } else if (index != 0) {
            row.hide();
          }
        } else {
          return false;
        }
      });
      first_click_moradia = false;
    });

    $j("#tab5").click(function () {
      $j(".alunoTab-active").toggleClass("alunoTab-active alunoTab");
      $j("#tab5").toggleClass("alunoTab alunoTab-active");
      $j(".tablecadastro >tbody  > tr").each(function (index, row) {
        if (row.id != "stop") {
          if (
            index >= $j("#tr_fossa").index() &&
            index < $j("#tr_recursos_prova_inep__").index()
          ) {
            row.show();
          } else if (index != 0) {
            row.hide();
          }
        } else {
          return false;
        }
      });
    });

    $j("#tab6").click(function () {
      $j(".alunoTab-active").toggleClass("alunoTab-active alunoTab");
      $j("#tab6").toggleClass("alunoTab alunoTab-active");
      $j(".tablecadastro >tbody  > tr").each(function (index, row) {
        if (row.id != "stop") {
          if (
            index >= $j("#tr_recursos_prova_inep__").index() &&
            index < $j("#tr_recursos_prova_inep__").index() + 1
          ) {
            row.show();
          } else if (index != 0) {
            row.hide();
          }
        } else {
          return false;
        }
      });
    });

    /* A seguinte função habilitam/desabilitam o campo de descrição quando for clicado
    nos referentes checkboxs */

    $j(".temDescricao").click(function () {
      if ($j("#" + this.id).prop("checked"))
        $j("#desc_" + this.id).removeAttr("disabled");
      else {
        $j("#desc_" + this.id).attr("disabled", "disabled");
        $j("#desc_" + this.id).val("");
      }
    });

    $j("#municipio_pessoa-aluno").closest("tr").hide();

    $j("body").append(`
          <div id="dialog-form-pessoa-aluno">
            <form>
              <h2></h2>
              <table>
                <tr>
                  <td valign="top">
                    <fieldset>
                      <legend>Dados b&aacute;sicos</legend>
                      <label for="nome-pessoa-aluno">Nome<span class="campo_obrigatorio">*</span> </label>
                      <input type="text" name="nome-pessoa-aluno" id="nome-pessoa-aluno" size="49" maxlength="255" class="text">
                      <label for="nome-social-pessoa-aluno">Nome social e/ou afetivo</label>
                      <input type="text" name="nome-social-pessoa-aluno" id="nome-social-pessoa-aluno" size="49" maxlength="255" class="text">
                      <label for="sexo-pessoa-aluno">Sexo<span class="campo_obrigatorio">*</span> </label>
                      <select class="select ui-widget-content ui-corner-all" name="sexo-pessoa-aluno" id="sexo-pessoa-aluno">
                        <option value="" selected>Sexo</option>
                        <option value="M">Masculino</option>
                        <option value="F">Feminino</option>
                      </select>
                      <label for="estado-civil-pessoa-aluno">Estado civil<span class="campo_obrigatorio">*</span> </label>
                      <select class="select ui-widget-content ui-corner-all" name="estado-civil-pessoa-aluno" id="estado-civil-pessoa-aluno">
                        <option id="estado-civil-pessoa-aluno_" value="" selected>Estado civil</option>
                        <option id="estado-civil-pessoa-aluno_2" value="2">Casado(a)</option>
                        <option id="estado-civil-pessoa-aluno_6" value="6">Companheiro(a)</option>
                        <option id="estado-civil-pessoa-aluno_3" value="3">Divorciado(a)</option>
                        <option id="estado-civil-pessoa-aluno_4" value="4">Separado(a)</option>
                        <option id="estado-civil-pessoa-aluno_1" value="1">Solteiro(a)</option>
                        <option id="estado-civil-pessoa-aluno_5" value="5">Viúvo(a)</option>
                        <option id="estado-civil-pessoa-aluno_7" value="7">Não informado</option>
                      </select>
                      <label for="data-nasc-pessoa-aluno"> Data de nascimento<span class="campo_obrigatorio">*</span> </label>
                      <input onKeyPress="formataData(this, event);" class="" placeholder="dd/mm/yyyy" type="text" name="data-nasc-pessoa-aluno" id="data-nasc-pessoa-aluno" value="" size="11" maxlength="10">
                      <label id="telefone_fixo_dois" style="display: inline;">Telefone</label>
                      <input placeholder="ddd" type="text" name="ddd_telefone_fixo" id="ddd_telefone_fixo" size="3" maxlength="3" style="display: inline;" />
                      <input placeholder="n\u00famero" type="text" name="telefone_fixo" id="telefone_fixo" size="9" maxlength="9" style="display: inline;" />
                      <label style="display: inline;" id="telefone_cel_dois">Celular</label>
                      <input placeholder="ddd" type="text " name="ddd_telefone_cel" id="ddd_telefone_cel" size="3" maxlength="3" style="display: inline; padding: 4px 6px;">
                      <input placeholder="n\u00famero" type="text " name="telefone_cel" id="telefone_cel" size="9" maxlength="9" style="display: inline; padding: 4px 6px;">
                      <label style="display: block;" for="naturalidade_pessoa-aluno"> Naturalidade<span class="campo_obrigatorio">*</span> </label>
                    </fieldset>
                  </td>
                  <td>
                    <fieldset valign="top">
                      <legend>Dados do endere&ccedil;o</legend>
                      <table></table>
                    </fieldset>
                  </td>
                  <td>
                    <fieldset>
                      <table></table>
                    </fieldset>
                  </td>
                </tr>
              </table>
              <p><a id="link_cadastro_detalhado">Cadastro detalhado</a></p>
            </form>
          </div>

        `);

    $j("body").append(`
          <div id="dialog-recursos-prova-inep" style="font-size: 85%; z-index: 9999;">
          <ul style="padding-right: 30px;">
            <li>Dentre as opções: Prova Ampliada (Fonte 18), Prova superampliada (Fonte 24) ou Material didático e Prova em Braille, apenas uma deve ser informada;</li>
            <li><b>Auxílio ledor</b>: pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Cegueira, Baixa visão, Surdocegueira, Deficiência física, Deficiência intelectual e Transtorno do espectro autista. <b>Exceto</b> se possuir também Surdez;</li>
            <li><b>Auxílio transcrição</b>: pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Cegueira, Baixa visão, Surdocegueira, Deficiência física, Deficiência intelectual e Transtorno do espectro autista. Obs.: Quando a deficiência for Cegueira ou Surdocegueira, obrigatoriamente este auxílio deve ser informado junto com um outro auxílio;</li>
            <li><b>Guia-Intérprete</b>: pode ser informado quando o(a) aluno(a) possuir qualquer deficiência. <b>Exceto</b> se possuir Surdocegueira;</li>
            <li><b>Tradutor-Intérprete de Libras</b>: pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Surdez, Deficiência auditiva e Surdocegueira. <b>Exceto</b> se possuir também Cegueira;</li>
            <li><b>Leitura Labial</b>: pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Surdez, Deficiência auditiva e Surdocegueira. <b>Exceto</b> se possuir também Cegueira;</li>
            <li><b>Prova Ampliada (Fonte 18)</b>: pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Baixa visão e Surdocegueira. <b>Exceto</b> se possuir também Cegueira;</li>
            <li><b>Prova superampliada (Fonte 24)</b>: pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Baixa visão e Surdocegueira. <b>Exceto</b> se possuir também Cegueira;</li>
            <li><b>CD com áudio para deficiente visual</b>: pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Cegueira, Baixa visão, Surdocegueira, Deficiência física, Deficiência intelectual e Transtorno do espectro autista. <b>Exceto</b> se possuir também Surdez;</li>
            <li><b>Prova de Língua Portuguesa como segunda língua para surdos e deficientes auditivos</b>: pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Surdez, Deficiência auditiva e Surdocegueira. <b>Exceto</b> se possuir também Cegueira;</li>
            <li><b>Prova em Vídeo em Libras</b>: pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Surdez, Deficiência auditiva e Surdocegueira. <b>Exceto</b> se possuir também Cegueira;</li>
            <li><b>Material didático e Prova em Braille</b>: pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Cegueira e Surdocegueira;</li>
            <li><b>Nenhum</b>: não pode ser informado quando o(a) aluno(a) possuir a(s) deficiência(s): Cegueira e Surdocegueira;</li>
           </ul>
          </div>

        `);

    $j("#dialog-recursos-prova-inep").dialog({
      autoOpen: false,
      title: "Regras para o preenchimento do campo:",
      height: "auto",
      width: "80%",
      modal: true,
      resizable: false,
      draggable: false,
      hide: {
        effect: "clip",
        duration: 500,
      },
      show: {
        effect: "clip",
        duration: 500,
      },
      buttons: {
        Fechar: function () {
          $j(this).dialog("close");
        },
      },
      open: function (event, ui) {
        $j("#dialog-recursos-prova-inep")
          .parent(".ui-dialog")
          .css("z-index", 99998);

        $j(".ui-widget-overlay").css("z-index", 99997);
      },
    });
    $j("body").on("click", ".open-dialog-recursos-prova-inep", () => {
      $j("#dialog-recursos-prova-inep").dialog("open");
    });

    $j(
      ".ui-dialog:has(#dialog-recursos-prova-inep) .ui-dialog-titlebar"
    ).show();

    var name = $j("#nome-pessoa-aluno"),
      nome_social = $j("#nome-social-pessoa-aluno"),
      sexo = $j("#sexo-pessoa-aluno"),
      estadocivil = $j("#estado-civil-pessoa-aluno"),
      datanasc = $j("#data-nasc-pessoa-aluno"),
      municipio = $j("#naturalidade_aluno_pessoa-aluno"),
      municipio_id = $j("#naturalidade_aluno_id"),
      telefone_1 = $j("#telefone_fixo"),
      telefone_mov = $j("#telefone_cel"),
      ddd_telefone_1 = $j("#ddd_telefone_fixo"),
      ddd_telefone_mov = $j("#ddd_telefone_cel"),
      complemento = $j("#complement"),
      numero = $j("#number"),
      letra = $j("#letra"),
      apartamento = $j("#apartamento"),
      bloco = $j("#bloco"),
      andar = $j("#andar"),
      allFields = $j([])
        .add(name)
        .add(nome_social)
        .add(sexo)
        .add(estadocivil)
        .add(datanasc)
        .add(municipio)
        .add(ddd_telefone_1)
        .add(telefone_1)
        .add(ddd_telefone_mov)
        .add(telefone_mov)
        .add(municipio_id)
        .add(complemento)
        .add(numero)
        .add(letra)
        .add(apartamento)
        .add(bloco)
        .add(andar);

    municipio
      .show()
      .toggleClass("geral text")
      .attr("display", "block")
      .appendTo("#dialog-form-pessoa-aluno tr td:first-child fieldset");

    $j("<label>")
      .html("CEP")
      .attr("for", "postal_code")
      .insertBefore($j("#postal_code"));
    $j("#postal_code")
      .toggleClass("geral text")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .appendTo(
        "#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table"
      )
      .find("td")
      .removeClass();
    $j("<label>")
      .html("Endereço")
      .attr("for", "address")
      .insertBefore($j("#address"));
    $j("#address")
      .toggleClass("geral text")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .appendTo(
        "#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table"
      )
      .find("td")
      .removeClass();
    $j("<label>")
      .html("Número")
      .attr("for", "number")
      .insertBefore($j("#number"));
    $j("#number")
      .toggleClass("geral text")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .appendTo(
        "#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table"
      )
      .find("td")
      .removeClass();
    $j("<label>")
      .html("Complemento")
      .attr("for", "complement")
      .insertBefore($j("#complement"));
    $j("#complement")
      .toggleClass("geral text")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .appendTo(
        "#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table"
      )
      .find("td")
      .removeClass();
    $j("<label>")
      .html("Bairro")
      .attr("for", "neighborhood")
      .insertBefore($j("#neighborhood"));
    $j("#neighborhood")
      .toggleClass("geral text")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .appendTo(
        "#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table"
      )
      .find("td")
      .removeClass();
    $j("<label>")
      .html("Município")
      .attr("for", "city_city")
      .insertBefore($j("#city_city"));
    $j("#city_city")
      .toggleClass("geral text")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .appendTo(
        "#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table"
      )
      .find("td")
      .removeClass();

    $j("<label>")
      .html("País de residência")
      .attr("for", "pais_residencia")
      .insertBefore($j("#pais_residencia"));
    $j("#pais_residencia")
      .toggleClass("geral text")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .appendTo(
        "#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table"
      )
      .find("td")
      .removeClass();

    let $label = $j("<label>")
      .html("Zona de residência")
      .attr("for", "zona_localizacao_censo")
      .insertBefore($j("#zona_localizacao_censo"));
    if ($j("#zona_localizacao_censo").hasClass("obrigatorio")) {
      $label.append(
        $j("<span/>").addClass("campo_obrigatorio").text("*")
      );
    }
    $j("#zona_localizacao_censo")
      .toggleClass("geral text obrigatorio")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .appendTo(
        "#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table"
      )
      .find("td")
      .removeClass();
    if ($j("#zona_localizacao_censo").hasClass("obrigatorio")) {
      $label.append(
        $j("<span/>").addClass("campo_obrigatorio").text("*")
      );
    }

    $j("<label>")
      .html("Localização diferenciada de residência")
      .attr("for", "localizacao_diferenciada")
      .insertBefore($j("#localizacao_diferenciada"));
    $j("#localizacao_diferenciada")
      .toggleClass("geral text")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .appendTo(
        "#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table"
      )
      .find("td")
      .removeClass();

    $label = $j("<label>")
      .html("Raça")
      .attr("for", "cor_raca")
      .attr("style", "display:block;")
      .insertBefore($j("#cor_raca"));
    if ($j("#cor_raca").hasClass("obrigatorio")) {
      $label.append(
        $j("<span/>").addClass("campo_obrigatorio").text("*")
      );
    }
    $j("#cor_raca")
      .toggleClass("geral text")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .insertAfter("#telefone_cel")
      .find("td")
      .removeClass();
    $j("#cor_raca").unwrap().unwrap().unwrap();

    $label = $j("<label>")
      .html("Nacionalidade")
      .attr("for", "tipo_nacionalidade")
      .attr("style", "display:block;")
      .insertBefore($j("#tipo_nacionalidade"));
    $j("#tipo_nacionalidade")
      .toggleClass("geral text")
      .closest("tr")
      .show()
      .find("td:first-child")
      .hide()
      .closest("tr")
      .removeClass()
      .insertAfter("#cor_raca")
      .find("td")
      .removeClass();
    $j("#tipo_nacionalidade").unwrap().unwrap().unwrap();
    if ($j("#tipo_nacionalidade").hasClass("obrigatorio")) {
      $label.append(
        $j("<span/>").addClass("campo_obrigatorio").text("*")
      );
    }

    let checkTipoNacionalidade = () => {
      if ($j.inArray($j("#tipo_nacionalidade").val(), ["2", "3"]) > -1) {
        $j("#pais_origem_nome").show();
        $j("#naturalidade_aluno_pessoa-aluno").makeUnrequired();
        $j(
          'label[for="naturalidade_pessoa-aluno"] .campo_obrigatorio'
        ).remove();
      } else {
        $j("#pais_origem_nome").hide();
        $j("#naturalidade_aluno_pessoa-aluno").makeRequired();
        if (
          !$j(
            'label[for="naturalidade_pessoa-aluno"] .campo_obrigatorio'
          ).length
        ) {
          $j('label[for="naturalidade_pessoa-aluno"]').append(
            $j('<span class="campo_obrigatorio">*</span>')
          );
        }
      }
    };
    $j("#tipo_nacionalidade").change(checkTipoNacionalidade);

    $j("#dialog-form-pessoa-aluno").find(":input").css("display", "block");
    $j("#postal_code").css("display", "inline");
    $j("#ddd_telefone_fixo").css("display", "inline");
    $j("#telefone_fixo").css("display", "inline");
    $j("#ddd_telefone_cel").css("display", "inline");
    $j("#telefone_cel").css("display", "inline");
    $j("#telefone_fixo_dois").css("display", "block");
    $j("#telefone_cel_dois").css("display", "block");

    $j("#dialog-form-pessoa-aluno").dialog({
      autoOpen: false,
      height: "auto",
      width: "50em",
      modal: true,
      resizable: false,
      draggable: false,
      buttons: {
        Gravar: function () {
          var bValid = true;
          allFields.removeClass("error");

          bValid = bValid && checkLength(name, "nome", 3, 255);
          bValid = bValid && checkSelect(sexo, "sexo");
          bValid = bValid && checkSelect(estadocivil, "estado civil");
          bValid =
            bValid &&
            checkRegexp(
              datanasc,
              /(^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$)/i,
              "O campo data de nascimento deve ser preenchido no formato dd/mm/yyyy."
            );
          if (municipio.hasClass("obrigatorio")) {
            bValid =
              bValid &&
              checkSimpleSearch(
                municipio,
                municipio_id,
                "munic\u00edpio"
              );
          }

          bValid =
            bValid &&
            ($j("#postal_code").val() == ""
              ? true
              : validateEndereco());

          if (!validaObrigatoriedadeTelefone()) {
            bValid = false;
          }

          if ($j("#zona_localizacao_censo").hasClass("obrigatorio")) {
            bValid =
              bValid &&
              checkSelect(
                $j("#zona_localizacao_censo"),
                "zona localização"
              );
          }
          if ($j("#cor_raca").hasClass("obrigatorio")) {
            bValid = bValid && checkSelect($j("#cor_raca"), "raça");
          }
          if ($j("#tipo_nacionalidade").hasClass("obrigatorio")) {
            bValid =
              bValid &&
              checkSelect(
                $j("#tipo_nacionalidade"),
                "nacionalidade"
              );
          }
          if (
            $j("#pais_origem_id").hasClass("obrigatorio") &&
            $j("#pais_origem_nome").is(":visible")
          ) {
            bValid =
              bValid &&
              checkSimpleSearch(
                $j("#pais_origem_nome"),
                $j("#pais_origem_id"),
                "pais de origem"
              );
          }

          if (bValid) {
            postPessoa(
              $j(this),
              $j("#pessoa_nome"),
              name.val(),
              sexo.val(),
              estadocivil.val(),
              datanasc.val(),
              municipio_id.val(),
              editar_pessoa ? $j("#pessoa_id").val() : null,
              null,
              ddd_telefone_1.val(),
              telefone_1.val(),
              ddd_telefone_mov.val(),
              telefone_mov.val(),
              undefined,
              $j("#tipo_nacionalidade").val(),
              $j("#pais_origem_id").val(),
              $j("#cor_raca").val(),
              $j("#zona_localizacao_censo").val(),
              $j("#localizacao_diferenciada").val(),
              nome_social.val(),
              $j("#pais_residencia").val()
            );
          }
        },
        Cancelar: function () {
          $j(this).dialog("close");
        },
      },
      create: function () {
        $j(this)
          .closest(".ui-dialog")
          .find(".ui-button-text:first")
          .addClass("btn-green");
      }
      ,
      close: function () {
        allFields.val("").removeClass("error");
      },
      hide: {
        effect: "clip",
        duration: 500,
      },
      show: {
        effect: "clip",
        duration: 500,
      },
    });

    $j("body").append(
      '<div id="dialog-form-pessoa-parent"><form><h2></h2><table><tr><td valign="top"><fieldset><label for="nome-pessoa-parent">Nome</label>    <input type="text " name="nome-pessoa-parent" id="nome-pessoa-parent" size="49" maxlength="255" class="text">    <label for="sexo-pessoa-parent">Sexo</label>  <select class="select ui-widget-content ui-corner-all" name="sexo-pessoa-parent" id="sexo-pessoa-parent" ><option value="" selected>Sexo</option><option value="M">Masculino</option><option value="F">Feminino</option></select>  <label for="estado-civil-pessoa-parent">Estado civil</label>   <select class="select ui-widget-content ui-corner-all" name="estado-civil-pessoa-parent" id="estado-civil-pessoa-parent"  ><option id="estado-civil-pessoa-parent_" value="" selected>Estado civil</option><option id="estado-civil-pessoa-parent_2" value="2">Casado(a)</option><option id="estado-civil-pessoa-parent_6" value="6">Companheiro(a)</option><option id="estado-civil-pessoa-parent_3" value="3">Divorciado(a)</option><option id="estado-civil-pessoa-parent_4" value="4">Separado(a)</option><option id="estado-civil-pessoa-parent_1" value="1">Solteiro(a)</option><option id="estado-civil-pessoa-parent_5" value="5">Vi&uacute;vo(a)</option><option id="estado-civil-pessoa-parent_7" value="7">Não informado</option></select><label for="data-nasc-pessoa-parent"> Data de nascimento </label> <input onKeyPress="formataData(this, event);" class="" placeholder="dd/mm/yyyy" type="text" name="data-nasc-pessoa-parent" id="data-nasc-pessoa-parent" value="" size="11" maxlength="10"> <div id="falecido-modal"> <label>Falecido?</label><input type="checkbox" name="falecido-parent" id="falecido-parent" style="display:inline;"> </div></fieldset><center><p><a id="link_cadastro_detalhado_parent" class="btn" style="color: blue !important; border: 1px solid blue; margin:2px">Cadastro detalhado...</a><br><a class="btn btn-success" id="link_export_responsavel" style="color:white !important; margin:2px;">Exportar Excel</a></p></center></form></div>'
    );

    $j("#dialog-form-pessoa-parent").find(":input").css("display", "block");

    var nameParent = $j("#nome-pessoa-parent"),
      sexoParent = $j("#sexo-pessoa-parent"),
      estadocivilParent = $j("#estado-civil-pessoa-parent"),
      datanascParent = $j("#data-nasc-pessoa-parent"),
      falecidoParent = $j("#falecido-parent"),
      allFields = $j([])
        .add(nameParent)
        .add(sexoParent)
        .add(estadocivilParent)
        .add(datanascParent)
        .add(falecidoParent);


    $j("#dialog-form-pessoa-parent").dialog({
      autoOpen: false,
      height: "auto",
      width: "auto",
      modal: true,
      resizable: false,
      draggable: false,
      buttons: {
        Gravar: function () {
          var bValid = true;
          allFields.removeClass("ui-state-error");

          bValid = bValid && checkLength(nameParent, "nome", 3, 255);
          bValid = bValid && checkSelect(sexoParent, "sexo");
          bValid =
            bValid &&
            checkSelect(estadocivilParent, "estado civil");

          if ($j("#data-nasc-pessoa-parent").val() != "") {
            bValid =
              bValid &&
              checkRegexp(
                datanascParent,
                /(^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$)/i,
                "O campo data de nascimento deve ser preenchido no formato dd/mm/yyyy."
              );
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
              editar_pessoa
                ? $j("#" + pessoaPaiOuMae + "_id").val()
                : null,
              pessoaPaiOuMae,
              null,
              null,
              null,
              null,
              falecidoParent.is(":checked")
            );
          }
        },
        Cancelar: function () {
          $j(this).dialog("close");
        },
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
        duration: 500,
      },
      show: {
        effect: "clip",
        duration: 500,
      },
    });

    $j("#link_cadastro_detalhado").click(function (e) {
      e.preventDefault();
      windowUtils.open(this.href);
      $j("#dialog-form-pessoa-aluno").dialog("close");
    });

    $j("#link_cadastro_detalhado_parent").click(function (e) {
      e.preventDefault();
      windowUtils.open(this.href);
      $j("#dialog-form-pessoa-parent").dialog("close");
    });

    $j("#cadastrar-pessoa-link").click(function () {
      $j("#link_cadastro_detalhado").attr(
        "href",
        "/intranet/atendidos_cad.php"
      );
      $j("#dialog-form-pessoa-aluno").dialog("open");
      $j("#postal_code").val("");
      permiteEditarEndereco();
      checkTipoNacionalidade();

      $j(".ui-widget-overlay").click(function () {
        $j(".ui-dialog-titlebar-close").trigger("click");
      });

      $j("#nome-pessoa-aluno").focus();

      $j("#dialog-form-pessoa-aluno form h2:first-child")
        .html("Cadastrar pessoa aluno")
        .css("margin-left", "0.75em");
      editar_pessoa = false;
    });

    $j("#editar-pessoa-link").click(function () {
      $j("#link_cadastro_detalhado").attr(
        "href",
        "/intranet/atendidos_cad.php?cod_pessoa_fj=" + person_details.id
      );

      $j("#link_export_responsavel").attr(
        "href",
        "/export/responsavel?cod_pessoa_fj=" + person_details.id
      );
    

      


      name.val(person_details.nome);
      nome_social.val(person_details.nome_social);
      datanasc.val(person_details.data_nascimento);
      estadocivil.val(person_details.estadocivil);
      sexo.val(person_details.sexo);

      if (person_details.idmun_nascimento) {
        $j("#naturalidade_aluno_id").val(
          person_details.idmun_nascimento
        );
        $j("#naturalidade_aluno_pessoa-aluno").val(
          person_details.idmun_nascimento +
          " - " +
          person_details.municipio_nascimento +
          " (" +
          person_details.sigla_uf_nascimento +
          ")"
        );
      }

      $j("#zona_localizacao_censo").val(
        person_details.zona_localizacao_censo
      );
      $j("#localizacao_diferenciada").val(
        person_details.localizacao_diferenciada
      );
      $j("#cor_raca").val(person_details.cor_raca);
      $j("#tipo_nacionalidade").val(person_details.tipo_nacionalidade);
      if (person_details.pais_origem_id) {
        $j("#pais_origem_id").val(person_details.pais_origem_id);
        $j("#pais_origem_nome").val(
          `${person_details.pais_origem_id} - ${person_details.pais_origem_nome}`
        );
      } else {
        $j("#pais_origem_id").val("");
        $j("#pais_origem_nome").val("");
      }

      $j("#postal_code").val(person_details.cep);
      $j("#ddd_telefone_fixo").val(person_details.ddd_fone_fixo);
      $j("#telefone_fixo").val(person_details.fone_fixo);
      $j("#ddd_telefone_cel").val(person_details.ddd_fone_mov);
      $j("#telefone_cel").val(person_details.fone_mov);
      $j("#pais_residencia").val(person_details.pais_residencia);

      if ($j("#postal_code").val()) {
        $j("#city_city").removeAttr("disabled");
        $j("#neighborhood").removeAttr("disabled");
        $j("#address").removeAttr("disabled");
        $j("#zona_localizacao").removeAttr("disabled");
        $j("#number").removeAttr("disabled");
        $j("#complement").removeAttr("disabled");
        $j("#address").val(person_details.address);
        $j("#number").val(person_details.number);
        $j("#complement").val(person_details.complement);
        $j("#neighborhood").val(person_details.neighborhood);
        $j("#city_id").val(person_details.city_id);
        $j("#city_city").val(
          person_details.city_id +
          " - " +
          person_details.city_name +
          " (" +
          person_details.state_abbreviation +
          ")"
        );
      }

      $j("#dialog-form-pessoa-aluno").dialog("open");

      $j(".ui-widget-overlay").click(function () {
        $j(".ui-dialog-titlebar-close").trigger("click");
      });

      $j("#nome-pessoa-aluno").focus();

      $j("#dialog-form-pessoa-aluno form h2:first-child")
        .html("Editar pessoa aluno")
        .css("margin-left", "0.75em");

      editar_pessoa = true;

      permiteEditarEndereco();
      checkTipoNacionalidade();
    });

    $j("#cadastrar-pessoa-pai-link").click(function () {
      if ($j("#pessoa_id").val()) {
        openModalParent("pai");
      } else {
        alertSelecionarPessoaAluno();
      }
    });

    $j("#cadastrar-pessoa-mae-link").click(function () {
      if ($j("#pessoa_id").val()) {
        openModalParent("mae");
      } else {
        alertSelecionarPessoaAluno();
      }
    });

    $j("#cadastrar-pessoa-responsavel-link").click(function () {
      if ($j("#pessoa_id").val()) {
        openModalParent("responsavel");
      } else {
        alertSelecionarPessoaAluno();
      }
    });


    $j("#editar-pessoa-pai-link").click(function () {
      if ($j("#pessoa_id").val()) {
        openEditModalParent("pai");
      }
    });

    $j("#editar-pessoa-mae-link").click(function () {
      if ($j("#pessoa_id").val()) {
        openEditModalParent("mae");
      }
    });

    $j("#editar-pessoa-responsavel-link").click(function () {
      if ($j("#pessoa_id").val()) {
        openEditModalParent("responsavel");
      }
    });

    function alertSelecionarPessoaAluno() {
      messageUtils.error(
        "Primeiro cadastre/selecione uma pessoa para o aluno. "
      );
    }

    function openModalParent(parentType) {
      $j("#link_cadastro_detalhado_parent").attr(
        "href",
        "/intranet/atendidos_cad.php?parent_type=" + parentType
      );
      $j("#dialog-form-pessoa-parent").dialog("open");
      $j(".ui-widget-overlay").click(function () {
        $j(".ui-dialog-titlebar-close").trigger("click");
      });
      $j("#nome-pessoa-parent").focus();
      $j("#falecido-parent").attr("checked", false);

      var tipoPessoa = "pai";

      switch (parentType) {
        case "mae":
          tipoPessoa = "m&atilde;e";
          break;
        case "responsavel":
          tipoPessoa = "respons&aacute;vel";
          break;
        default:
          tipoPessoa = "pai";
      }

      if (parentType == "responsavel") {
        $j("#falecido-modal").hide();
      } else {
        $j("#falecido-modal").show();
      }

      $j("#dialog-form-pessoa-parent form h2:first-child")
        .html("Cadastrar pessoa " + tipoPessoa)
        .css("margin-left", "0.75em");

      pessoaPaiOuMae = parentType;
      editar_pessoa = false;
    }

    function openEditModalParent(parentType) {
      $j("#link_cadastro_detalhado_parent").attr(
        "href",
        "/intranet/atendidos_cad.php?cod_pessoa_fj=" +
        $j("#" + parentType + "_id").val() +
        "&parent_type=" +
        parentType
      );
      $j("#link_export_responsavel").attr(
        "href",
        "/exports/responsavel?cod_pessoa_fj=" +
        $j("#" + parentType + "_id").val()
      );

      $j("#dialog-form-pessoa-parent").dialog("open");
      $j(".ui-widget-overlay").click(function () {
        $j(".ui-dialog-titlebar-close").trigger("click");
      });
      $j("#nome-pessoa-parent").focus();

      nameParent.val(window[parentType + "_details"].nome);
      estadocivilParent.val(window[parentType + "_details"].estadocivil);
      sexoParent.val(window[parentType + "_details"].sexo);
      datanascParent.val(window[parentType + "_details"].data_nascimento);

      falecidoParent.prop(
        "checked",
        window[parentType + "_details"].falecido
      );

      if (parentType == "responsavel") {
        $j("#falecido-modal").hide();
      } else {
        $j("#falecido-modal").show();
      }

      $j("#dialog-form-pessoa-parent form h2:first-child")
        .html(
          "Editar pessoa " +
          (parentType == "mae" ? "m&atilde;e" : parentType)
        )
        .css("margin-left", "0.75em");

      pessoaPaiOuMae = parentType;
      editar_pessoa = true;
    }

    function checkLength(o, n, min, max) {
      if (o.val().length > max || o.val().length < min) {
        o.addClass("error");

        messageUtils.error(
          "Tamanho do " +
          n +
          " deve ter entre " +
          min +
          " e " +
          max +
          " caracteres."
        );
        return false;
      } else {
        return true;
      }
    }

    function checkRegexp(o, regexp, n) {
      if (!regexp.test(o.val())) {
        o.addClass("error");
        messageUtils.error(n);
        return false;
      } else {
        return true;
      }
    }

    function checkSelect(comp, name) {
      if (comp.val() == "") {
        comp.addClass("error");
        messageUtils.error("Selecione um(a) " + name + ".");
        return false;
      } else {
        return true;
      }
    }

    function checkSimpleSearch(comp, hiddenComp, name) {
      if (hiddenComp.val() == "") {
        comp.addClass("error");
        messageUtils.error("Selecione um(a) " + name + ".");
        return false;
      } else {
        return true;
      }
    }

    function validaObrigatoriedadeTelefone() {
      let obrigarTelefonePessoa =
        $j("#obrigar_telefone_pessoa").val() == "1";
      let telefoneFixo = $j("#telefone_fixo").val();
      let telefoneCel = $j("#telefone_cel").val();

      if (
        obrigarTelefonePessoa &&
        telefoneFixo == "" &&
        telefoneCel == ""
      ) {
        messageUtils.error(
          "É necessário informar um Telefone ou Celular."
        );
        return false;
      }

      return true;
    }

    $j("#pai_id").change(function () {
      getPersonParentDetails($j(this).val(), "pai");
    });

    $j("#mae_id").change(function () {
      getPersonParentDetails($j(this).val(), "mae");
    });

    $j("#responsavel_id").change(function () {
      getPersonParentDetails($j(this).val(), "responsavel");
    });

    $cpfField.focusout(function () {
      $j(document).removeData("submit_form_after_ajax_validation");
      validatesUniquenessOfCpf();
    });

    var validatesUniquenessOfCpf = function () {
      var cpf = $cpfField.val();

      $cpfNotice.hide();

      if (cpf && !ignoreValidation.includes(cpf) && validatesCpf()) {
        getPersonByCpf(cpf);
      } else {
        handleShowSubmit();
      }
    };

    var handleGetPersonByCpf = function (dataResponse) {
      handleMessages(dataResponse.msgs);
      $cpfNotice.hide();

      var pessoaId = dataResponse.id;

      if (pessoaId && pessoaId != $j("#pessoa_id").val()) {
        $cpfNotice
          .html(
            "CPF já utilizado pela pessoa código " + pessoaId + ", "
          )
          .slideDown("fast");

        $j("<a>")
          .addClass("decorated")
          .attr(
            "href",
            "/intranet/atendidos_cad.php?cod_pessoa_fj=" + pessoaId
          )
          .attr("target", "_blank")
          .html("acessar cadastro.")
          .appendTo($cpfNotice);

        $j("body,html").animate(
          { scrollTop: $j("body").offset().top },
          "fast"
        );

        $submitButton.attr("disabled", "disabled").hide();
      } else {
        handleShowSubmit();
      }
    };

    var getPersonByCpf = function (cpf) {
      var options = {
        url: getResourceUrlBuilder.buildUrl(
          "/module/Api/pessoa",
          "pessoa"
        ),
        dataType: "json",
        data: { cpf: cpf },
        success: handleGetPersonByCpf,
        async: false,
      };

      getResource(options);
    };

    var validatesCpf = function () {
      var valid = true;
      var cpf = $cpfField.val();

      $cpfNotice.hide();

      if (cpf && !ignoreValidation.includes(cpf) && !validationUtils.validatesCpf(cpf)) {
        $cpfNotice.html("O CPF informado é inválido").slideDown("fast");

        $submitButton.attr("disabled", "disabled").hide();

        valid = false;
      } else {
        $submitButton.attr("disabled", false).show();
      }

      return valid;
    };
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
    falecido,
    tipo_nacionalidade,
    pais_origem_id,
    cor_raca,
    zona_localizacao_censo,
    localizacao_diferenciada,
    nome_social,
    pais_residencia,
  ) {
    var data = {
      nome: nome,
      sexo: sexo,
      estadocivil: estadocivil,
      datanasc: datanasc,
      ddd_telefone_1: ddd_telefone_1,
      telefone_1: telefone_1,
      ddd_telefone_mov: ddd_telefone_mov,
      telefone_mov: telefone_mov,
      naturalidade: naturalidade,
      pessoa_id: pessoa_id,
      falecido: falecido,
      tipo_nacionalidade: tipo_nacionalidade,
      pais_origem_id: pais_origem_id,
      cor_raca: cor_raca,
      zona_localizacao_censo: zona_localizacao_censo,
      localizacao_diferenciada: localizacao_diferenciada,
      nome_social: nome_social,
      pais_residencia: pais_residencia
    };

    var options = {
      url: postResourceUrlBuilder.buildUrl(
        "/module/Api/pessoa",
        "pessoa",
        {}
      ),
      dataType: "json",
      data: data,
      success: function (dataResponse) {
        if (dataResponse["any_error_msg"]) {
          dataResponse["msgs"].forEach((msgObject) => {
            messageUtils.error(msgObject["msg"]);
          });
        } else {
          $container.dialog("close");
          if (parentType == "mae")
            afterChangePessoaParent(dataResponse.pessoa_id, "mae");
          else if (parentType == "pai")
            afterChangePessoaParent(dataResponse.pessoa_id, "pai");
          else if (parentType == "responsavel")
            afterChangePessoaParent(
              dataResponse.pessoa_id,
              "responsavel"
            );
          else postEnderecoPessoa(dataResponse.pessoa_id);
        }
      },
    };

    postResource(options);
  }

  function postEnderecoPessoa(pessoa_id) {
    if (checkCepFields($j("#postal_code").val())) {
      var data = {
        person_id: pessoa_id,
        postal_code: $j("#postal_code").val(),
        address: $j("#address").val(),
        number: $j("#number").val(),
        complement: $j("#complement").val(),
        neighborhood: $j("#neighborhood").val(),
        city_id: $j("#city_id").val(),
      };

      var options = {
        url: postResourceUrlBuilder.buildUrl(
          "/module/Api/pessoa",
          "pessoa-endereco",
          {}
        ),
        dataType: "json",
        data: data,
        success: function (dataResponse) {
          afterChangePessoa(null, null, pessoa_id);
        },
      };

      postResource(options);
    } else {
      afterChangePessoa(null, null, pessoa_id);
    }
  }

  $j("#beneficios_chzn ul").css("width", "307px");

  window.setTimeout(function () {
    $j("#btn_enviar").unbind().click(newSubmitForm);
  }, 500);
})(jQuery);

var handleSelect = function (event, ui) {
  $j(event.target).val(ui.item.label);
  return false;
};

var searchProjeto = function (request, response) {
  var searchPath = "/module/Api/Projeto?oper=get&resource=projeto-search";
  var params = { query: request.term };

  $j.get(searchPath, params, function (dataResponse) {
    simpleSearch.handleSearch(dataResponse, response);
  });
};

function setAutoComplete() {
  $j.each($j('input[id^="projeto_cod_projeto"]'), function (index, field) {
    $j(field).autocomplete({
      source: searchProjeto,
      select: handleSelect,
      minLength: 1,
      autoFocus: true,
    });
  });
}

setAutoComplete();

var $addProjetoButton = $j("#btn_add_tab_add_2");

$addProjetoButton.click(function () {
  setAutoComplete();
});

if ($j("#transporte_rota").length > 0) {
  $j("#transporte_rota").on("change", function () {
    chamaGetPonto();
  });

  var valPonto = 0;

  function chamaGetPonto() {
    var campoRota = $j("#transporte_rota").val();
    var campoPonto = document.getElementById("transporte_ponto");

    if (campoRota == "") {
      campoPonto.length = 1;
      campoPonto.options[0].text = "Selecione uma rota acima";
    } else {
      campoPonto.length = 1;
      campoPonto.disabled = true;
      campoPonto.options[0].text = "Carregando pontos...";

      var xml_ponto = new ajax(getPonto);
      xml_ponto.envia("ponto_xml.php?rota=" + campoRota);
    }
  }

  function getPonto(xml_ponto) {
    var campoPonto = document.getElementById("transporte_ponto");
    var DOM_array = xml_ponto.getElementsByTagName("ponto");

    if (DOM_array.length) {
      campoPonto.length = 1;
      campoPonto.options[0].text = "Selecione um ponto";
      campoPonto.disabled = false;

      for (var i = 0; i < DOM_array.length; i++) {
        campoPonto.options[campoPonto.options.length] = new Option(
          DOM_array[i].firstChild.data,
          DOM_array[i].getAttribute("cod_ponto"),
          false,
          false
        );
      }

      $j("#transporte_ponto").val(valPonto);
    } else {
      campoPonto.options[0].text = "Rota sem pontos";
    }
  }

  function camposTransporte() {
    $tipoTransporte = $j("#tipo_transporte");

    $j("#veiculo_transporte_escolar").makeUnrequired();
    if ($tipoTransporte.val() == "nenhum") {
      document.getElementById(
        "veiculo_transporte_escolar"
      ).disabled = true;
      $j("#transporte_rota").closest("tr").hide();
      $j("#transporte_ponto").closest("tr").hide();
      $j("#pessoaj_transporte_destino").closest("tr").hide();
      $j("#transporte_observacao").closest("tr").hide();
    } else if (
      $tipoTransporte.val() == "municipal" ||
      ($tipoTransporte.val() == "estadual" &&
        $tipoTransporte.val() != "nenhum")
    ) {
      if (obrigarCamposCenso) {
        $j("#veiculo_transporte_escolar").makeRequired();
      }
      document.getElementById(
        "veiculo_transporte_escolar"
      ).disabled = false;
      $j("#transporte_rota").closest("tr").show();
      $j("#transporte_ponto").closest("tr").show();
      $j("#pessoaj_transporte_destino").closest("tr").show();
      $j("#transporte_observacao").closest("tr").show();
    } else {
      document.getElementById(
        "veiculo_transporte_escolar"
      ).disabled = true;
      $j("#transporte_rota").closest("tr").hide();
      $j("#transporte_ponto").closest("tr").hide();
      $j("#pessoaj_transporte_destino").closest("tr").hide();
      $j("#transporte_observacao").closest("tr").hide();
    }

    $j("#veiculo_transporte_escolar").trigger("chosen:updated");
  }

  $j("#tipo_transporte").on("change", function () {
    camposTransporte();
  });

  function verificaObrigatoriedadeRg() {
    $j("#data_emissao_rg").makeUnrequired();
    $j("#orgao_emissao_rg").makeUnrequired();
    $j("#uf_emissao_rg").makeUnrequired();
    if ($j("#rg").val().trim().length && obrigarCamposCenso) {
      $j("#data_emissao_rg").makeRequired();
      $j("#orgao_emissao_rg").makeRequired();
      $j("#uf_emissao_rg").makeRequired();
    }
  }

  $j("#rg").on("change", verificaObrigatoriedadeRg);
}

aluno_inep_id.on("keyup change", function () {
  const value = $j(this).val().split("");
  if (value[0] === "0") {
    messageUtils.error(
      "O código INEP não pode começar com o número 0 (zero)."
    );
    $j(this).val("");
  }
});