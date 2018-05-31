$j(document).ready(function(){

  let currentDateString = () => new Date().toLocaleString('pt-BR');

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
            ' <a id="download_file" href="#" style="margin-top: 10px;font-family: verdana, arial;font-size: 14px;">Clique aqui para realizar o download</a>' +
            '</div>'+
            '</div>';


    var headerPaginaResposta = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>'+'Análise exportação'+'</title>'+
            '<link rel="stylesheet" href="../modules/Educacenso/Assets/Stylesheets/educacensoPdf.css?v=2"></head><body>'+
            '<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">'+
            `<p class="date-info">Data da geração: ${currentDateString()}</p>`+
						'<div id="content">'+
						'  <h1 class="title">'+"Análise de exportação"+'</h1>'+
						'</div>'+
						'<div id="editor"></div>';

    var paginaResposta = "";
    var falhaAnalise;
    var fase2;

    $j("body").append(modalLoad);
    $j("body").append(modalExport);

    var iniciaAnalise = function() {

    	var escola = $j("#ref_cod_escola").val();
    	var dataIni = $j("#data_ini").val();
    	var dataFim = $j("#data_fim").val();
      fase2 = ($j("#fase2").val() == "true");

    	if (!escola || !dataIni || !dataFim){
    		alert("Preencha os dados obrigat\u00f3rios antes de continuar.");
    		return;
    	} else if (!isValidDate(dataIni) || !isValidDate(dataFim)) {
        alert("A data informada \u00e9 inv\u00e1lida.");
        return;
      }

      resetParameters();

      if (fase2) {
        $j("#modal_load").modal({
          escapeClose: false,
          clickClose: false,
          showClose: false
        });
        analisaRegistro89();
      } else {
        $j("#modal_load").modal({
          escapeClose: false,
          clickClose: false,
          showClose: false
        });
        analisaRegistro00();
      }
    }

    function isValidDate(s) {
      var bits = s.split('/');
      var d = new Date(bits[2], bits[1] - 1, bits[0]);
      return d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[0]);
    }

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

      $j("#modal_export").modal({
        escapeClose: false,
        clickClose: false,
        showClose: false
      });

      if (falhaAnalise) {
        document.write(paginaResposta);
      } else if (fase2) {
        educacensoExportFase2();
      } else {
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
          htmlAnalise += `<p>
                            <a class="educacenso-link-path"
                               href="`+response.mensagens[i].linkPath+`"
                               target="_new">`
                              +response.mensagens[i].path+
                            `</a>
                          </p>`;

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

    var educacensoExportFase2 = function(){
        var urlForEducacensoExport = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoExport', 'educacenso-export-fase2', {
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
        downloadExportFile(conteudo);
        $j.modal.close();
      }, false);

    };

    var downloadExportFile = function(conteudo){
      //Cria um form para efetuar post na janela de exportacao
      var form = document.createElement("form");
      form.setAttribute("method", "POST");
      form.setAttribute("action", "educar_exportacao_educacenso.php");

      var hiddenField = document.createElement("input");
      hiddenField.setAttribute("type", "hidden");
      hiddenField.setAttribute("name", "exportacao");
      hiddenField.setAttribute("value", conteudo);

      form.appendChild(hiddenField);

      document.body.appendChild(form);
      form.submit();
    }

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
          ano    : $j("#ano").val(),
          data_fim : $j("#data_fim").val()
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
      $j("#registro_load").text("Analisando registro 40");
      analisaRegistro40();
    };

    var analisaRegistro40 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-40', {
          escola : $j("#ref_cod_escola").val(),
          ano    : $j("#ano").val(),
          data_fim : $j("#data_fim").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro40
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro40 = function(response) {
      montaHtmlRegistro(response);
      $j("#registro_load").text("Analisando registro 50");
      analisaRegistro50();
    };

    var analisaRegistro50 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-50', {
          escola : $j("#ref_cod_escola").val(),
          ano    : $j("#ano").val(),
          data_fim : $j("#data_fim").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro50
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro50 = function(response) {
      montaHtmlRegistro(response);
      $j("#registro_load").text("Analisando registro 51");
      analisaRegistro51();
    };

    var analisaRegistro51 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-51', {
          escola : $j("#ref_cod_escola").val(),
          ano    : $j("#ano").val(),
          data_fim : $j("#data_fim").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro51
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro51 = function(response) {
      montaHtmlRegistro(response);
      $j("#registro_load").text("Analisando registro 60");
      analisaRegistro60();
    };

    var analisaRegistro60 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-60', {
          escola   : $j("#ref_cod_escola").val(),
          ano      : $j("#ano").val(),
          data_ini : $j("#data_ini").val(),
          data_fim : $j("#data_fim").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro60
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro60 = function(response) {
      montaHtmlRegistro(response);
      $j("#registro_load").text("Analisando registro 70");
      analisaRegistro70();
    };

    var analisaRegistro70 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-70', {
          escola   : $j("#ref_cod_escola").val(),
          ano      : $j("#ano").val(),
          data_ini : $j("#data_ini").val(),
          data_fim : $j("#data_fim").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro70
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro70 = function(response) {
      montaHtmlRegistro(response);
      $j("#registro_load").text("Analisando registro 80");
      analisaRegistro80();
    };

    var analisaRegistro80 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-80', {
          escola   : $j("#ref_cod_escola").val(),
          ano      : $j("#ano").val(),
          data_ini : $j("#data_ini").val(),
          data_fim : $j("#data_fim").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro80
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro80 = function(response) {
      montaHtmlRegistro(response);
      finishAnalysis();
    };

    var analisaRegistro89 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-89', {
          escola   : $j("#ref_cod_escola").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',

          success  : handleGetAnaliseRegistro89
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro89 = function(response) {
      montaHtmlRegistro(response);
      $j("#registro_load").text("Analisando registro 90");
      analisaRegistro90();
    };

    var analisaRegistro90 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-90', {
          escola   : $j("#ref_cod_escola").val(),
          ano      : $j("#ano").val(),
          data_ini : $j("#data_ini").val(),
          data_fim : $j("#data_fim").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro90
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro90 = function(response) {
      montaHtmlRegistro(response);
      $j("#registro_load").text("Analisando registro 91");
      analisaRegistro91();
    };

    var analisaRegistro91 = function(){
        var urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', 'registro-91', {
          escola   : $j("#ref_cod_escola").val(),
          ano      : $j("#ano").val()
        });

        var options = {
          url : urlForGetAnaliseRegistro,
          dataType : 'json',
          success  : handleGetAnaliseRegistro91
        };
        getResources(options);
    };

    var handleGetAnaliseRegistro91 = function(response) {
      montaHtmlRegistro(response);
      finishAnalysis();
    };
  iniciaAnalise();
});