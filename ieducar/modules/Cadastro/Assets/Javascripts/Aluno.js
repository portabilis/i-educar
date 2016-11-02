var editar_pessoa   = false;
var person_details;
var pai_details;
var mae_details;
var pessoaPaiOuMae;

 // before page is ready

var $idField        = $j('#id');
var $nomeField      = $j('#pessoa_nome');

var $resourceNotice = $j('<span>').html('')
                                  .addClass('error resource-notice')
                                  .hide()
                                  .width($nomeField.outerWidth() - 12)
                                  .insertBefore($idField.parent());

var $pessoaNotice = $resourceNotice.clone()
                                   .appendTo($nomeField.parent());

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
                                      .attr('id', 'cadastrar-pessoa-pai-link')
                                      .html('Cadastrar pessoa')
                                      .appendTo($pessoaPaiActionBar);

var $linkToEditPessoaPai = $j('<a>').hide()
                                    .addClass('editar-pessoa-pai decorated')
                                    .attr('id', 'editar-pessoa-pai-link')
                                    .html('Editar pessoa')
                                    .appendTo($pessoaPaiActionBar);

var $linkToCreatePessoaMae = $linkToCreatePessoaPai.clone()
                                                   .removeClass('cadastrar-pessoa-pai')
                                                   .attr('id', 'cadastrar-pessoa-mae-link')
                                                   .addClass('cadastrar-pessoa-mae')
                                                   .appendTo($pessoaMaeActionBar);

var $linkToEditPessoaMae = $linkToEditPessoaPai.clone()
                                               .removeClass('editar-pessoa-pai')
                                               .addClass('editar-pessoa-mae')
                                               .attr('id', 'editar-pessoa-mae-link')
                                               .appendTo($pessoaMaeActionBar);                             




// adiciona id 'stop' na linha separadora
$j('.tableDetalheLinhaSeparador').closest('tr').attr('id','stop');
// Adiciona abas na página
$j('td .formdktd').append('<div id="tabControl"><ul><li><div id="tab1" class="alunoTab"> <span class="tabText">Dados pessoais</span></div></li><li><div id="tab2" class="alunoTab"> <span class="tabText">Ficha m\u00e9dica</span></div></li><li><div id="tab3" class="alunoTab"> <span class="tabText">Uniforme escolar</span></div></li><li><div id="tab4" class="alunoTab"> <span class="tabText">Moradia</span></div></li></ul></div>');

// Adiciona estilo de aba selecionada a primeira aba
$j('#tab1').addClass('alunoTab-active').removeClass('alunoTab');

// hide nos campos das outras abas (deixando só os campos da primeira aba)
$j('.tablecadastro >tbody  > tr').each(function(index, row) {
  if (index>14){
    if (row.id!='stop')
      row.hide();
    else
      return false;
  }
});

// Adiciona classe para que os campos de descrição possam ser desativados (checkboxs)
$j('#restricao_atividade_fisica, #acomp_medico_psicologico, #medicacao_especifica, #tratamento_medico, #doenca_congenita, #alergia_alimento, #alergia_medicamento, #fratura_trauma, #plano_saude').addClass('temDescricao');

$j('#quantidade_camiseta, #tamanho_camiseta, #quantidade_calca, #tamanho_calca, #quantidade_calcado, #tamanho_calcado, #quantidade_bermuda, #tamanho_bermuda, #quantidade_saia, #tamanho_saia, #quantidade_meia, #tamanho_meia, #tamanho_blusa_jaqueta, #quantidade_blusa_jaqueta').addClass('uniforme');

// ajax

resourceOptions.handlePost = function(dataResponse) {
  $nomeField.attr('disabled', 'disabled');
  $j('.pessoa-links .cadastrar-pessoa').hide();

  if (! dataResponse.any_error_msg)
    window.setTimeout(function() { document.location = '/intranet/educar_aluno_det.php?cod_aluno=' + resource.id(); }, 500);
  else
    $submitButton.removeAttr('disabled').val('Gravar');
}

resourceOptions.handlePut = function(dataResponse) {
  if (! dataResponse.any_error_msg)
    window.setTimeout(function() { document.location = '/intranet/educar_aluno_det.php?cod_aluno=' + resource.id(); }, 500);
  else
    $submitButton.removeAttr('disabled').val('Gravar');
}

var tipo_resp;


