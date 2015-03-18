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

var handleGetPersonDetails = function(dataResponse) {
  handleMessages(dataResponse.msgs);
  $pessoaNotice.hide();

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
    $j('.pessoa-links .editar-pessoa').attr('href', '/intranet/atendidos_cad.php?cod_pessoa_fj=' + dataResponse.id)
                                      .show().css('display', 'inline');

    $submitButton.removeAttr('disabled').show();
  }

  $j('#pessoa_id').val(dataResponse.id);
  $nomeField.val(dataResponse.id + ' - ' + dataResponse.nome);

  var nomePai         = dataResponse.nome_pai;
  var nomeMae         = dataResponse.nome_mae;
  var nomeResponsavel = dataResponse.nome_responsavel;

  if (dataResponse.pai_id)
    nomePai = dataResponse.pai_id + ' - ' + nomePai;

  if (dataResponse.mae_id)
    nomeMae = dataResponse.mae_id + ' - ' + nomeMae;

  if (dataResponse.responsavel_id)
    nomeResponsavel = dataResponse.responsavel_id + ' - ' + nomeResponsavel;

  $j('#data_nascimento').val(dataResponse.data_nascimento);
  $j('#rg').val(dataResponse.rg);

  $j('#pai').val(nomePai);
  $j('#mae').val(nomeMae);
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

var updatePersonDetails = function() {
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

function afterChangePessoa(targetWindow, pessoaId) {
  targetWindow.close();

  // timeout para usuario perceber mudança
  window.setTimeout(function() {
    messageUtils.success('Pessoa alterada com sucesso', $nomeField);

    $j('#pessoa_id').val(pessoaId);
    getPersonDetails(pessoaId);

    if ($nomeField.is(':active'))
      $nomeField.focus();

  }, 500);
}


// when page is ready

(function($) {
  $(document).ready(function() {

    // pessoa

    var $pessoaActionBar  = $j('<span>').html('')
                                        .addClass('pessoa-links')
                                        .width($nomeField.outerWidth() - 12)
                                        .appendTo($nomeField.parent());

    $j('<a>').hide()
             .addClass('cadastrar-pessoa decorated')
             .attr('href', '/intranet/atendidos_cad.php')
             .attr('target', '_blank')
             .html('Cadastrar pessoa')
             .appendTo($pessoaActionBar);

    $j('<a>').hide()
             .addClass('editar-pessoa decorated')
             .attr('href', '#')
             .attr('target', '_blank')
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
            if (index>14 && index<61){
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
            if (index>60 && index<83){
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

          if (index<83 && index!=0){
            row.hide();
          }else{
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

  }); // ready
})(jQuery);