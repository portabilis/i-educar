// before page is ready

function hrefToCreateParent(parentType) {
  return '/intranet/atendidos_cad.php?parent_type=' + parentType;
}

function hrefToEditParent(parentType) {
  var id = $j(buildId(parentType + '_id')).val();
  return hrefToCreateParent(parentType) + '&cod_pessoa_fj=' + id;
}

var pessoaId      = $j('#cod_pessoa_fj').val();
var $form         = $j('#formcadastro');
var $submitButton = $j('#btn_enviar');
var $cpfField     = $j('#id_federal');
var $cpfNotice    = $j('<span>').html('')
                                .addClass('error resource-notice')
                                .hide()
                                .width($j('#nm_pessoa').outerWidth() - 12)
                                .appendTo($cpfField.parent());


// links pessoa pai, mãe

var $paiNomeField = $j('#pai_nome');
var $paiIdField   = $j('#pai_id');

var $maeNomeField = $j('#mae_nome');
var $maeIdField   = $j('#mae_id');


var $pessoaPaiActionBar  = $j('<span>').html('')
                                       .addClass('pessoa-links pessoa-pai-links')
                                       .width($paiNomeField.outerWidth() - 12)
                                       .appendTo($paiNomeField.parent());

var $pessoaMaeActionBar = $pessoaPaiActionBar.clone()
                                         .removeClass('pessoa-pai-links')
                                         .addClass('pessoa-mae-links')
                                         .appendTo($maeNomeField.parent());

var $linkToCreatePessoaPai = $j('<a>').addClass('cadastrar-pessoa-pai decorated')
                                      .attr('href', hrefToCreateParent('pai'))
                                      .attr('target', '_blank')
                                      .html('Cadastrar pessoa')
                                      .appendTo($pessoaPaiActionBar);

var $linkToEditPessoaPai = $j('<a>').hide()
                                    .addClass('editar-pessoa-pai decorated')
                                    .attr('href', hrefToEditParent('pai'))
                                    .attr('target', '_blank')
                                    .html('Editar pessoa')
                                    .appendTo($pessoaPaiActionBar);

var $linkToCreatePessoaMae = $linkToCreatePessoaPai.clone()
                                                   .removeClass('cadastrar-pessoa-pai')
                                                   .addClass('cadastrar-pessoa-mae')
                                                   .attr('href', hrefToCreateParent('mae'))
                                                   .appendTo($pessoaMaeActionBar);

var $linkToEditPessoaMae = $linkToEditPessoaPai.clone()
                                               .removeClass('editar-pessoa-pai')
                                               .addClass('editar-pessoa-mae')
                                               .attr('href', hrefToEditParent('mae'))
                                               .appendTo($pessoaMaeActionBar);

var handleGetPersonByCpf = function(dataResponse) {
  handleMessages(dataResponse.msgs);
  $cpfNotice.hide();

  var pessoaId = dataResponse.id;

  if (pessoaId && pessoaId != $j('#cod_pessoa_fj').val()) {
    $cpfNotice.html(stringUtils.toUtf8('CPF já utilizado pela pessoa código ' + pessoaId + ', ')).slideDown('fast');

    $j('<a>').addClass('decorated')
             .attr('href', '/intranet/atendidos_cad.php?cod_pessoa_fj=' + pessoaId)
             .attr('target', '_blank')
             .html('acessar cadastro.')
             .appendTo($cpfNotice);

    $j('body,html').animate({ scrollTop: $j('body').offset().top }, 'fast');
  }

  else if ($j(document).data('submit_form_after_ajax_validation'))
    formUtils.submit();
}


var getPersonByCpf = function(cpf) {
  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa'),
    dataType : 'json',
    data     : { cpf : cpf },
    success  : handleGetPersonByCpf,

    // forçado requisições sincronas, evitando erro com requisições ainda não concluidas,
    // como no caso, onde o usuário pressiona cancelar por exemplo.
    async    : false
  };

  getResource(options);
}


// hide or show #pais_origem_nome by #tipo_nacionalidade
var checkTipoNacionalidade = function() {
  if ($j.inArray($j('#tipo_nacionalidade').val(), ['2', '3']) > -1)
    $j('#pais_origem_nome').show();
  else
    $j('#pais_origem_nome').hide();
}