resourceOptions.handleGet = function(dataResponse) {
  handleMessages(dataResponse.msgs);
  $resourceNotice.hide();

  if (dataResponse.id && ! dataResponse.ativo) {
    $submitButton.attr('disabled', 'disabled').hide();
    $deleteButton.attr('disabled', 'disabled').hide();

    var msg = "Este cadastro foi desativado em <b>"+ dataResponse.destroyed_at +
              " </b><br/>pelo usuário <b>" + dataResponse.destroyed_by + "</b>, ";

    $resourceNotice.html(stringUtils.toUtf8(msg)).slideDown('fast');

    $j('<a>').addClass('decorated')
             .attr('href', '#')
             .click(resourceOptions.enable)
             .html('reativar cadastro.')
             .appendTo($resourceNotice);
  }
  else
    $deleteButton.removeAttr('disabled').show();

  if (dataResponse.pessoa_id)
    getPersonDetails(dataResponse.pessoa_id);

  $idField.val(dataResponse.id);
  $j('#aluno_inep_id').val(dataResponse.aluno_inep_id);
  $j('#aluno_estado_id').val(dataResponse.aluno_estado_id);
  tipo_resp = dataResponse.tipo_responsavel;  
  $j('#religiao_id').val(dataResponse.religiao_id);
  $j('#beneficio_id').val(dataResponse.beneficio_id);
  $j('#tipo_transporte').val(dataResponse.tipo_transporte);
  $j('#alfabetizado').attr('checked', dataResponse.alfabetizado);

  /***********************************************
      CAMPOS DA FICHA MÉDICA
  ************************************************/

  $j('#sus').val(dataResponse.sus);

  //campos checkbox
   if (dataResponse.alergia_medicamento == 'S'){
    $j('#alergia_medicamento').attr('checked',true);  
    $j('#alergia_medicamento').val('on');   
  }   

   if (dataResponse.alergia_alimento == 'S'){
    $j('#alergia_alimento').attr('checked',true);  
    $j('#alergia_alimento').val('on');   
  }   

   if (dataResponse.doenca_congenita == 'S'){
    $j('#doenca_congenita').attr('checked',true);  
    $j('#doenca_congenita').val('on');   
  }   

   if (dataResponse.fumante == 'S'){
    $j('#fumante').attr('checked',true);  
    $j('#fumante').val('on');   
  }   

   if (dataResponse.doenca_caxumba == 'S'){
    $j('#doenca_caxumba').attr('checked',true);  
    $j('#doenca_caxumba').val('on');   
  }   

   if (dataResponse.doenca_sarampo == 'S'){
    $j('#doenca_sarampo').attr('checked',true);  
    $j('#doenca_sarampo').val('on');   
  }   

   if (dataResponse.doenca_rubeola == 'S'){
    $j('#doenca_rubeola').attr('checked',true);  
    $j('#doenca_rubeola').val('on');   
  }   

   if (dataResponse.doenca_catapora == 'S'){
    $j('#doenca_catapora').attr('checked',true);  
    $j('#doenca_catapora').val('on');   
  }   

   if (dataResponse.doenca_escarlatina == 'S'){
    $j('#doenca_escarlatina').attr('checked',true);  
    $j('#doenca_escarlatina').val('on');   
  }   

   if (dataResponse.doenca_coqueluche == 'S'){
    $j('#doenca_coqueluche').attr('checked',true);  
    $j('#doenca_coqueluche').val('on');   
  }   

   if (dataResponse.epiletico == 'S'){
    $j('#epiletico').attr('checked',true);  
    $j('#epiletico').val('on');   
  }   

   if (dataResponse.epiletico_tratamento == 'S'){
    $j('#epiletico_tratamento').attr('checked',true);  
    $j('#epiletico_tratamento').val('on');   
  }   

   if (dataResponse.hemofilico == 'S'){
    $j('#hemofilico').attr('checked',true);  
    $j('#hemofilico').val('on');   
  }   

   if (dataResponse.hipertenso == 'S'){
    $j('#hipertenso').attr('checked',true);  
    $j('#hipertenso').val('on');   
  }   

   if (dataResponse.asmatico == 'S'){
    $j('#asmatico').attr('checked',true);  
    $j('#asmatico').val('on');   
  }   

   if (dataResponse.diabetico == 'S'){
    $j('#diabetico').attr('checked',true);  
    $j('#diabetico').val('on');   
  }   

   if (dataResponse.insulina == 'S'){
    $j('#insulina').attr('checked',true);  
    $j('#insulina').val('on');   
  }   

   if (dataResponse.tratamento_medico == 'S'){
    $j('#tratamento_medico').attr('checked',true);  
    $j('#tratamento_medico').val('on');   
  }   

   if (dataResponse.medicacao_especifica == 'S'){
    $j('#medicacao_especifica').attr('checked',true);  
    $j('#medicacao_especifica').val('on');   
  }   

   if (dataResponse.acomp_medico_psicologico == 'S'){
    $j('#acomp_medico_psicologico').attr('checked',true);  
    $j('#acomp_medico_psicologico').val('on');   
  }   

   if (dataResponse.restricao_atividade_fisica == 'S'){
    $j('#restricao_atividade_fisica').attr('checked',true);  
    $j('#restricao_atividade_fisica').val('on');   
  }   

   if (dataResponse.fratura_trauma == 'S'){
    $j('#fratura_trauma').attr('checked',true);  
    $j('#fratura_trauma').val('on');   
  }   
   if (dataResponse.plano_saude == 'S'){
    $j('#plano_saude').attr('checked',true);  
    $j('#plano_saude').val('on');   
  }   
  // campos texto
  $j('#altura').val(dataResponse.altura);
  $j('#peso').val(dataResponse.peso);
  $j('#grupo_sanguineo').val(dataResponse.grupo_sanguineo);
  $j('#fator_rh').val(dataResponse.fator_rh);
  $j('#desc_alergia_medicamento').val(dataResponse.desc_alergia_medicamento);
  $j('#desc_alergia_alimento').val(dataResponse.desc_alergia_alimento);
  $j('#desc_doenca_congenita').val(dataResponse.desc_doenca_congenita);
  $j('#doenca_outras').val(dataResponse.doenca_outras);
  $j('#desc_tratamento_medico').val(dataResponse.desc_tratamento_medico);
  $j('#desc_medicacao_especifica').val(dataResponse.desc_medicacao_especifica);
  $j('#desc_acomp_medico_psicologico').val(dataResponse.desc_acomp_medico_psicologico);
  $j('#desc_restricao_atividade_fisica').val(dataResponse.desc_restricao_atividade_fisica);
  $j('#desc_fratura_trauma').val(dataResponse.desc_fratura_trauma);
  $j('#desc_plano_saude').val(dataResponse.desc_plano_saude);
  $j('#hospital_clinica').val(dataResponse.hospital_clinica);
  $j('#hospital_clinica_endereco').val(dataResponse.hospital_clinica_endereco);
  $j('#hospital_clinica_telefone').val(dataResponse.hospital_clinica_telefone);
  $j('#responsavel').val(dataResponse.responsavel);
  $j('#responsavel_parentesco').val(dataResponse.responsavel_parentesco);
  $j('#responsavel_parentesco_telefone').val(dataResponse.responsavel_parentesco_telefone);
  $j('#responsavel_parentesco_celular').val(dataResponse.responsavel_parentesco_celular);

    /***********************************************
      CAMPOS DO UNIFORME ESCOLAR
    ************************************************/

  if (dataResponse.recebeu_uniforme == 'S'){
    $j('#recebeu_uniforme').attr('checked',true);  
    $j('#recebeu_uniforme').val('on');   
  }   
  $j('#tamanho_camiseta').val(dataResponse.tamanho_camiseta);
  $j('#tamanho_calcado').val(dataResponse.tamanho_calcado);
  $j('#tamanho_saia').val(dataResponse.tamanho_saia);
  $j('#tamanho_calca').val(dataResponse.tamanho_calca);
  $j('#tamanho_meia').val(dataResponse.tamanho_meia);
  $j('#tamanho_bermuda').val(dataResponse.tamanho_bermuda);
  $j('#tamanho_blusa_jaqueta').val(dataResponse.tamanho_blusa_jaqueta);
  $j('#quantidade_camiseta').val(dataResponse.quantidade_camiseta);
  $j('#quantidade_calcado').val(dataResponse.quantidade_calcado);
  $j('#quantidade_saia').val(dataResponse.quantidade_saia);
  $j('#quantidade_calca').val(dataResponse.quantidade_calca);
  $j('#quantidade_calcado').val(dataResponse.quantidade_calcado);
  $j('#quantidade_bermuda').val(dataResponse.quantidade_bermuda);
  $j('#quantidade_meia').val(dataResponse.quantidade_meia);  
  $j('#quantidade_blusa_jaqueta').val(dataResponse.quantidade_blusa_jaqueta); 

    /***********************************************
      CAMPOS DA MORADIA
    ************************************************/  

  if (dataResponse.empregada_domestica == 'S'){
    $j('#empregada_domestica').attr('checked',true);  
    $j('#empregada_domestica').val('on');   
  }     
  if (dataResponse.automovel == 'S'){
    $j('#automovel').attr('checked',true);  
    $j('#automovel').val('on');   
  }     
  if (dataResponse.motocicleta == 'S'){
    $j('#motocicleta').attr('checked',true);  
    $j('#motocicleta').val('on');   
  }     
  if (dataResponse.computador == 'S'){
    $j('#computador').attr('checked',true);  
    $j('#computador').val('on');   
  }     
  if (dataResponse.geladeira == 'S'){
    $j('#geladeira').attr('checked',true);  
    $j('#geladeira').val('on');   
  }     
  if (dataResponse.fogao == 'S'){
    $j('#fogao').attr('checked',true);  
    $j('#fogao').val('on');   
  }     
  if (dataResponse.maquina_lavar == 'S'){
    $j('#maquina_lavar').attr('checked',true);  
    $j('#maquina_lavar').val('on');   
  }     
  if (dataResponse.microondas == 'S'){
    $j('#microondas').attr('checked',true);  
    $j('#microondas').val('on');   
  }     
  if (dataResponse.video_dvd == 'S'){
    $j('#video_dvd').attr('checked',true);  
    $j('#video_dvd').val('on');   
  }     
  if (dataResponse.televisao == 'S'){
    $j('#televisao').attr('checked',true);  
    $j('#televisao').val('on');   
  }  
  if (dataResponse.telefone == 'S'){
    $j('#telefone').attr('checked',true);  
    $j('#telefone').val('on');   
  }  
  if (dataResponse.celular == 'S'){
    $j('#celular').attr('checked',true);  
    $j('#celular').val('on');   
  }         
  if (dataResponse.agua_encanada == 'S'){
    $j('#agua_encanada').attr('checked',true);  
    $j('#agua_encanada').val('on');   
  }  
  if (dataResponse.poco == 'S'){
    $j('#poco').attr('checked',true);  
    $j('#poco').val('on');   
  }  
  if (dataResponse.energia == 'S'){
    $j('#energia').attr('checked',true);  
    $j('#energia').val('on');   
  }  
  if (dataResponse.esgoto == 'S'){
    $j('#esgoto').attr('checked',true);  
    $j('#esgoto').val('on');   
  }  
  if (dataResponse.fossa == 'S'){
    $j('#fossa').attr('checked',true);  
    $j('#fossa').val('on');   
  }         
  if (dataResponse.lixo == 'S'){
    $j('#lixo').attr('checked',true);  
    $j('#lixo').val('on');   
  }         

  $j('#quartos').val(dataResponse.quartos);   
  $j('#sala').val(dataResponse.sala);   
  $j('#copa').val(dataResponse.copa);   
  $j('#banheiro').val(dataResponse.banheiro);   
  $j('#garagem').val(dataResponse.garagem);  
  $j('#casa_outra').val(dataResponse.casa_outra);  
  $j('#quant_pessoas').val(dataResponse.quant_pessoas);  
  $j('#renda').val(dataResponse.renda);  
  $j('#moradia').val(dataResponse.moradia).change();
  $j('#material').val(dataResponse.material).change(); 
  $j('#moradia_situacao').val(dataResponse.moradia_situacao).change(); 

};

