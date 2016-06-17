$j(document).ready(function(){

	var modalLoad = '<div id="modal_load" class="modal" style="display:none;">' +
				  	'<div style="float:left;width:100px;">' +
  					'	<img src="imagens/educacenso/load_modal_educacenso.gif" width="100px" height="100px" alt="">' +
  					'</div>'+
  					'<div style="float:right;width:300px;">'+
  					'	<p style="margin-left: 20px; margin-top: 30px;font-family: verdana, arial; font-size: 18px;">Analisando as informa&ccedil;&otilde;es</p>' +
  					'	<p id="registro_load" style="margin-left: 20px; margin-top: 10px;font-family: verdana, arial; font-size: 10px;">Analisando registro 00</p>' +
  					'</div>'+
  					'</div>';

  var modalExport = '<div id="modal_export" class="modal" style="display:none; text-align:center">' +
            '<div id="modal_gif_load" style="float:left;width:100px;">' +
            ' <img src="imagens/educacenso/load_modal_educacenso.gif" width="100px" height="100px" alt="">' +
            '</div>'+
            '<div id="modal_mensagem_exportacao" style="float:right;width:300px;">'+
            ' <p style="margin-left: 20px; margin-top: 30px;font-family: verdana, arial; font-size: 18px;">Aguarde, os dados est&atilde;o sendo exportados</p>' +
            '</div>'+
            '<div id="modal_mensagem_sucesso" style="width:400px;display:none;">'+
            ' <p style=" margin-top: 20px;font-family: verdana, arial; font-size: 18px;">Exporta&ccedil;&atilde;o realizada com sucesso.</p>' +
            ' <a id="download_file" href="#" download="exportacao.txt" style="margin-top: 10px;font-family: verdana, arial;font-size: 14px;">Clique aqui para realizar o download</a>' +
            '</div>'+
            '</div>';

    var headerPaginaResposta = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>'+stringUtils.toUtf8('Análise exportação')+'</title>'+
            '<link rel="stylesheet" href="../modules/Educacenso/Assets/Stylesheets/educacensoPdf.css"></head><body>'+
						'<div id="content">'+
						'  <h1 class="title">'+stringUtils.toUtf8("Análise de exportação")+'</h1>'+
						'</div>'+
						'<div id="editor"></div>';

    var paginaResposta = "";
    var falhaAnalise;

    $j("body").append(modalLoad);
    $j("body").append(modalExport);

    $j("#btn_enviar").click(function(){

    	var escola = $j("#ref_cod_escola").val();
    	var dataIni = $j("#data_ini").val();
    	var dataFim = $j("#data_fim").val();

    	if (!escola || !dataIni || !dataFim){
    		alert("Preencha os dados obrigat\u00f3rios antes de continuar.");
    		return;
    	}

      $j("#modal_load").modal({
        escapeClose: false,
        clickClose: false,
        showClose: false
      });

      resetParameters();
      analisaRegistro00();
    });

    var resetParameters = function() {
      paginaResposta = headerPaginaResposta;
      falhaAnalise = false;
      $j("#registro_load").text("Analisando registro 00");
      $j("#modal_gif_load").css("display", "block");
      $j("#modal_mensagem_exportacao").css("display", "block");
      $j("#modal_mensagem_sucesso").css("display", "none");
    }

    var finishAnalysis = function() {
      paginaResposta += '</body></html>';

      var specialElementHandlers = {
          '#editor': function (element, renderer) {
              return true;
          }
      };

      $j.modal.close();

      if (falhaAnalise) {
        var newPage = window.open();
        newPage.document.write(paginaResposta);
      } else {
        $j("#modal_export").modal({
          escapeClose: false,
          clickClose: false,
          showClose: false
        });
        educacensoExport();
      }
    }

    var montaHtmlRegistro = function(response) {
      var htmlAnalise = "<h2 class='subtitle'>"+response.title+"</h2>";

      if (response.any_error_msg) {
        htmlAnalise += "<p class='errorMessage'>"+response.msgs[0].msg+"</p>";
        falhaAnalise = true;
      } else {
        //Monta uma lista em HTML com as mensagens retornadas da análise
        htmlAnalise += "<ul>";
        for (i = 0; i < response.mensagens.length; i++) {
          htmlAnalise += "<li>"+response.mensagens[i].text+"</li>";
          htmlAnalise += "<p>"+response.mensagens[i].path+"</p>";

          if (response.mensagens[i].fail) falhaAnalise = true;
        }
        htmlAnalise +="</ul>";
      }
      paginaResposta += htmlAnalise;
    };

    var educacensoExport = function(){
        var urlForEducacensoExport = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoExport', 'educacenso-export', {
          escola   : $j("#ref_cod_escola").val(),
          ano      : $j("#ano").val(),
          data_ini : $j("#data_ini").val(),
          data_fim : $j("#data_fim").val()
        });

        var options = {
          url : urlForEducacensoExport,
          dataType : 'json',
          success  : handleEducacensoExport
        };
        getResources(options);
    };

    var handleEducacensoExport = function(response) {
      if (response.error) {
        console.log(response.mensagem);
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

    var analisaRegistro00 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-00', {
          escola : $j("#ref_cod_escola").val(),
          ano    : $j("#ano").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro00
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro00 = function(response) {
      montaHtmlRegistro(response);
      $j("#registro_load").text("Analisando registro 10");
      analisaRegistro10();
    };

    var analisaRegistro10 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-10', {
          escola : $j("#ref_cod_escola").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro10
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro10 = function(response) {
      montaHtmlRegistro(response);
      $j("#registro_load").text("Analisando registro 20");
      analisaRegistro20();
    };

    var analisaRegistro20 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-20', {
          escola : $j("#ref_cod_escola").val(),
          ano    : $j("#ano").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro20
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro20 = function(response) {
      montaHtmlRegistro(response);
      $j("#registro_load").text("Analisando registro 30");
      analisaRegistro30();
    };

    var analisaRegistro30 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-30', {
          escola : $j("#ref_cod_escola").val(),
          ano    : $j("#ano").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro30
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro30 = function(response) {
      montaHtmlRegistro(response);
      finishAnalysis();
    };

});