// hide or show *certidao* fields, by #tipo_certidao_civil
var checkTipoCertidaoCivil = function() {
  var $certidaoCivilFields     = $j('#termo_certidao_civil, #livro_certidao_civil, #folha_certidao_civil');
  var $certidaoNascimentoField = $j('#certidao_nascimento');
  var tipoCertidaoCivil        = $j('#tipo_certidao_civil').val();

  $certidaoCivilFields.hide();
  $certidaoNascimentoField.hide();

  if ($j.inArray(tipoCertidaoCivil, ['91', '92']) > -1) {
    $certidaoCivilFields.show();
    $j('#tr_tipo_certidao_civil td:first span').html(stringUtils.toUtf8('Tipo certidão civil / Termo / Livro / Folha'));
  }

  else if (tipoCertidaoCivil == 'certidao_nascimento_novo_formato') {
    $certidaoNascimentoField.show();
    $j('#tr_tipo_certidao_civil td:first span').html(stringUtils.toUtf8('Tipo certidão civil / Certidão nascimento'));
  }

}

var validatesCpf = function() {
  var valid = true;
  var cpf   = $cpfField.val();

  $cpfNotice.hide();

  if (cpf && ! validationUtils.validatesCpf(cpf)) {
    $cpfNotice.html(stringUtils.toUtf8('O CPF informado é inválido')).slideDown('fast');

    // não usado $cpfField.focus(), pois isto prenderia o usuário a página,
    // caso o mesmo tenha informado um cpf invalido e clique em cancelar
    $j('body,html').animate({ scrollTop: $j('body').offset().top }, 'fast');

    valid = false;
  }

  return valid;
}


var validatesUniquenessOfCpf = function() {
  var cpf = $cpfField.val();

  $cpfNotice.hide();

  if(cpf && validatesCpf())
    getPersonByCpf(cpf);
}


var submitForm = function(event) {
  if ($j('#cep_').val()){
    if (!validateEndereco()){
      alert('Preencha os campos de endera\u00e7amento corretamente.')
      return;
    }
  }
  if ($cpfField.val()) {
    $j(document).data('submit_form_after_ajax_validation', true);
    validatesUniquenessOfCpf();
  }

  else
    formUtils.submit();
}

// when page is ready

$j(document).ready(function() {
  $cpfField.focus();

  changeVisibilityOfLinksToPessoaPai();
  changeVisibilityOfLinksToPessoaMae();

  // style fixup

  // agrupado zebra por tipo documento, branco => .formlttd, colorido => .formmdtd

  $j('#tr_uf_emissao_certidao_civil td').removeClass('formmdtd');
  $j('#tr_carteira_trabalho td').removeClass('formlttd').addClass('formmdtd');

  // bind events

  checkTipoNacionalidade();
  $j('#tipo_nacionalidade').change(checkTipoNacionalidade);

  checkTipoCertidaoCivil();
  $j('#tipo_certidao_civil').change(checkTipoCertidaoCivil);

  $cpfField.focusout(function() {
    $j(document).removeData('submit_form_after_ajax_validation');
    validatesUniquenessOfCpf();
  });


  // ao clicar na lupa de pesquisa de cep, move página para cima,
  // pois (exceto no ie), a popup de pesquisa é exibida no topo da página.
  if (! $j.browser.msie) {
    $j('#cep_').siblings('img').click(function(){
      $j('body,html').animate({ scrollTop: $j('body').offset().top }, 'fast');
    });
  }

  $submitButton.removeAttr('onclick');
  $submitButton.click(submitForm);

  hideEnderecoFields();
  fixUpPlaceholderEndereco();

}); // ready


// pessoa links callbacks

var changeVisibilityOfLinksToPessoaParent = function(parentType) {
  var $nomeField  = $j(buildId(parentType + '_nome'));
  var $idField    = $j(buildId(parentType + '_id'));
  var $linkToEdit = $j('.pessoa-' + parentType + '-links .editar-pessoa-' + parentType);

  if($nomeField.val() && $idField.val()) {
    $linkToEdit.attr('href', hrefToEditParent(parentType));
    $linkToEdit.show().css('display', 'inline');
  }
  else {
    $nomeField.val('')
    $idField.val('');

    $linkToEdit.hide();
  }
}