// pessoa links callbacks

var changeVisibilityOfLinksToPessoaParent = function(parentType) {
  var $nomeField  = $j(buildId(parentType + '_nome'));
  var $idField    = $j(buildId(parentType + '_id'));
  var $linkToEdit = $j('.pessoa-' + parentType + '-links .editar-pessoa-' + parentType);

  if($nomeField.val() && $idField.val()) {
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

var simpleSearchPaiOptions = {
  autocompleteOptions : { close  : changeVisibilityOfLinksToPessoaPai }
};

var simpleSearchMaeOptions = {
  autocompleteOptions : { close : changeVisibilityOfLinksToPessoaMae }
};

$paiIdField.change(changeVisibilityOfLinksToPessoaPai);
$maeIdField.change(changeVisibilityOfLinksToPessoaMae);

var handleGetPersonDetails = function(dataResponse) {
  handleMessages(dataResponse.msgs);
  $pessoaNotice.hide();
  person_details = dataResponse;

  mae_details = dataResponse.mae_details;

  pai_details = dataResponse.pai_details;

  var alunoId = dataResponse.aluno_id;

  if (alunoId && alunoId != resource.id()) {
    $submitButton.attr('disabled', 'disabled').hide();

    $pessoaNotice.html(stringUtils.toUtf8('Esta pessoa já possui o aluno código '+ alunoId +' cadastrado, ' ))
                 .slideDown('fast');

    $j('<a>').addClass('decorated')
             .attr('href', resource.url(alunoId))
             .attr('target', '_blank')
             .html('acessar cadastro.')
             .appendTo($pessoaNotice);
  }

  else {
    $j('.pessoa-links .editar-pessoa').show().css('display', 'inline');

    $submitButton.removeAttr('disabled').show();
  }

  $j('#pessoa_id').val(dataResponse.id);
  $nomeField.val(dataResponse.id + ' - ' + dataResponse.nome);

  var nomePai         = dataResponse.nome_pai;
  var nomeMae         = dataResponse.nome_mae;
  var nomeResponsavel = dataResponse.nome_responsavel;

  if (dataResponse.pai_id){
    pai_details.nome = nomePai;
    $j('#pai_nome').val(dataResponse.pai_id + ' - ' + nomePai);
    $j('#pai_id').val(dataResponse.pai_id);
  }else{
    $j('#pai_nome').val('');
    $j('#pai_id').val('');
  }

  $j('#pai_id').trigger('change');

  if (dataResponse.mae_id){
    mae_details.nome = nomeMae;
    $j('#mae_nome').val(dataResponse.mae_id + ' - ' + nomeMae);
    $j('#mae_id').val(dataResponse.mae_id);  
  }else{
    $j('#mae_nome').val('');
    $j('#mae_id').val('');  
  }

  $j('#mae_id').trigger('change');

  if (dataResponse.responsavel_id)
    nomeResponsavel = dataResponse.responsavel_id + ' - ' + nomeResponsavel;

  $j('#data_nascimento').val(dataResponse.data_nascimento);
  $j('#rg').val(dataResponse.rg);

  $j('#responsavel_nome').val(nomeResponsavel);
  $j('#responsavel_id').val(dataResponse.responsavel_id);

  // deficiencias

  $deficiencias = $j('#deficiencias');

  $j.each(dataResponse.deficiencias, function(id, nome) {
    $deficiencias.children("[value=" + id + "]").attr('selected', '');
  });

  $deficiencias.trigger('liszt:updated');

  $j('#tipo_responsavel').find('option').remove().end();
  if ( $j('#pai').val()=='' && $j('#mae').val()==''){
      $j('#tipo_responsavel').append('<option value="outra_pessoa" selected >Outra pessoa</option>');
      $j('#responsavel_nome').show();
  }else if ($j('#pai').val()==''){
      $j('#tipo_responsavel').append('<option value="mae" selected >M&atilde;e</option>');
      $j('#tipo_responsavel').append('<option value="outra_pessoa" >Outra pessoa</option>');
  } else if ($j('#mae').val()==''){
      $j('#tipo_responsavel').append('<option value="pai" selected >Pai</option>');
      $j('#tipo_responsavel').append('<option value="outra_pessoa" >Outra pessoa</option>');
  } else{
      $j('#tipo_responsavel').append('<option value="mae" selected >M&atilde;e</option>');
      $j('#tipo_responsavel').append('<option value="pai" selected >Pai</option>');
      $j('#tipo_responsavel').append('<option value="outra_pessoa" >Outra pessoa</option>');
  }
  $j('#tipo_responsavel').val(tipo_resp).change();

  // # TODO show aluno photo
  //$j('#aluno_foto').val(dataResponse.url_foto);
    canShowParentsFields();
}

var handleGetPersonParentDetails = function(dataResponse, parentType) {

    window[parentType+'_details'] = dataResponse;

    if(dataResponse.id){

      if(parentType=='mae'){
        $maeNomeField.val(dataResponse.id + ' - '+ dataResponse.nome);
        $maeIdField.val(dataResponse.id);
        changeVisibilityOfLinksToPessoaMae();
      } else {
        $paiNomeField.val(dataResponse.id + ' - '+ dataResponse.nome);
        $paiIdField.val(dataResponse.id);
        changeVisibilityOfLinksToPessoaPai();
      }
    }
}

var getPersonDetails = function(personId) {
  var additionalVars = {
    id : personId
  };

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa', additionalVars),
    dataType : 'json',
    data     : {},
    success  : handleGetPersonDetails
  };

  getResource(options);
}

var getPersonParentDetails = function(personId,parentType) {
  var additionalVars = {
    id : personId
  };

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa-parent', additionalVars),
    dataType : 'json',
    data     : {},
    success  : function(data){
      handleGetPersonParentDetails(data, parentType)
    }
  };

  getResource(options);
}

