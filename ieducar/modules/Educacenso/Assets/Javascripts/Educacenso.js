$j(document).ready(function(){

	var modalLoad = '<div id="modal_load" class="modal" style="display:none;">' +
				  	'<div style="float:left;width:100px;">' +
  					'	<img src="imagens/educacenso/load_modal_educacenso.gif" width="100px" height="100px" alt="">' +
  					'</div>'+
  					'<div style="float:right;width:300px;">'+
  					'	<p style="margin-left: 20px; margin-top: 30px;font-family: verdana, arial; font-size: 18px;">Analisando as informa&ccedil;&otilde;es</p>' +
  					'	<p id="registro_load" style="margin-left: 20px; margin-top: 10px;font-family: verdana, arial; font-size: 10px;">Registro 00 teste</p>' +
  					'</div>'+
  					'</div>';

    var headerPaginaResposta = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Analize exportação</title></head><body>'+
						 '<div id="content">'+
						 '  <h1>Analise de exporta&ccedil;&atilde;o</h1>'+
						 '</div>'+
						 '<div id="editor"></div>';

    var paginaResposta = "";

    $j("body").append(modalLoad);
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

      paginaResposta = headerPaginaResposta;
      analisaRegistro00();
    });

    var finishAnalysis = function() {
      paginaResposta += '</body></html>';

      var doc = new jsPDF();
      var specialElementHandlers = {
          '#editor': function (element, renderer) {
              return true;
          }
      };

      $j.modal.close();
      doc.fromHTML(paginaResposta, 15, 15, {
          'width': 170,
              'elementHandlers': specialElementHandlers
      });
      doc.output('dataurlnewwindow');
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

      var htmlAnalise00 = "<h2>"+stringUtils.toUtf8("Análise de exportação - Registro 00")+"</h2>";

      if (response.any_error_msg) {
        htmlAnalise00 += "<p>"+stringUtils.toUtf8(response.msgs[0].msg)+"</p>";
      } else {
        var escola = response.escola[0];
        htmlAnalise00 += "<ul>";

        if (!escola["inep"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' não encontrados. Verifique se a escola possui o código INEP cadastrado. (Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Código INEP);")+"</li>";
        }
        if (!escola["cpf_gestor_escolar"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' não encontrados. Verifique se o(a) gestor(a) escolar possui o CPF cadastrado. (Pessoa FJ > Pessoa física > Editar > Campo: CPF);")+"</li>";
        }
        if (!escola["nome_gestor_escolar"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' não encontrados. Verifique se o(a) gestor(a) escolar foi informado(a). (Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Gestor escolar);")+"</li>";
        }
        if (!escola["cargo_gestor_escolar"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' não encontrados. Verifique se o cargo do(a) gestor(a) escolar foi informado. (Cadastros > Escola > Cadastrar > Editar > Campo: Cargo do gestor escolar);")+"</li>";
        }
        if (!escola["data_inicio"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' possui valor inválido. Verifique se a data inicial da primeira etapa foi cadastrada corretamente. (Cadastros > Escola > Cadastrar > Editar ano letivo > Ok > Campo: Data inicial);")+"</li>";
        }
        if (!escola["data_fim"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' possui valor inválido. Verifique se a data final da última etapa foi cadastrada corretamente. (Cadastros > Escola > Cadastrar > Editar ano letivo > Ok > Campo: Data final);")+"</li>";
        }
        if (!escola["latitude"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' não encontrados. Verificamos que a longitude foi informada, portanto obrigatoriamente a latitude também deve ser informada. (Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Latitude);")+"</li>";
        }
        if (!escola["longitude"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' não encontrados. Verificamos que a latitude foi informada, portanto obrigatoriamente a longitude também deve ser informada. (Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Longitude);")+"</li>";
        }
        if (!escola["uf_municipio"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' não encontrados. Verifique se o código da UF informada, foi cadastrado conforme a 'Tabela de UF'. (Endereçamento > Estado > Editar > Campo: Código INEP);")+"</li>";
        }
        if (!escola["municipio"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' não encontrados. Verifique se o código do município informado, foi cadastrado conforme a 'Tabela de Municípios'. (Endereçamento > Município > Editar > Campo: Código INEP);")+"</li>";
        }
        if (!escola["distrito"]) {
          htmlAnalise00 += "<li>"+stringUtils.toUtf8("Dados para formular o registro 00 da escola 'nome da escola' não encontrados. Verifique se o código do distrito informado, foi cadastrado conforme a 'Tabela de Distritos'. (Endereçamento > Distrito > Editar > Campo: Código INEP);")+"</li>";
        }
        htmlAnalise00 +="</ul>";
      }
      paginaResposta += htmlAnalise00;

      finishAnalysis();
    };

});