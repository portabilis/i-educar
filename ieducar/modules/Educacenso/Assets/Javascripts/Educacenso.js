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

    var paginaResposta = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Analize exportação</title></head><body>'+
						 '<div id="content">'+
						 '  <h3>Analise de exporta&ccedil;&atilde;o</h3>'+
						 '  <p>Este e um exemplo de problema relatado pela analise do i-Educar.</p>'+
						 '</div>'+
						 '<div id="editor"></div>'+
						 '</body></html>';

    var doc = new jsPDF();
    var specialElementHandlers = {
        '#editor': function (element, renderer) {
            return true;
        }
    };

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

      //Simula analise
      setTimeout(function()
      {
        finishAnalysis();
      }, 3000);

    });

    var finishAnalysis = function() {
        $j.modal.close();
        doc.fromHTML(paginaResposta, 15, 15, {
            'width': 170,
                'elementHandlers': specialElementHandlers
        });
        doc.output('dataurlnewwindow');
    }

});