var updatePersonDetails = function() {
  canShowParentsFields();
  if ($j('#pessoa_nome').val() && $j('#pessoa_id').val())
    getPersonDetails($j('#pessoa_id').val());
  else
    clearPersonDetails();
}

var clearPersonDetails = function() {
  $j('#pessoa_id').val('');
  $j('#pai').val('');
  $j('#mae').val('');
  $j('.pessoa-links .editar-pessoa').hide();
}

// simple search options

var simpleSearchPessoaOptions = {
  autocompleteOptions : { close : updatePersonDetails /*, change : updatePersonDetails*/ }
};


// children callbacks

function afterChangePessoa(targetWindow, parentType, parentId, parentName) {
  if (targetWindow != null)
    targetWindow.close();

  var $tempIdField;
  var $tempNomeField;

  if(parentType){
    $tempIdField   = $j(buildId(parentType + '_id'));
    $tempNomeField = $j(buildId(parentType + '_nome'));
  }else{
    $tempIdField = $j('pessoa_id');
    $tempNomeField = $nomeField;
  }


  // timeout para usuario perceber mudança
  window.setTimeout(function() {
    messageUtils.success('Pessoa alterada com sucesso', $tempNomeField);

    $tempIdField.val(parentId);
    if(!parentType){
      getPersonDetails(parentId);
    }else{
      $tempNomeField.val(parentId + ' - ' +parentName);
    }

    if ($tempNomeField.is(':active'))
    $tempNomeField.focus();

    changeVisibilityOfLinksToPessoaParent(parentType);

  }, 500);
}

function afterChangePessoaParent(pessoaId, parentType) {

  $tempField = (parentType == 'pai' ? $paiNomeField : $maeNomeField);

  messageUtils.success('Pessoa '+parentType+' alterada com sucesso', $tempField);

  getPersonParentDetails(pessoaId, parentType);

  if ($tempField.is(':active'))
    $tempField.focus();
}

function canShowParentsFields(){
  if ($j('#pessoa_id').val()){
    $paiNomeField.removeAttr('disabled');
    $maeNomeField.removeAttr('disabled');
  }else{
    $paiNomeField.attr('disabled', 'true');
    $maeNomeField.attr('disabled', 'true');
  }
}

// when page is ready

