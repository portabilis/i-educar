$j(document).ready(function(){

  var modalExport = '<div id="modal_export" class="modal" style="display:none; text-align:center">' +
            '<div id="modal_gif_load" style="float:left;width:100px;">' +
            ' <img src="imagens/educacenso/load_modal_educacenso.gif" width="100px" height="100px" alt="">' +
            '</div>'+
            '<div id="modal_mensagem_exportacao" style="float:right;width:300px;">'+
            ' <p style="margin-left: 20px; margin-top: 30px;font-family: verdana, arial; font-size: 18px;">Aguarde, os dados est&atilde;o sendo exportados</p>' +
            '</div>'+
            '<div id="modal_mensagem_sucesso" style="width:400px;display:none;">'+
            ' <p style=" margin-top: 20px;font-family: verdana, arial; font-size: 18px;">Exporta&ccedil;&atilde;o realizada com sucesso!</p>' +
            ' <a id="download_file" href="#" download="exportacao_usuarios_i-educar.csv" style="margin-top: 10px;font-family: verdana, arial;font-size: 14px;">Clique aqui para realizar o download</a>' +
            '</div>'+
            '</div>';

    $j("body").append(modalExport);

    $j("#btn_enviar").click(function(){
      //validações na hora de enviar para o controller
    	var instituicao = $j("#ref_cod_instituicao").val();
      var escola      = $j("#ref_cod_escola").val();
      var status      = $j("#status").val();
      var tipoUsuario = $j("#ref_cod_tipo_usuario").val();

    	if (!instituicao){
    		alert("Preencha os dados obrigat\u00f3rios antes de continuar.");
    		return;
    	}

      resetParameters();
      analisaRegistro();
    });

    var resetParameters = function() {
      falhaAnalise = false;
      $j("#registro_load").text("Analisando registro 00");
      $j("#modal_gif_load").css("display", "block");
      $j("#modal_mensagem_exportacao").css("display", "block");
      $j("#modal_mensagem_sucesso").css("display", "none");
    }

    var finishAnalysis = function() {

      var specialElementHandlers = {
          '#editor': function (element, renderer) {
              return true;
          }
      };

      $j.modal.close();

        $j("#modal_export").modal({
          escapeClose: false,
          clickClose: false,
          showClose: false
        });
        userExport();
    }

    var userExport = function(){
        var urlForUserExport = getResourceUrlBuilder.buildUrl('/module/Api/UsuarioExport', 'exportarDados', {
          instituicao : $j("#ref_cod_instituicao").val(),
          escola      : $j("#ref_cod_escola").val(),
          status      : $j("#status").val(),
          tipoUsuario : $j("#ref_cod_tipo_usuario").val()
        });

        var options = {
          url : urlForUserExport,
          dataType : 'json',
          success  : handleUserExport
        };
        getResources(options);
    };

    var handleUserExport = function(response) {
      if (response.error) {
        $j.modal.close();
        alert("Ocorreu um erro ao realizar a exporta\u00e7\u00e3o.");
        return;
      }
      //Realiza alterações na modal para mostrar resultado de sucesso
      $j("#modal_gif_load").css("display", "none");
      $j("#modal_mensagem_exportacao").css("display", "none");
      $j("#modal_mensagem_sucesso").css("display", "block");

      //Cria evento para download do arquivo de exportação
      var create = document.getElementById('download_file'), conteudo = response.conteudo;
      create.addEventListener('click', function () {
        var link = document.getElementById('download_file');
        link.href = makeTextFile(conteudo);
        $j.modal.close();
      }, false);

    };

    var textFile = null,
      makeTextFile = function (text) {
        var data = new Blob([text], {type: 'text/plain'});

        // If we are replacing a previously generated file we need to
        // manually revoke the object URL to avoid memory leaks.
        if (textFile !== null) {
          window.URL.revokeObjectURL(textFile);
        }

        textFile = window.URL.createObjectURL(data);

        return textFile;
      };

    var analisaRegistro = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/UsuarioExport', 'exportarDados', {
          instituicao : $j("#ref_cod_instituicao").val(),
          escola      : $j("#ref_cod_escola").val(),
          status      : $j("#status").val(),
          tipoUsuario : $j("#ref_cod_tipo_usuario").val()
        });
        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro
        };
        getResources(options);
    };


    var handleGetAnaliseRegistro = function(response) {
      finishAnalysis();
    };

});