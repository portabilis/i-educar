// before page is ready

$j('td .formdktd').append('<div id="tabControl"><ul><li><div id="tab1" class="alunoTab"> <span class="tabText">Dados pessoais</span></div></li><li><div id="tab2" class="alunoTab"> <span class="tabText">Outros dados</span></div></li></ul></div>');

$j('#tab1').addClass('alunoTab-active').removeClass('alunoTab');

var $idField        = $j('#id');
var $nomeField      = $j('#pessoa_nome');

var $resourceNotice = $j('<span>').html('')
                                  .addClass('error resource-notice')
                                  .hide()
                                  .width($nomeField.outerWidth() - 12)
                                  .insertBefore($idField.parent());

var $pessoaNotice = $resourceNotice.clone()
                                   .appendTo($nomeField.parent());


// hide nos campos da ficha médica
$j('.tablecadastro >tbody  > tr').each(function(index, row) {
  if (index>14 && index!=52){
    row.hide();
  }
});

// Adiciona classe para que os campos de descrição possam ser desativados
$j('#restricao_atividade_fisica, #acomp_medico_psicologico, #medicacao_especifica, #tratamento_medico, #doenca_congenita, #alergia_alimento, #alergia_medicamento, #fratura_trauma, #plano_saude').addClass('temDescricao');

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
  $j('#tipo_responsavel').val(dataResponse.tipo_responsavel).change();
  $j('#religiao_id').val(dataResponse.religiao_id);
  $j('#beneficio_id').val(dataResponse.beneficio_id);
  $j('#tipo_transporte').val(dataResponse.tipo_transporte);
  $j('#alfabetizado').attr('checked', dataResponse.alfabetizado);
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


    var msg = 'Bem vindo ao novo cadastro de alunos,<br />' +
              'Agora você pode navegar entre as abas! <br />'+
              '<b>Dúvidas?</b> Entre em contato com o suporte.';

    $j('<p>').addClass('back-to-old-version right-top-notice notice')
             .html(stringUtils.toUtf8(msg))
             .appendTo($j('#tab1').closest('td'));
  

    $j('#tab1').click( 
      function(){

        $j('.alunoTab-active').toggleClass('alunoTab-active alunoTab');
        $j('#tab1').toggleClass('alunoTab alunoTab-active')
        $j('.tablecadastro >tbody  > tr').each(function(index, row) {
          if (index>16 && index!=52){
            row.hide();
          }else{
            row.show();
          }
        });        
      }
    );  

    // Quando for clicado em ficha médica, Exibir as linhas da tabela referente.
    $j('#tab2').click( 
      function(){
        $j('.alunoTab-active').toggleClass('alunoTab-active alunoTab');
        $j('#tab2').toggleClass('alunoTab alunoTab-active')
        $j('.tablecadastro >tbody  > tr').each(function(index, row) {
          if (index>14){
            row.show();
          }else if (index>0){
            row.hide();
          }
        });

        // Esse loop desativa/ativa os campos de descrição, conforme os checkbox    
        $j('.temDescricao').each(function(i, obj) {
            $j('#desc_'+obj.id).prop('disabled', !$j('#'+obj.id).prop('checked'));                  
        });
      
      });    

    /* A seguinte função habilitam/desabilitam o campo de descrição quando for clicado 
    nos referentes checkboxs */         

    $j('.temDescricao').click(function(){
        if ($j('#'+this.id).prop('checked'))
          $j('#desc_'+this.id).removeAttr('disabled');          
        else
          $j('#desc_'+this.id).attr('disabled','disabled');          
    });

  }); // ready
})(jQuery);