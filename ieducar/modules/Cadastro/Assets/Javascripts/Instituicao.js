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
  $arrayDocumento[$arrayDocumento.length] = $j('<div>').attr('id', id).append($j('<div>').html(stringUtils.toUtf8(titulo + ':'))
                                                                          .css({ "text-align" : "right", "float" : "left"}))
                                                                          .append($j('<span>').append($j('<a>').html(stringUtils.toUtf8('Excluir'))
                                                                                           .addClass('decorated')
                                                                                           .attr('id','link_excluir_documento_'+id)
                                                                                           .css({ "cursor": "pointer", "color" : "#B22222"})
                                                                                           .css('margin-left','10px')
                                                                                           .click({id: id}, excluirDocumento)))
                                                                          .append($j('<span>').append($j('<a>').html(stringUtils.toUtf8('Visualizar'))
                                                                                           .addClass('decorated')
                                                                                           .attr('id','link_visualizar_documento_'+id)
                                                                                           .attr('target','_blank')
                                                                                           .attr('href',url)
                                                                                           .css({ "cursor": "pointer", "color" : "#556B2F"})
                                                                                           .css('margin-left','10px')))
                                                                          .insertAfter($j('#aviso_formato'));

}

var $loadingDocumento =  $j('<img>').attr('src', 'imagens/indicator.gif')
                                      .css('margin-top', '3px')
                                      .hide()
                                      .insertAfter($j('#aviso_formato'));

// when page is ready

(function($) {
  $(document).ready(function() {

    var titulo = $j('#titulo_documento').val();
    $j('#documento').on('change', prepareUploadDocumento);

    function prepareUploadDocumento(event)
    {
      $j('#documento').removeClass('error');
      uploadFilesDocumento(event.target.files);
    }


    function uploadFilesDocumento(files)
    {
      if ($j('#titulo_documento').val() !== '' ){
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
              url: '/intranet/upload_just_pdf.php?files',
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
      }else{
        alert('Favor, inserir um t\u00edtulo para este documento.');
        $j('#documento').val('').clone(true);
      }
    }


  }); // ready

  //gambiarra sinistra que funciona
})(jQuery);