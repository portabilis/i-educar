$j(document).ready(function(){
	let codigoEscola = document.getElementById('codigoEscola').value;
	let nomeEscola = document.getElementById('escola').value;
	let numeroSequencial = document.getElementById('numeroSequencial').value;
  const escola_em_outro_municipio = $j('#escola_em_outro_municipio');
  const cidade_escola = $j('#escola_cidade');
  const estado_escola = $j('#escola_uf');
  const pais_escola = $j('#idpais');

	//Quando for novo cadastro
	if(codigoEscola === '' && nomeEscola === '' && numeroSequencial === ''){
		$j('#ref_cod_escola').val("");
		$j('#escola').closest('tr').hide();
    cidade_escola.closest('tr').hide();
    estado_escola.closest('tr').hide();
    pais_escola.closest('tr').hide();
	}
	//Quando for edição e for outra
	else if(codigoEscola === '' && numeroSequencial !== ''){
    escola_em_outro_municipio.prop('checked', true);
    $j('#ref_cod_instituicao').closest('tr').hide();
    $j('#ref_cod_escola').closest('tr').hide();
		$j('#ref_cod_escola').val('outra');
		$j('#escola').closest('tr').show();
		$j('#escola').val(nomeEscola);
	}
	//Quando for edição e não for outra
	else{
    cidade_escola.closest('tr').hide();
    estado_escola.closest('tr').hide();
    pais_escola.closest('tr').hide();
    $j('#ref_cod_escola').makeRequired();
    $j('#ref_cod_instituicao').makeRequired();
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

    escola_em_outro_municipio.change(function () {
      if (escola_em_outro_municipio.is(':checked')) {
        $j('#ref_cod_escola').val('outra');
        $j('#escola').closest('tr').show();
        $j('#ref_cod_instituicao').closest('tr').hide();
        $j('#ref_cod_escola').closest('tr').hide();
        cidade_escola.closest('tr').show();
        estado_escola.closest('tr').show();
        pais_escola.closest('tr').show();
        pais_escola.val('');
        estado_escola.val('');
        cidade_escola.val('');
      } else {
        $j('#escola').closest('tr').hide();
        cidade_escola.closest('tr').hide();
        estado_escola.closest('tr').hide();
        pais_escola.closest('tr').hide();
        pais_escola.val('');
        estado_escola.val('');
        cidade_escola.val('');
        $j('#ref_cod_instituicao').closest('tr').show();
        $j('#ref_cod_escola').closest('tr').show();
        $j('#ref_cod_instituicao').makeRequired();
        $j('#ref_cod_escola').makeRequired();
        }
      });

    $j('#ref_cod_escola').change(function () {
      const cod_escola = $j('#ref_cod_escola').val();
      let url = getResourceUrlBuilder.buildUrl('/module/Api/Escola',
        'endereco-escola',
        { escola_id : cod_escola }
      );
      let options = {
        url      : url,
        dataType : 'json',
        success  : function (response) {
          pais_escola.val(response.country_id);
          estado_escola.val(response.state_abbreviation);
          cidade_escola.val(response.city);
        }
      };
      getResources(options);
    });
});

document.getElementById('cb_faltas_globalizadas').onclick =function()
{
	setVisibility('tr_faltas_globalizadas',this.checked);
	this.setAttribute('value', this.checked ? 'on' : '');
}

document.getElementById('cb_faltas_globalizadas').onclick();



document.getElementById('idpais').onchange = function() {
	let campoPais = document.getElementById( 'idpais' ).value;
	let campoEstado = document.getElementById( 'escola_uf' );

	campoEstado.length = 1;
	campoEstado.disabled = true;
	campoEstado.options[0] = new Option( 'Carregando estados', '', false, false );

	let xml1 = new ajax(getEstado_XML);
	strURL = "public_uf_xml.php?pais="+campoPais+"&abbreviation=true";
	xml1.envia(strURL);
}

function getEstado_XML(xml)
{


	let campoEstado = document.getElementById( 'escola_uf' );


	let estados = xml.getElementsByTagName( "estado" );

	campoEstado.length = 1;
	campoEstado.options[0] = new Option( 'Selecione um estado', '', false, false );
	for ( let j = 0; j < estados.length; j++ )
	{

		campoEstado.options[campoEstado.options.length] = new Option( estados[j].firstChild.nodeValue, estados[j].getAttribute('id'), false, false );

	}
	if ( campoEstado.length == 1 ) {
		campoEstado.options[0] = new Option( 'País não possui estados', '', false, false );
	}

	campoEstado.disabled = false;
}


// autocomplete disciplina fields

let handleSelect = function(event, ui){
	$j(event.target).val(ui.item.label);
	return false;
};

let search = function(request, response) {
	let searchPath = '/module/Api/ComponenteCurricular?oper=get&resource=componente_curricular-search';
	let params     = { query : request.term };

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

let submitForm = function(event) {
	let $frequenciaField  	  = $j('#frequencia');
	let frequencia        	  = $frequenciaField.val();
	let frequenciaObrigatoria = $frequenciaField.hasClass('obrigatorio');

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

let $addDisciplinaButton = $j('#btn_add_tab_add_1');

$addDisciplinaButton.click(function(){
	setAutoComplete();
});


// submit button

let $submitButton = $j('#btn_enviar');

$submitButton.removeAttr('onclick');
$submitButton.click(validaSubmit);

  function validaSubmit() {
    if (!$j('#escola_em_outro_municipio').is(':checked')) {
      if ($j('#ref_cod_instituicao').closest("select").val() === '') {
        return alert('É necessário informar a instituição');
      }
      if ($j('#ref_cod_escola').closest("select").val() === '') {
        return alert('É necessário informar a escola');
      }
    }
    acao();
  };

