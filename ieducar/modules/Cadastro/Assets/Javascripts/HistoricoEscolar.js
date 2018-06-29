$j(document).ready(function(){
	var codigoEscola = document.getElementById('codigoEscola').value;
	var nomeEscola = document.getElementById('escola').value;
	var numeroSequencial = document.getElementById('numeroSequencial').value;

	//Quando for novo cadastro
	if(codigoEscola === '' && nomeEscola === '' && numeroSequencial === ''){
		$j('#ref_cod_escola').val("");
		$j('#escola').closest('tr').hide();
	}
	//Quando for edição e for outra
	else if(codigoEscola === '' && numeroSequencial !== ''){
		$j('#ref_cod_escola').val('outra');
		$j('#escola').closest('tr').show();
		$j('#escola').val(nomeEscola);
	}
	//Quando for edição e não for outra
	else{
		$j('#ref_cod_escola').val(codigoEscola);
		$j('#escola').val('');
		$j('#escola').closest('tr').hide();
	}

	function habilitaPosicao(){
		if ($j('#dependencia')[0].checked) {
			$j('#posicao').closest('tr').hide();
		} else{
			$j('#posicao').closest('tr').show();
		}
	}

	$j("#dependencia").click(function(){
		habilitaPosicao();
	});

	habilitaPosicao();

});

	$j(function (){
	$j('#ref_cod_escola').change(function (){
		var ref_cod_escola_destino = $j('#ref_cod_escola').val();
		if(ref_cod_escola_destino === 'outra'){
			$j('#escola').closest('tr').show();
		}
		else{
			$j('#escola').val('');
			$j('#escola').closest('tr').hide();
		}
	});

});

document.getElementById('cb_faltas_globalizadas').onclick =function()
{
	setVisibility('tr_faltas_globalizadas',this.checked);
	this.setAttribute('value', this.checked ? 'on' : '');
}

document.getElementById('cb_faltas_globalizadas').onclick();



document.getElementById('idpais').onchange = function() {
	var campoPais = document.getElementById( 'idpais' ).value;
	var campoEstado = document.getElementById( 'escola_uf' );

	campoEstado.length = 1;
	campoEstado.disabled = true;
	campoEstado.options[0] = new Option( 'Carregando estados', '', false, false );

	var xml1 = new ajax(getEstado_XML);
	strURL = "public_uf_xml.php?pais="+campoPais;
	xml1.envia(strURL);
}

function getEstado_XML(xml)
{


	var campoEstado = document.getElementById( 'escola_uf' );


	var estados = xml.getElementsByTagName( "estado" );

	campoEstado.length = 1;
	campoEstado.options[0] = new Option( 'Selecione um estado', '', false, false );
	for ( var j = 0; j < estados.length; j++ )
	{

		campoEstado.options[campoEstado.options.length] = new Option( estados[j].firstChild.nodeValue, estados[j].getAttribute('sigla_uf'), false, false );

	}
	if ( campoEstado.length == 1 ) {
		campoEstado.options[0] = new Option( 'País não possui estados', '', false, false );
	}

	campoEstado.disabled = false;
}


// autocomplete disciplina fields

var handleSelect = function(event, ui){
	$j(event.target).val(ui.item.label);
	return false;
};

var search = function(request, response) {
	var searchPath = '/module/Api/ComponenteCurricular?oper=get&resource=componente_curricular-search';
	var params     = { query : request.term };

	$j.get(searchPath, params, function(dataResponse) {
		simpleSearch.handleSearch(dataResponse, response);
	});
};

function setAutoComplete() {
	$j.each($j('input[id^="nm_disciplina"]'), function(index, field) {

		$j(field).autocomplete({
			source    : search,
			select    : handleSelect,
			minLength : 1,
			autoFocus : true
		});

	});
}

setAutoComplete();

var submitForm = function(event) {
	var $frequenciaField  	  = $j('#frequencia');
	var frequencia        	  = $frequenciaField.val();
	var frequenciaObrigatoria = $frequenciaField.hasClass('obrigatorio');

if (frequencia.indexOf(',') > -1){
	frequencia = frequencia.replace('.', '').replace(',', '.');
}

if((frequencia.trim() == '')&&(!frequenciaObrigatoria)){
	formUtils.submit();
}else{
	if (validatesIfNumericValueIsInRange(frequencia, $frequenciaField, 0, 100))
    	formUtils.submit();
	}
}

// bind events

var $addDisciplinaButton = $j('#btn_add_tab_add_1');

$addDisciplinaButton.click(function(){
	setAutoComplete();
});


// submit button

var $submitButton = $j('#btn_enviar');

$submitButton.removeAttr('onclick');
$submitButton.click(submitForm);

