$j(document).ready(function(){
  const recordsFirstStep = [
    '00',
    '10',
    '20',
    '30',
    '40',
    '50',
    '60'
  ];

  const recordsFirstStepNotActive = [
    '00',
    '30',
    '40'
  ];

  const recordsSecondStep = [
    '89',
    '90',
    '91'
  ];

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
             '<div id="modal_mensagem_desabilitado" style="width:400px;display:none; text-align:left">'+
             ' <p style="margin-left: 0px; margin-top: 30px;font-family: verdana, arial; font-size: 18px; font-weight: bold;">Parabéns! Até o momento todos os dados da sua escola, foram validados com sucesso!</p>' +
             ' <p style="margin-left: 0px; margin-top: 30px;font-family: verdana, arial; font-size: 14px; font-weight: bold;">Mas porque o EducaSis não baixou o arquivo de exportação da minha escola?</p>' +
             ' <p style="margin-left: 0px; margin-top: 30px;font-family: verdana, arial; font-size: 14px;">O Censo Escolar atualizou a Tabela de Curso de Formação Superior de 2020. Novos cursos foram adicionados e outros foram modificados. Nós estamos trabalhando para atualizar esses cursos no i-Educar.</p>' +
             '<img style="max-width:100%;" src="/intranet/imagens/cursos_superiores_servidor.png"/>' +
             ' <p style="margin-left: 0px; margin-top: 30px;font-family: verdana, arial; font-size: 14px;">Mas não se preocupe, pois vamos concluir essas alterações até o dia <strong>05/06</strong> e o arquivo será liberado novamente. Se concluirmos antes, iremos te avisar!</p>' +
             '</div>'+
            '</div>';


    var headerPaginaResposta = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>'+'Análise exportação'+'</title>'+
            '<link rel="stylesheet" href="../modules/Educacenso/Assets/Stylesheets/educacensoPdf.css?v=5"></head><body>'+
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
      fase2 = ($j("#fase2").val() == "true");

    	if (!escola){
    		alert("Preencha os dados obrigat\u00f3rios antes de continuar.");
    		return;
    	}

      resetParameters();

      if (fase2) {
        $j("#modal_load").modal({
          escapeClose: false,
          clickClose: false,
          showClose: false
        });
        analyseRecords(recordsSecondStep);
      } else {
        $j("#modal_load").modal({
          escapeClose: false,
          clickClose: false,
          showClose: false
        });

        $j.getJSON(
          getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', `valida-instituicao`, {
            instituicao: $j("#ref_cod_instituicao").val()
          })
        ).done((data) => {
          if (data['valid']) {
            analyseRecords($j('#escola_em_andamento').val() == '1' ? recordsFirstStep : recordsFirstStepNotActive);
          } else {
            makeInvalidInstitutionWarning();
            falhaAnalise = true;
            finishAnalysis();
          }
        });
      }
    }

    let analyseRecords = (records) => {
      let record = records.shift();
      $j("#registro_load").text(`Analisando registro ${record}`);
      let urlForGetAnaliseRegistro = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoAnalise', `registro-${record}`, {
        escola: $j("#ref_cod_escola").val(),
        ano: $j("#ano").val()
      });

      let options = {
        url: urlForGetAnaliseRegistro,
        dataType: 'json',
        success: (successData) => {
          montaHtmlRegistro(successData);
          if (records.length === 0) {
            finishAnalysis();
          } else {
            analyseRecords(records);
          }
        }
      };
      getResources(options);
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
      $j("#modal_mensagem_disabled").css("display", "none");
    }

    var finishAnalysis = function() {
      paginaResposta += '</body></html>';

      var specialElementHandlers = {
          '#editor': function (element, renderer) {
              return true;
          }
      };

      $j("#modal_export").modal({
        escapeClose: true,
        clickClose: true,
        showClose: true
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

    let makeInvalidInstitutionWarning = () => {
      const instituicaoId = $j('#ref_cod_instituicao').val();
      paginaResposta += `<div class="educacenso-institution-warning-container">
                            <p> Para exportar todos os registros corretamente é necessário preencher o campo <b>Data de referência do Educacenso</b>, apresentado no cadastro da Instituição na aba Parâmetros. A data de referência normalmente é correspondente à última quarta-feira do mês de maio do ano atual.</p>
                            <a class="educacenso-link-path"
                               href="/intranet/educar_instituicao_cad.php?cod_instituicao=${instituicaoId}"
                               target="_new">
                               (Escola > Cadastros > Instituição > Editar > Aba: Parâmetros > Campo: Data de referência do Educacenso)
                            </a>
                          </div>`;
    };

    var educacensoExport = function(){
        var urlForEducacensoExport = getResourceUrlBuilder.buildUrl('/module/Api/EducacensoExport', 'educacenso-export', {
          escola   : $j("#ref_cod_escola").val(),
          ano      : $j("#ano").val()
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
          ano      : $j("#ano").val()
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

      if($j('#enable_export').val() == '1') {
        $j("#modal_mensagem_exportacao").css("display", "none");
        $j("#modal_mensagem_sucesso").css("display", "block");
        $j("#modal_mensagem_desabilitado").css("display", "none");
      } else {
        $j("#modal_mensagem_exportacao").css("display", "none");
        $j("#modal_mensagem_sucesso").css("display", "none");
        $j("#modal_mensagem_desabilitado").css("display", "block");
      }

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

      var escola = document.createElement("input");
      escola.setAttribute("type", "hidden");
      escola.setAttribute("name", "escola");
      escola.setAttribute("value",  $j("#ref_cod_escola").val());
      form.appendChild(escola);

      document.body.appendChild(form);
      form.submit();
    }

  iniciaAnalise();
});