var changeVisibilityOfLinksToPessoaPai = function() {
  changeVisibilityOfLinksToPessoaParent('pai');
}

var changeVisibilityOfLinksToPessoaMae = function() {
  changeVisibilityOfLinksToPessoaParent('mae');
}


// children callbacks

var afterSetSearchFields = function() {
  $j('body,html').animate({ scrollTop: $j('#btn_enviar').offset().top }, 'fast');
  $j('#complemento').focus();
};

var afterUnsetSearchFields = function() {
  $j('body,html').animate({ scrollTop: $j('#btn_enviar').offset().top }, 'fast');
  $j('#cep_').focus();
};

function afterChangePessoa(targetWindow, parentType, parentId, parentName) {
  targetWindow.close();

  var $idField   = $j(buildId(parentType + '_id'));
  var $nomeField = $j(buildId(parentType + '_nome'));

  // timeout para usuario perceber mudança
  window.setTimeout(function() {
    messageUtils.success('Pessoa alterada com sucesso', $nomeField);

    $idField.val(parentId);
    $nomeField.val(parentId + ' - ' +parentName);
    $nomeField.focus();

    changeVisibilityOfLinksToPessoaParent(parentType);

  }, 500);
}


// simple search options

var simpleSearchPaiOptions = {
  autocompleteOptions : { close  : changeVisibilityOfLinksToPessoaPai }
};

var simpleSearchMaeOptions = {
  autocompleteOptions : { close : changeVisibilityOfLinksToPessoaMae }
};

$paiNomeField.focusout(changeVisibilityOfLinksToPessoaPai);
$maeNomeField.focusout(changeVisibilityOfLinksToPessoaMae);

// --------------------------------- SCRIPTS ENDEREÇAMENTO ---------------------------------------- //

function hideEnderecoFields(){
  if($j('#cep_').val()){

    if ($j('#bairro_id').val())
      bloqueiaCadastroBairro();
    else
      bloqueiaBuscaBairro();

    if ($j('#logradouro_id').val())
      bloqueiaCadastroLogradouro();
    else
      bloqueiaBuscaLogradouro();

  }else{

    $j('#bairro').closest('tr').hide();
    $j('#logradouro').closest('tr').hide();
  }
}


// Campo CEP

var handleGetCep = function(dataResponse) {

  if (dataResponse['cep']){
    $j('#municipio_id').val(dataResponse['idmun']);
    $j('#municipio_municipio').val(dataResponse['idmun'] + ' - ' + dataResponse['nome'] + ' (' + dataResponse['sigla_uf'] + ')');
    $j('#bairro_id').val(dataResponse['idbai']);
    $j('#bairro_bairro').val(dataResponse['nome_bairro']+' / Zona '+(dataResponse['zona_localizacao'] == 1 ? 'Urbana' : 'Rural'));
    $j('#logradouro_id').val(dataResponse['idlog']);
    $j('#logradouro_logradouro').val(dataResponse['tipo_logradouro']+' '+dataResponse['nome_logradouro']);

  }else{
    $j('#municipio_id').val('');
    $j('#municipio_municipio').val('');
    $j('#bairro_id').val('');
    $j('#bairro_bairro').val('');
    $j('#logradouro_id').val('');
    $j('#logradouro_logradouro').val('');
  }

  $j('#municipio_municipio').removeAttr('disabled');
  $j('#bairro_bairro').removeAttr('disabled');
  $j('#logradouro_logradouro').removeAttr('disabled');
  $j('#bairro').removeAttr('disabled');
  $j('#zona_localizacao').removeAttr('disabled');
  $j('#idtlog').removeAttr('disabled');
  $j('#logradouro').removeAttr('disabled');
  bloqueiaCadastroBairro();
  bloqueiaCadastroLogradouro();
  fixUpPlaceholderEndereco();
}

