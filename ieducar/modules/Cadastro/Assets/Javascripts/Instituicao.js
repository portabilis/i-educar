
//abas

$j('.tablecadastro').children().children('tr:first').children('td:first').append('<div id="tabControl"><ul><li><div id="tab1" class="instituicaoTab"> <span class="tabText">Dados gerais</span></div></li><li><div id="tab2" class="instituicaoTab"> <span class="tabText">Documenta&ccedil;&atilde;o Padr&atilde;o</span></div></li></ul></div>');
$j('.tablecadastro').children().children('tr:first').children('td:first').find('b').remove();
$j('#tab1').addClass('instituicaoTab-active').removeClass('instituicaoTab');

// Adiciona um ID à linha que termina o formulário para parar de esconder os campos
$j('.tableDetalheLinhaSeparador').closest('tr').attr('id','stop');

// Pega o número dessa linha
linha_inicial_documentacao = $j('#tr_documento').index()-1;

// hide nos campos das outras abas (deixando só os campos da primeira aba)
$j('.tablecadastro >tbody  > tr').each(function(index, row) {
  if (index>=linha_inicial_documentacao - 1){
    if (row.id!='stop')
      row.hide();
    else{
      return false;
    }
  }
});

var $arrayDocumento = [];

function inserirDocumento(url) {
  var searchPath = '../module/Api/InstituicaoDocumentacao?oper=get&resource=insertDocuments';
  var params = {instituicao_id : $j('#cod_instituicao').val(), titulo_documento : $j('#titulo_documento').val(), url_documento : url}

  $j.get(searchPath, params, function(idDocumento){
    addDocumento(idDocumento.id, $j('#titulo_documento').val(), url);
    $j('#titulo_documento').val('');
  });
}

function getDocumento() {
var searchPath = '../module/Api/InstituicaoDocumentacao?oper=get&resource=getDocuments';
  var params = {instituicao_id : $j('#cod_instituicao').val()}
  var id     = '';
  var titulo = '';
  var url    = '';

  $j.get(searchPath, params, function(data){

    var documentos = data.documentos;

    for (var i = documentos.length - 1; i >= 0; i--) {
      addDocumento(documentos[i].id, documentos[i].titulo_documento, documentos[i].url_documento);
    }
  });
}

function excluirDocumento(event){
  var searchPath = '../module/Api/InstituicaoDocumentacao?oper=get&resource=deleteDocuments';
  var params = {id : event.data['id']}

  $j.get(searchPath, params, function(deleteID){
    $j('#'+event.data['id']).hide();
  });
}

getDocumento();

function addDocumento(id, titulo, url){
  $arrayDocumento[$arrayDocumento.length] = $j('<tr>').attr('id', id).append($j('<td>').html(stringUtils.toUtf8(titulo + ':'))
                                                                          .css({ "text-align" : "right"}))
                                                                          .append($j('<td>').append($j('<a>').html(stringUtils.toUtf8('Excluir'))
                                                                                           .addClass('decorated')
                                                                                           .attr('id','link_excluir_documento_'+id)
                                                                                           .css({ "cursor": "pointer", "color" : "#B22222"})
                                                                                           .css('margin-left','10px')
                                                                                           .click({id: id}, excluirDocumento)))
                                                                          .append($j('<td>').append($j('<a>').html(stringUtils.toUtf8('Visualizar'))
                                                                                           .addClass('decorated')
                                                                                           .attr('id','link_visualizar_documento_'+id)
                                                                                           .attr('target','_blank')
                                                                                           .attr('href',url)
                                                                                           .css({ "cursor": "pointer", "color" : "#556B2F"})
                                                                                           .css('margin-left','10px')))
                                                                          .insertBefore($j('#documento'));

}

var $loadingDocumento =  $j('<img>').attr('src', 'imagens/indicator.gif')
                                      .css('margin-top', '3px')
                                      .hide()
                                      .insertAfter($j('#documento'));

// when page is ready

(function($) {
  $(document).ready(function() {


    $j('#tab1').click( 
      function(){

        $j('.instituicaoTab-active').toggleClass('instituicaoTab-active instituicaoTab');
        $j('#tab1').toggleClass('instituicaoTab instituicaoTab-active')
        $j('.tablecadastro >tbody  > tr').each(function(index, row) {
          if (index>=linha_inicial_documentacao -1){
            if (row.id!='stop')
              row.hide();    
            else
              return false;
          }else{
            if ($j('#cod_instituicao').val() != '' || $j.inArray(row.id, ['tr_deficiencias', 'tr_cod_docente_inep']) == -1)
              row.show();
          }
        });        
      }
    );  

    // Adicionais
    $j('#tab2').click( 
      function(){
        $j('.instituicaoTab-active').toggleClass('instituicaoTab-active instituicaoTab');
        $j('#tab2').toggleClass('instituicaoTab instituicaoTab-active')
        $j('.tablecadastro >tbody  > tr').each(function(index, row) {
          if (row.id!='stop'){
            if (index>=linha_inicial_documentacao -1){
              if ((index - linha_inicial_documentacao + 1) % 2 == 0){
                $j('#'+row.id).find('td').removeClass('formlttd');
                $j('#'+row.id).find('td').addClass('formmdtd');
              }else{
                $j('#'+row.id).find('td').removeClass('formmdtd');
                $j('#'+row.id).find('td').addClass('formlttd');
                
              }
              row.show();
            }else if (index>0){
              row.hide();
            }
          }else
            return false;
        });
      });

    // fix checkboxs
    $j('.tablecadastro >tbody  > tr').each(function(index, row) {
      if (index>=linha_inicial_documentacao){
        $j('#'+row.id).find('input:checked').val('on');
      }
    });

    var titulo = $j('#titulo_documento').val();
    $j('#documento').on('change', prepareUploadDocumento);

    function prepareUploadDocumento(event)
    {
      $j('#documento').removeClass('error');
      uploadFilesDocumento(event.target.files);
    }


    function uploadFilesDocumento(files)
    {
      if (files && files.length>0){
        $j('#documento').attr('disabled', 'disabled');
        $j('#btn_enviar').attr('disabled', 'disabled').val('Aguarde...');
        $loadingDocumento.show();
        messageUtils.notice('Carregando documento...');

        var data = new FormData();
        $j.each(files, function(key, value)
        {
          data.append(key, value);
        });

        $j.ajax({
            url: '/intranet/upload.php?files',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(dataResponse)
            {
              if (dataResponse.error){
                $j('#documento').val("").addClass('error');
                messageUtils.error(dataResponse.error);
              }else{
                messageUtils.success('Documento carregado com sucesso');
                $j('#documento').addClass('success');
                inserirDocumento(dataResponse.file_url);
              }

            },
            error: function()
            {
              $j('#documento').val("").addClass('error');
              messageUtils.error('Não foi possível enviar o arquivo');
            },
            complete: function()
            {
              $j('#documento').removeAttr('disabled');
              $loadingDocumento.hide();
              $j('#btn_enviar').removeAttr('disabled').val('Gravar');
            }
        });
      }
    }


  }); // ready

  //gambiarra sinistra que funciona
})(jQuery);