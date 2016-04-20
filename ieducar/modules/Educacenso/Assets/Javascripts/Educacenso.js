$j(document).ready(function(){

	var modalLoad = '<div id="ex5" class="modal">' +
					'<div style="float:left;width:100px;">' +
  					'	<img src="http://www.tourismthailand.org/images/loading/load.gif" width="100px" height="100px" alt="">' +
  					'</div>'+
  					'<div style="float:right;width:300px;">'+
  					'	<p style="margin-left: 20px; margin-top: 40px;font-family: verdana, arial; font-size: 18px;">Analisando as informa&ccedil;&otilde;es</p>' +
  					'</div>'+
  					'</div>';

    $j("body").append(modalLoad);
    $j("#btn_enviar").click(function(){

    	var escola = $j("#ref_cod_escola").val();
    	var dataIni = $j("#data_ini").val();
    	var dataFim = $j("#data_fim").val();

    	if (!escola || !dataIni || !dataFim){
    		alert("Preencha os dados obrigat\u00f3rios antes de continuar.");
    		return;
    	}

    	$j("#ex5").modal({
		  escapeClose: false,
		  clickClose: false,
		  showClose: false
		});
    });

    function analizarExportar(){

    }


});