// Caso cep seja válido dispara ajax para recuperar dados do primeiro cep encontrado
var searchCep = function() {

  var cep = $j('#cep_').val();

  if (checkCepFields(cep)) {
    var additionalVars = {
      cep : cep,
    };

    var options = {
      url      : getResourceUrlBuilder.buildUrl('/module/Api/endereco', 'primeiro_endereco_cep', additionalVars),
      dataType : 'json',
      data     : {},
      success  : handleGetCep
    };

    getResource(options);
  }else
    clearEnderecoFields();

}
// Ao digitar um cep inválido todos os campos de endereçamento são bloqueados e limpados
function clearEnderecoFields(){
  $j('#bairro').attr('disabled','disabled');
  $j('#zona_localizacao').attr('disabled','disabled');
  $j('#bairro_bairro').attr('disabled','disabled');
  $j('#logradouro_logradouro').attr('disabled','disabled');
  $j('#idtlog').attr('disabled','disabled');
  $j('#logradouro').attr('disabled','disabled');
  $j('#municipio_municipio').attr('disabled','disabled');
  $j('#bairro').val('');
  $j('#zona_localizacao').val('');
  $j('#bairro_bairro').val('');
  $j('#logradouro_logradouro').val('');
  $j('#idtlog').val('');
  $j('#logradouro').val('');
  $j('#bairro_id').val('');
  $j('#logradouro_id').val('');
  $j('#municipio_municipio').val('');
  $j('#municipio_id').val('');
}
// Verifica se o formato do cep é válido
function checkCepFields(cep) {
    var regexp = /[0-9]{5}\-[0-9]{3}/;
    var valid = regexp.test(cep);
    return valid;
}

// Eventos que escondem//apagam campos não usados na alternância entre cadastro/busca
function bloqueiaCadastroBairro(){
  if (checkCepFields($j('#cep_').val())){
    $j('#bairro').closest('tr').hide();
    $j('#bairro_bairro').closest('tr').show();
    $j('#zona_localizacao').val('');
    $j('#bairro').val('');
  }
}

function bloqueiaBuscaBairro(){
  if (checkCepFields($j('#cep_').val())){
    $j('#bairro_bairro').closest('tr').hide();
    $j('#bairro').closest('tr').show();
    $j('#bairro_bairro').val('');
    $j('#bairro_id').val('');
  }
}

function bloqueiaCadastroLogradouro(){
  if (checkCepFields($j('#cep_').val())){
    $j('#idtlog').closest('tr').hide();
    $j('#logradouro_logradouro').closest('tr').show();
    $j('#idtlog').val('');
    $j('#logradouro').val('');
  }
}

function bloqueiaBuscaLogradouro(){
  if (checkCepFields($j('#cep_').val())){
    $j('#logradouro_logradouro').closest('tr').hide();
    $j('#idtlog').closest('tr').show();
    $j('#logradouro_logradouro').val('');
    $j('#logradouro_id').val('');
  }
}

// Dispara evento para buscar CEP quando o mesmo for preenchido sem utilizar a lupa
$j('#cep_').keyup(searchCep);
$j('#cep_').change(searchCep);

// Limpa campos logradouro e bairro simpleSearch
function clearLogradouroAndBairroFields(){
  $j('#logradouro_logradouro').val('');
  $j('#logradouro_id').val('');
  $j('#bairro_bairro').val('');
  $j('#bairro_id').val('');
}

// Adiciona links para Informar/Atualizar troca entre cadastro ou busca
function addLinksEnderecamento(){
  $j('<span>') .html('ou cadastre um novo bairro')
               .attr('id', 'span-busca-bairro')
               .css('color','blue')
               .css('margin-left','5px')
               .addClass('decorated')
               .appendTo($j('#bairro_bairro').closest('td'));

  $j('<span>').html('ou busque um bairro existente')
              .attr('id', 'span-cad-bairro')
              .css('color','blue')
              .css('margin-left','5px')
              .addClass('decorated')
              .appendTo($j('#zona_localizacao').closest('td'));

  $j('<span>').html('ou cadastre um novo logradouro')
              .attr('id', 'span-busca-logradouro')
              .css('color','blue')
              .css('margin-left','5px')
              .addClass('decorated')
              .appendTo($j('#logradouro_logradouro').closest('td'));

  $j('<span>').html('ou busque logradouro existente')
              .attr('id', 'span-cad-logradouro')
              .css('color','blue')
              .css('margin-left','5px')
              .addClass('decorated')
              .appendTo($j('#idtlog').closest('td'));
}