(function($) {
  $(document).ready(function() {

    canShowParentsFields();

    var $pessoaActionBar  = $j('<span>').html('')
                                        .addClass('pessoa-links')
                                        .width($nomeField.outerWidth() - 12)
                                        .appendTo($nomeField.parent());

    $j('<a>').hide()
             .addClass('cadastrar-pessoa decorated')
             .attr('id', 'cadastrar-pessoa-link')
             .html('Cadastrar pessoa')
             .appendTo($pessoaActionBar);

    $j('<a>').hide()
             .addClass('editar-pessoa decorated')
             .attr('id', 'editar-pessoa-link')
             .html('Editar pessoa')
             .appendTo($pessoaActionBar);

    if (resource.isNew()) {
      $nomeField.focus();
      $j('.pessoa-links .cadastrar-pessoa').show().css('display', 'inline');
    }
    else
      $nomeField.attr('disabled', 'disabled');

    // responsavel

    var checkTipoResponsavel = function(){
      if ($j('#tipo_responsavel').val() == 'outra_pessoa')
        $j('#responsavel_nome').show();
      else
        $j('#responsavel_nome').hide();
    }

    checkTipoResponsavel();
    $j('#tipo_responsavel').change(checkTipoResponsavel); 

    
    var checkMoradia = function(){
      if($j('#moradia').val() == 'C'){
        $j('#material').show();    
        $j('#casa_outra').hide();
      }else if($j('#moradia').val() == 'O'){
        $j('#material').hide();
        $j('#casa_outra').show();
      }else{
        $j('#casa_outra').hide();
        $j('#material').hide();        
      }
    } 
    checkMoradia();
    $j('#moradia').change(checkMoradia); 


    var msg = 'Bem vindo ao novo cadastro de alunos,<br />' +
              'Agora você pode navegar entre as abas! <br />'+
              '<b>Dúvidas?</b> Entre em contato com o suporte.';

    $j('<p>').addClass('back-to-old-version right-top-notice notice')
             .html(stringUtils.toUtf8(msg))
             .appendTo($j('#tab1').closest('td'));
  

    /***********************
    EVENTOS DE CLICK EM ABAS
    ************************/

    // DADOS PESSOAIS
    $j('#tab1').click( 
      function(){

        $j('.alunoTab-active').toggleClass('alunoTab-active alunoTab');
        $j('#tab1').toggleClass('alunoTab alunoTab-active')
        $j('.tablecadastro >tbody  > tr').each(function(index, row) {
          if (index>14){
            if (row.id!='stop')
              row.hide();
            else
              return false;            
          }else{
            row.show();
          }
        });        
      }
    );  

    // FICHA MÉDICA
    $j('#tab2').click( 
      function(){
        $j('.alunoTab-active').toggleClass('alunoTab-active alunoTab');
        $j('#tab2').toggleClass('alunoTab alunoTab-active')
        $j('.tablecadastro >tbody  > tr').each(function(index, row) {
          if (row.id!='stop'){
            if (index>14 && index<62){
              row.show();
            }else if (index>0){
              row.hide();
            }
          }else
            return false;
        });
        // Esse loop desativa/ativa os campos de descrição, conforme os checkbox    
        $j('.temDescricao').each(function(i, obj) {
            $j('#desc_'+obj.id).prop('disabled', !$j('#'+obj.id).prop('checked'));                  
        });
      
      });    
    // UNIFORME
    $j('#tab3').click( 
      function(){
        $j('.alunoTab-active').toggleClass('alunoTab-active alunoTab');
        $j('#tab3').toggleClass('alunoTab alunoTab-active')
        $j('.tablecadastro >tbody  > tr').each(function(index, row) {
          if (row.id!='stop'){
            if (index>60 && index<84){
              row.show();
            }else if (index>0){
              row.hide();
            }       
          }else
            return false;
        });
        $j('.uniforme').prop('disabled',!$j('#recebeu_uniforme').prop('checked'));
      });     

    // MORADIA
    $j('#tab4').click( 
      function(){
        $j('.alunoTab-active').toggleClass('alunoTab-active alunoTab');
        $j('#tab4').toggleClass('alunoTab alunoTab-active')
        $j('.tablecadastro >tbody  > tr').each(function(index, row) {

          if (index<84 && index!=0){
            row.hide();
          }else if(index<111){
            row.show();
          }          
        });
        $j('.uniforme').prop('disabled',!$j('#recebeu_uniforme').prop('checked'));
      });   


    /* A seguinte função habilitam/desabilitam o campo de descrição quando for clicado 
    nos referentes checkboxs */         

    $j('.temDescricao').click(function(){
        if ($j('#'+this.id).prop('checked'))
          $j('#desc_'+this.id).removeAttr('disabled');          
        else{
          $j('#desc_'+this.id).attr('disabled','disabled');          
          $j('#desc_'+this.id).val('');          
        }
    });

    $j('#recebeu_uniforme').click(function(){
      if ($j('#recebeu_uniforme').prop('checked'))
        $j('.uniforme').removeAttr('disabled');          
      else{
        $j('.uniforme').attr('disabled','disabled');          
        $j('.uniforme').val('');          
      }
    });

    // MODAL pessoa-aluno

    //  Esse simplesSearch é carregado no final do arquivo, então a sua linha deve ser escondida,
    // é só campo será 'puxado' para a modal
    $j('#municipio_pessoa-aluno').closest('tr').hide();


    $j('body').append('<div id="dialog-form-pessoa-aluno" ><form><p></p><table><tr><td valign="top"><fieldset><legend>Dados b&aacute;sicos</legend><label for="nome-pessoa-aluno">Nome</label>    <input type="text " name="nome-pessoa-aluno" id="nome-pessoa-aluno" size="58" maxlength="255" class="text">    <label for="sexo-pessoa-aluno">Sexo</label>  <select class="select ui-widget-content ui-corner-all" name="sexo-pessoa-aluno" id="sexo-pessoa-aluno" ><option value="" selected>Sexo</option><option value="M">Masculino</option><option value="F">Feminino</option></select>    <label for="estado-civil-pessoa-aluno">Estado civil</label>   <select class="select ui-widget-content ui-corner-all" name="estado-civil-pessoa-aluno" id="estado-civil-pessoa-aluno"  ><option id="estado-civil-pessoa-aluno_" value="" selected>Estado civil</option><option id="estado-civil-pessoa-aluno_2" value="2">Casado(a)</option><option id="estado-civil-pessoa-aluno_6" value="6">Companheiro(a)</option><option id="estado-civil-pessoa-aluno_3" value="3">Divorciado(a)</option><option id="estado-civil-pessoa-aluno_4" value="4">Separado(a)</option><option id="estado-civil-pessoa-aluno_1" value="1">Solteiro(a)</option><option id="estado-civil-pessoa-aluno_5" value="5">Vi&uacute;vo(a)</option></select> <label for="data-nasc-pessoa-aluno"> Data de nascimento </label> <input onKeyPress="formataData(this, event);" class="" placeholder="dd/mm/yyyy" type="text" name="data-nasc-pessoa-aluno" id="data-nasc-pessoa-aluno" value="" size="11" maxlength="10" > <label for="naturalidade_pessoa-aluno"> Naturalidade </label>  </fieldset> </td><td><fieldset valign="top"> <legend>Dados do endere&ccedil;o</legend> <table></table></fieldset></td><td><fieldset ><table></table></fieldset></td></tr></table><p><a id="link_cadastro_detalhado" target="_blank">Cadastro detalhado</a></p></form></div>');

    var name = $j("#nome-pessoa-aluno"),
      sexo = $j( "#sexo-pessoa-aluno" ),
      estadocivil  = $j( "#estado-civil-pessoa-aluno" ),
      datanasc     = $j( "#data-nasc-pessoa-aluno" ),
      municipio    = $j( "#naturalidade_aluno_pessoa-aluno" ),
      municipio_id = $j( "#naturalidade_aluno_id" ),
      complemento  = $j( "#complemento" ),
      numero       = $j( "#numero" ),
      letra        = $j( "#letra" ),
      apartamento  = $j( "#apartamento" ),
      bloco        = $j( "#bloco" ),
      andar        = $j( "#andar" ),
      allFields = $j( [] ).add( name ).add( sexo ).add( estadocivil ).add(datanasc).add(municipio).add(municipio_id)
      .add(complemento).add(numero).add(letra).add(apartamento).add(bloco).add(andar);

    municipio.show().toggleClass('geral text').attr('display', 'block').appendTo('#dialog-form-pessoa-aluno tr td:first-child fieldset');

    $j('<label>').html('CEP').attr('for', 'cep_').insertBefore($j('#cep_'));
    $j('#cep_').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
    $j('<label>').html('Munic&iacute;pio').attr('for', 'municipio_municipio').insertBefore($j('#municipio_municipio'));
    $j('#municipio_municipio').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();      
    $j('<label>').html('Logradouro').attr('for', 'logradouro_logradouro').insertBefore($j('#logradouro_logradouro'));
    $j('#logradouro_logradouro').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
    $j('<label>').html('Tipo de logradouro').attr('for', 'idtlog').insertBefore($j('#idtlog'));
    $j('#idtlog').toggleClass('geral text');
    $j('<label>').html('Logradouro').attr('for', 'logradouro').insertBefore($j('#logradouro'));
    $j('#logradouro').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
    $j('<label>').html('Bairro').attr('for', 'bairro_bairro').insertBefore($j('#bairro_bairro'));
    $j('#bairro_bairro').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
    $j('<label>').html('Zona de localiza&ccedil;&atilde;o').attr('for', 'zona_localizacao').insertBefore($j('#zona_localizacao'));
    $j('#zona_localizacao').toggleClass('geral text');
    $j('<label>').html('Bairro').attr('for', 'bairro').insertBefore($j('#bairro'));
    $j('#bairro').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();

    $j('<label>').html('Complemento').attr('for', 'complemento').insertBefore($j('#complemento'));
    $j('#complemento').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(2) fieldset table').find('td').removeClass();
    $j('<label>').html('N&uacute;mero').attr('for', 'numero').insertBefore($j('#numero'));
    $j('#numero').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(3) fieldset table').find('td').removeClass();      
    $j('<label>').html('Letra').attr('for', 'letra').insertBefore($j('#letra'));
    $j('#letra').toggleClass('geral text');
    $j('<label>').html('N&ordm; de apartamento').attr('for', 'apartamento').insertBefore($j('#apartamento'));
    $j('#apartamento').toggleClass('geral text').closest('tr').show().find('td:first-child').hide().closest('tr').removeClass().appendTo('#dialog-form-pessoa-aluno tr td:nth-child(3) fieldset table').find('td').removeClass();      
    $j('<label>').html('Bloco').attr('for', 'bloco').insertBefore($j('#bloco'));
    $j('#bloco').toggleClass('geral text');
    $j('<label>').html('Andar').attr('for', 'andar').insertBefore($j('#andar'));
    $j('#andar').toggleClass('geral text');

    $j('#dialog-form-pessoa-aluno').find(':input').css('display', 'block');
    $j('#cep_').css('display', 'inline');

    $j( "#dialog-form-pessoa-aluno" ).dialog({
      autoOpen: false,
      height: 'auto',
      width: 'auto',
      modal: true,
      resizable: false,
      draggable: false,
      buttons: {
        "Gravar" : function() {
          var bValid = true;
          allFields.removeClass( "error" );

          bValid = bValid && checkLength( name, "nome", 3, 255 );
          bValid = bValid && checkSelect( sexo, "sexo");
          bValid = bValid && checkSelect( estadocivil, "estado civil");
          bValid = bValid && checkRegexp( datanasc, /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/[12][0-9]{3}$/i, "O campo data de nascimento deve ser preenchido no formato dd/mm/yyyy." );
          bValid = bValid && checkSimpleSearch( municipio, municipio_id, "munic\u00edpio");
          bValid = bValid && ($j('#cep_').val() == '' ? true : validateEndereco());

          if ( bValid ) {
            postPessoa($j('#pessoa_nome'), name.val(), sexo.val(), estadocivil.val(), datanasc.val(), municipio_id.val(), (editar_pessoa ? $j('#pessoa_id').val() : null), null);
            $j( this ).dialog( "close" );
          }
        },
        "Cancelar": function() {

            $j( this ).dialog( "close" );
        }
      },
      close: function() {

        allFields.val( "" ).removeClass( "error" );

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

    $j('body').append('<div id="dialog-form-pessoa-parent"><form><p></p><table><tr><td valign="top"><fieldset><label for="nome-pessoa-parent">Nome</label>    <input type="text " name="nome-pessoa-parent" id="nome-pessoa-parent" size="58" maxlength="255" class="text">    <label for="sexo-pessoa-parent">Sexo</label>  <select class="select ui-widget-content ui-corner-all" name="sexo-pessoa-parent" id="sexo-pessoa-parent" ><option value="" selected>Sexo</option><option value="M">Masculino</option><option value="F">Feminino</option></select>    <label for="estado-civil-pessoa-parent">Estado civil</label>   <select class="select ui-widget-content ui-corner-all" name="estado-civil-pessoa-parent" id="estado-civil-pessoa-parent"  ><option id="estado-civil-pessoa-parent_" value="" selected>Estado civil</option><option id="estado-civil-pessoa-parent_2" value="2">Casado(a)</option><option id="estado-civil-pessoa-parent_6" value="6">Companheiro(a)</option><option id="estado-civil-pessoa-parent_3" value="3">Divorciado(a)</option><option id="estado-civil-pessoa-parent_4" value="4">Separado(a)</option><option id="estado-civil-pessoa-parent_1" value="1">Solteiro(a)</option><option id="estado-civil-pessoa-parent_5" value="5">Vi&uacute;vo(a)</option></select></fieldset><p><a id="link_cadastro_detalhado_parent" target="_blank">Cadastro detalhado</a></p></form></div>');    

    $j('#dialog-form-pessoa-parent').find(':input').css('display', 'block');

    var nameParent = $j("#nome-pessoa-parent"),
      sexoParent = $j( "#sexo-pessoa-parent" ),
      estadocivilParent  = $j( "#estado-civil-pessoa-parent" ),
      allFields = $j( [] ).add( nameParent ).add( sexoParent ).add( estadocivilParent );

    $j( "#dialog-form-pessoa-parent" ).dialog({
      autoOpen: false,
      height: 'auto',
      width: 'auto',
      modal: true,
      resizable: false,
      draggable: false,
      buttons: {
        "Gravar" : function() {
          var bValid = true;
          allFields.removeClass( "ui-state-error" );

          bValid = bValid && checkLength( nameParent, "nome", 3, 255 );
          bValid = bValid && checkSelect( sexoParent, "sexo");
          bValid = bValid && checkSelect( estadocivilParent, "estado civil");

          if ( bValid ) {
            postPessoa(nameParent, nameParent.val(), sexoParent.val(), estadocivilParent.val(), null, null, (editar_pessoa ? $j('#'+pessoaPaiOuMae+'_id').val() : null), pessoaPaiOuMae);
            $j( this ).dialog( "close" );
          }
        },
        "Cancelar": function() {

            $j( this ).dialog( "close" );
        }
      },
      close: function() {

        allFields.val( "" ).removeClass( "error" );

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

    $j('#link_cadastro_detalhado').click(function(){
      $j( "#dialog-form-pessoa-aluno" ).dialog( "close" );
    });

    $j('#link_cadastro_detalhado_parent').click(function(){
      $j( "#dialog-form-pessoa-parent" ).dialog( "close" );
    });

    $j("#cadastrar-pessoa-link").click(function() {

        $j('#link_cadastro_detalhado').attr('href','/intranet/atendidos_cad.php');

        $j( "#dialog-form-pessoa-aluno" ).dialog( "open" );

        $j('#cep_').val('');
        clearEnderecoFields();
        hideEnderecoFields();

        $j(".ui-widget-overlay").click(function(){
          $j(".ui-dialog-titlebar-close").trigger('click');
        });

        $j('#nome-pessoa-aluno').focus();

        $j('#dialog-form-pessoa-aluno form p:first-child').html('Cadastrar pessoa aluno').css('margin-left', '0.75em');

        editar_pessoa = false;

    });

    $j("#editar-pessoa-link").click(function() {

        $j('#link_cadastro_detalhado').attr('href','/intranet/atendidos_cad.php?cod_pessoa_fj=' + person_details.id);
        clearEnderecoFields();

        name.val(person_details.nome);
        datanasc.val(person_details.data_nascimento);
        estadocivil.val(person_details.estadocivil);
        sexo.val(person_details.sexo);

        if (person_details.idmun_nascimento){

          $j('#naturalidade_aluno_id').val(person_details.idmun_nascimento);
          $j('#naturalidade_aluno_pessoa-aluno').val(person_details.idmun_nascimento+' - '+person_details.municipio_nascimento+' ('+person_details.sigla_uf_nascimento+')');

        }

        $j('#cep_').val(person_details.cep);

        if ($j('#cep_').val()){

          $j('#municipio_municipio').removeAttr('disabled');
          $j('#bairro_bairro').removeAttr('disabled');
          $j('#logradouro_logradouro').removeAttr('disabled');
          $j('#bairro').removeAttr('disabled');
          $j('#zona_localizacao').removeAttr('disabled');
          $j('#idtlog').removeAttr('disabled');
          $j('#logradouro').removeAttr('disabled');

          $j('#complemento').val(person_details.complemento);
          $j('#numero').val(person_details.numero);
          $j('#letra').val(person_details.letra);
          $j('#apartamento').val(person_details.apartamento);
          $j('#bloco').val(person_details.bloco);
          $j('#andar').val(person_details.andar);

          $j('#municipio_id').val(person_details.idmun);

          $j('#municipio_municipio').val(person_details.idmun+' - '+person_details.municipio+' ('+person_details.sigla_uf+')');

          if (person_details.idbai && person_details.idlog){

            $j('#bairro_id').val(person_details.idbai);
            $j('#logradouro_id').val(person_details.idlog);
            $j('#bairro_bairro').val(person_details.bairro + ' / Zona '+(person_details.zona_localizacao == "1" ? "Urbana" : "Rural"));
            $j('#logradouro_logradouro').val($j("#idtlog option[value='"+person_details.idtlog+"']").text() + ' '+person_details.logradouro);

          }else{

            $j('#bairro').val(person_details.bairro);
            $j('#logradouro').val(person_details.logradouro);
            $j('#idtlog').val(person_details.idtlog);
            $j('#zona_localizacao').val(person_details.zona_localizacao);

          }
        }

        hideEnderecoFields();

        $j( "#dialog-form-pessoa-aluno" ).dialog("open");

        $j(".ui-widget-overlay").click(function(){
          $j(".ui-dialog-titlebar-close").trigger('click');
        });

        $j('#nome-pessoa-aluno').focus();

        $j('#dialog-form-pessoa-aluno form p:first-child').html('Editar pessoa aluno').css('margin-left', '0.75em');

        editar_pessoa = true;

    });

    $j("#cadastrar-pessoa-pai-link").click(function() {

        if($j('#pessoa_id').val()){

          openModalParent('pai');

        }else{

          alertSelecionarPessoaAluno();
        }

    });


    $j("#cadastrar-pessoa-mae-link").click(function() {

        if($j('#pessoa_id').val()){

          openModalParent('mae');

        }else{
          alertSelecionarPessoaAluno();
        }

    });

    $j("#editar-pessoa-pai-link").click(function() {

        if($j('#pessoa_id').val()){

          openEditModalParent('pai');

        }

    });


    $j("#editar-pessoa-mae-link").click(function() {

        if($j('#pessoa_id').val()){

          openEditModalParent('mae');

        }

    });

    function alertSelecionarPessoaAluno(){
      messageUtils.error('Primeiro cadastre/selecione uma pessoa para o aluno. ');
    }

    function openModalParent(parentType){

      $j('#link_cadastro_detalhado_parent').attr('href','/intranet/atendidos_cad.php?parent_type='+parentType);

      $j( "#dialog-form-pessoa-parent" ).dialog( "open" );

      $j(".ui-widget-overlay").click(function(){
        $j(".ui-dialog-titlebar-close").trigger('click');
      });

      $j('#nome-pessoa-parent').focus();

      $j('#dialog-form-pessoa-parent form p:first-child').html('Cadastrar pessoa '+(parentType == 'mae' ? 'm&atilde;e' : parentType)).css('margin-left', '0.75em');
      pessoaPaiOuMae = parentType;

      editar_pessoa = false;

    }

    function openEditModalParent(parentType){

      $j('#link_cadastro_detalhado_parent').attr('href','/intranet/atendidos_cad.php?cod_pessoa_fj='+ $j('#'+parentType+'_id').val() +'&parent_type='+parentType);

      $j( "#dialog-form-pessoa-parent" ).dialog( "open" );

      $j(".ui-widget-overlay").click(function(){
        $j(".ui-dialog-titlebar-close").trigger('click');
      });

      $j('#nome-pessoa-parent').focus();

      nameParent.val(window[parentType+'_details'].nome);
      estadocivilParent.val(window[parentType+'_details'].estadocivil);
      sexoParent.val(window[parentType+'_details'].sexo);

      $j('#dialog-form-pessoa-parent form p:first-child').html('Editar pessoa '+(parentType == 'mae' ? 'm&atilde;e' : parentType)).css('margin-left', '0.75em');
  
      pessoaPaiOuMae = parentType;

      editar_pessoa = true;
    }

    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "error" );
        messageUtils.error( "Tamanho do " + n + " deve ter entre " +
          min + " e " + max + " caracteres." );
        return false;
      } else {
        return true;
      }
    }

    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "error" );
        messageUtils.error( n );
        return false;
      } else {
        return true;
      }
    }

    function checkSelect(comp, name) {

      if ( comp.val() == '') {
        comp.addClass( "error" );
        messageUtils.error( "Selecione um "+name+"." );
        return false;
      } else {
        return true;
      }

    }

    function checkSimpleSearch(comp, hiddenComp, name) {

      if ( hiddenComp.val() == '') {
        comp.addClass( "error" );
        messageUtils.error( "Selecione um "+name+"." );
        return false;
      } else {
        return true;
      }

    }

    $j('#pai_id').change( function(){ getPersonParentDetails($j(this).val(), 'pai') });
    $j('#mae_id').change( function(){ getPersonParentDetails($j(this).val(), 'mae' ) });
  }); // ready

  function postPessoa($pessoaField, nome, sexo, estadocivil, datanasc, naturalidade, pessoa_id, parentType) {

      var data = {
        nome             : nome,
        sexo             : sexo,
        estadocivil      : estadocivil,
        datanasc         : datanasc,
        naturalidade     : naturalidade,
        pessoa_id        : pessoa_id
      };

      var options = {
        url : postResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa', {}),
        dataType : 'json',
        data : data,
        success : function(dataResponse) {
          if(parentType=='mae')
            afterChangePessoaParent(dataResponse.pessoa_id, 'mae');
          else if(parentType=='pai')
            afterChangePessoaParent(dataResponse.pessoa_id, 'pai');
          else
            postEnderecoPessoa(dataResponse.pessoa_id);
        }
      };

      postResource(options);

  }

  function postEnderecoPessoa(pessoa_id) {

    if (checkCepFields($j('#cep_').val())){

      var data = {
        pessoa_id          : pessoa_id,
        cep                : $j('#cep_').val(),
        municipio_id       : $j('#municipio_id').val(),
        bairro             : $j('#bairro').val(),
        bairro_id          : $j('#bairro_id').val(),
        zona_localizacao   : $j('#zona_localizacao').val(),
        logradouro         : $j('#logradouro').val(),
        idtlog             : $j('#idtlog').val(),
        logradouro_id      : $j('#logradouro_id').val(),
        apartamento        : $j('#apartamento').val(),
        complemento        : $j('#complemento').val(),
        numero             : $j('#numero').val(),
        letra              : $j('#letra').val(),
        bloco              : $j('#bloco').val(),
        andar              : $j('#andar').val()
      };

      var options = {
        url : postResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa-endereco', {}),
        dataType : 'json',
        data : data,
        success : function(dataResponse) {
          afterChangePessoa(null,null,pessoa_id);
        }
      };

      postResource(options);

    }else{
      afterChangePessoa(null,null,pessoa_id);
    }

  }

})(jQuery);