addLinksEnderecamento();

// Dispara evento para alterar entre Cadastro/Busca
$j('#span-busca-bairro').click(bloqueiaBuscaBairro);
$j('#span-cad-bairro').click(bloqueiaCadastroBairro);
$j('#span-busca-logradouro').click(bloqueiaBuscaLogradouro);
$j('#span-cad-logradouro').click(bloqueiaCadastroLogradouro);

// Altera zebrado para não interferir quando for trocado entre cadastro/busca de bairro/logradouro
function alteraZebradoEnderacamento(){
  if ($j('#bairro').closest('td').hasClass('formmdtd'))
    $j('#bairro').closest('tr').find('td').toggleClass('formmdtd formlttd');
  else
    $j('#bairro').closest('tr').find('td').toggleClass('formlttd formmdtd');

  if ($j('#logradouro_logradouro').closest('td').hasClass('formmdtd'))
    $j('#logradouro_logradouro').closest('tr').find('td').toggleClass('formmdtd formlttd');
  else
    $j('#logradouro_logradouro').closest('tr').find('td').toggleClass('formlttd formmdtd');
}

alteraZebradoEnderacamento();

// Correções para apagarem o valor do campo ID quando for deletado o valor do simpleSearch
$j('#municipio_municipio').keyup( function(){
  if ($j('#municipio_municipio').val() == '')
    $j('#municipio_id').val('').trigger('change');
});

$j('#bairro_bairro').focusout( function(){
  if ($j('#bairro_bairro').val() == '')
    $j('#bairro_id').val('');
});

$j('#logradouro_logradouro').focusout( function(){
  if ($j('#logradouro_logradouro').val() == '')
    $j('#logradouro_id').val('');
});

/* Como os campos SimpleSearchBairro e SimpleSearchLogradouro dependem do valor do municipio_id,
   quando o mesmo for alterado dispara um evento para apagar esses campos dependentes */
$j('#municipio_id').change(clearLogradouroAndBairroFields);

function fixUpPlaceholderEndereco(){
  $j('#municipio_municipio').attr('placeholder' , 'Digite o nome de um munic\u00edpio para buscar');
  $j('#bairro_bairro').attr('placeholder' , 'Digite o nome de um bairro para buscar');
  $j('#logradouro_logradouro').attr('placeholder' , 'Digite o nome de um logradouro para buscar');
  $j('#bairro').attr('placeholder' , 'Digite o nome do novo bairro');
  $j('#logradouro').attr('placeholder' , 'Digite o nome do novo logradouro');
}

function validateEndereco(){

  var err = false;

  if (!$j('#municipio_id').val()){
    $j('#municipio_municipio').addClass('error');
    $j('#municipio_id').addClass('error');
    err = true;
  }

  if ($j('#logradouro_logradouro').closest('tr').is(':visible')){

    if (!$j('#logradouro_id').val()){
      $j('#logradouro_logradouro').addClass('error');
      $j('#logradouro_id').addClass('error');
      err = true;
    }
  }else{
    if (!$j('#logradouro').val()){
      $j('#logradouro').addClass('error');
      err = true;
    }
    if (!$j('#idtlog').val()){
      $j('#idtlog').addClass('error');
      err = true;
    }
  }
  if ($j('#bairro_bairro').closest('tr').is(':visible')){
    if (!$j('#bairro_id').val()){
      $j('#bairro_bairro').addClass('error');
      $j('#bairro_id').addClass('error');
      err = true;
    }

  }else{

    if (!$j('#bairro').val()){
      $j('#bairro').addClass('error');
      err = true;
    }

    if (!$j('#zona_localizacao').val()){
      $j('#zona_localizacao').addClass('error');
      err = true;
    }
  }

  return !err;

}

// --------------------------------- FIM SCRIPTS ENDEREÇAMENTO ---------------------------------------- //
