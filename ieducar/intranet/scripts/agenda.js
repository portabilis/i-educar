var oldBotoes = '';

function form2text()
{
	// se tivesse algum sendo editado, fecha o formulario dele
	inputOldId = document.getElementById( "agenda_rap_id" );
	if( inputOldId != null )
	{
		compId = inputOldId.value;
	}

	// seleciona os DIVs
	divConteudo = document.getElementById( "conteudo_" + compId );
	divTitle = document.getElementById( "titulo_" + compId );
	divBotoes = document.getElementById( "botoes_" + compId );

	inputTitle = document.getElementById( "titulo_original_" + compId );
	inputConteudo = document.getElementById( "conteudo_original_" + compId );
	inputHoraIni = document.getElementById( "hora_original_ini_" + compId );
	inputHoraFim = document.getElementById( "hora_original_fim_" + compId );
	inputData = document.getElementById( "data_original_" + compId );
	inputOpcoes = document.getElementById( "extras_original_" + compId );

	if( inputTitle.value )
	{
		titulo = inputTitle.value;
	}
	else
	{
		arrTitle = inputConteudo.value.split( ' ' );
		titulo = arrTitle.slice( 0, 6 ).join( ' ' );
	}
	titulo = inputHoraIni.value + ' - ' + titulo + ' - ' + inputHoraFim.value;
	if( inputOpcoes.value & 1 && ! ( inputOpcoes.value & 4 ) )
	{
		titulo = '<span class="alerta">' + titulo + '</span>';
	}

	divTitle.innerHTML = titulo;
	divConteudo.innerHTML = inputConteudo.value.split( "\n" ).join( "<br>\n" );

	divBotoes.innerHTML = oldBotoes;
	if( document.getElementById( "aberto_" + compId ).value == 0 )
	{
		agenda_retrair( compId );
	}
}

function text2form( compId )
{
	// se tivesse algum sendo editado, fecha o formulario dele
	inputOldId = document.getElementById( "agenda_rap_id" );
	if( inputOldId != null )
	{
		form2text();
	}

	// seleciona os DIVs
	divConteudo = document.getElementById( "conteudo_" + compId );
	divTitle = document.getElementById( "titulo_" + compId );
	divBotoes = document.getElementById( "botoes_" + compId );

	inputTitle = document.getElementById( "titulo_original_" + compId );
	inputConteudo = document.getElementById( "conteudo_original_" + compId );
	inputHoraIni = document.getElementById( "hora_original_ini_" + compId );
	inputHoraFim = document.getElementById( "hora_original_fim_" + compId );
	inputData = document.getElementById( "data_original_" + compId );
	inputOpcoes = document.getElementById( "extras_original_" + compId );
	inputAberto = document.getElementById( "aberto_" + compId );

	publica = document.getElementById( "agenda_publica" ).value;

	divTitle.innerHTML = '<input type="hidden" name="agenda_rap_id" id="agenda_rap_id" value="' + compId + '"><input type="text" name="agenda_rap_hora" id="agenda_rap_hora" class="agenda_hora" value="' + inputHoraIni.value + '"  maxlength="5" onKeyPress="formataHora(this, event);"> <input type="text" name="agenda_rap_titulo" id="agenda_rap_titulo" class="agenda_titulo" value="">';
	document.getElementById( 'agenda_rap_titulo' ).value = inputTitle.value;

	chck1 = ( inputOpcoes.value & 2 ) ? 'checked' : '';
	chck2 = ( inputOpcoes.value & 1 ) ? 'checked' : '';

	conteudo = '<textarea name="agenda_rap_conteudo" id="agenda_rap_conteudo" class="agenda_conteudo">' + inputConteudo.value + '</textarea><br>';
	conteudo += 'Fim do Compromisso:<input type="text" name="agenda_rap_horafim" id="agenda_rap_horafim" class="agenda_hora" value="' + inputHoraFim.value + '" maxlength="5" onKeyPress="formataHora(this, event);"> Data: <input type="text" name="agenda_rap_data" id="agenda_rap_data" class="agenda_data" value="' + inputData.value + '"  maxlength="10" onKeyPress="formataData(this, event);"><br>';
	conteudo += 'Importante:<input type="checkbox" name="agenda_rap_importante" id="agenda_rap_importante" ' + chck2 + ' style="margin-right:89px;">';
	if( publica == 1 )
	{
		conteudo += 'P&uacute;blico:<input type="checkbox" name="agenda_rap_publico" id="agenda_rap_publico" ' + chck1 + '>';
	}
	conteudo += '<br>';
	conteudo += '<input type="button" name="agenda_salvar" class="agenda_rap_botao" id="agenda_salvar" value="Salvar Altera&ccedil;&otilde;es" onclick="agenda_salva();"> <input type="button" name="agenda_cancelar" class="agenda_rap_botao" id="agenda_cancelar" value="Cancelar Altera&ccedil;&otilde;es" onclick="form2text( ' + compId + ' );">';

	divConteudo.innerHTML = conteudo;
	oldBotoes = divBotoes.innerHTML;
	divBotoes.innerHTML = "";
}

function agenda_salva()
{
	erros = '';
	// descobre quem estava sendo editado
	inputOldId = document.getElementById( "agenda_rap_id" );
	if( inputOldId != null )
	{
		compId = inputOldId.value;

		// seleciona os campos para verificar dados (data, hora, obrigatorio, etc)
		inputTitle = document.getElementById( "agenda_rap_titulo" );
		inputConteudo = document.getElementById( "agenda_rap_conteudo" );
		inputHoraIni = document.getElementById( "agenda_rap_hora" );
		inputHoraFim = document.getElementById( "agenda_rap_horafim" );
		inputData = document.getElementById( "agenda_rap_data" );

		// verifica integridade
		if( !(/[0-9]{2}:[0-9]{2}/.test( inputHoraIni.value ) ) )
		{
			alert( 'Preencha o campo Hora de Inicio corretamente.\nFormato hora: hh:mm' );
			inputHoraIni.focus();
			return false;
		}
		if (!(/(((0[1-9]|[12][0-9])\/(02))|((0[1-9]|[12][0-9]|(30))\/(0[4689]|(11)))|((0[1-9]|[12][0-9]|3[01])\/(0[13578]|(10)|(12))))\/[1-2][0-9]{3}/.test( inputData.value )))
		{
			alert( 'Preencha o campo Data corretamente.\nFormato data: dd/mm/aaaa' );
			inputData.focus();
			return false;
		}
		if( !(/[^ ]/.test( inputTitle.value )) && !(/[^ ]/.test( inputConteudo.value )) )
		{
			alert( 'Preencha o campo Titulo ou o campo Descricao' );
			inputConteudo.focus();
			return false;
		}

		// se estiver ok envia o formulario
		document.getElementById( "agenda_principal" ).submit();
		return;
	}
	else
	{
		erros += 'Impossivel identificar compromisso editado.';
	}
	alert( erros );
}

function agenda_expandir( compId )
{
	// descobre se alguem estava sendo editado
	inputOldId = document.getElementById( "agenda_rap_id" );
	if( inputOldId != null )
	{
		// tem alguem
		if( inputOldId.value == compId )
		{
			// eh exatamente o que vamos expandir
			form2text();
		}
	}
	inputConteudo = document.getElementById( "conteudo_original_" + compId );
	divConteudo = document.getElementById( "conteudo_" + compId );
	divExpandir = document.getElementById( "agenda_expandir_" + compId );

	divExpandir.innerHTML = '<div id="agenda_expandir_' + compId + '"><a href="javascript:agenda_retrair( ' + compId + ' );"><img src="imagens/agenda_icon_extendido.gif" border="0" alt="Retrair" title="Retrair este compromisso"></a></div>';

	divConteudo.innerHTML = inputConteudo.value.split( '\n' ).join( '<br>' );;
	document.getElementById( "aberto_" + compId ).value = 1;
}

function agenda_retrair( compId )
{
	// descobre se alguem estava sendo editado
	inputOldId = document.getElementById( "agenda_rap_id" );
	if( inputOldId != null )
	{
		// tem alguem
		if( inputOldId.value == compId )
		{
			// eh exatamente o que vamos retrair
			form2text();
		}
	}
	inputConteudo = document.getElementById( "conteudo_original_" + compId );
	divConteudo = document.getElementById( "conteudo_" + compId );
	divExpandir = document.getElementById( "agenda_expandir_" + compId );

	divExpandir.innerHTML = '<div id="agenda_expandir_' + compId + '"><a href="javascript:agenda_expandir( ' + compId + ' );"><img src="imagens/agenda_icon_retraido.gif" border="0" alt="Expandir" title="Expandir este compromisso"></a></div>';

	textoArr = inputConteudo.value.split( ' ' ).slice( 0, 21 ).join( ' ' ) + '...';
	textoArr = textoArr.split( '\n' ).join( '<br>' );
	divConteudo.innerHTML = textoArr;
	document.getElementById( "aberto_" + compId ).value = 0;
}

function excluir_compromisso( compId )
{
	if( confirm( 'Deseja realmente excluir este compromisso?\nEsta e uma operacao irreversivel!' ) )
	{
		excluirSim( compId, location );
	}
	//showExpansivel( 400, 114, 'Deseja realmente excluir este compromisso?<br><br>Esta &eacute; uma opera&ccedil;&atilde;o irrevers&iacute;vel.<br><br><input type="button" name="agenda_sim" class="agenda_rap_botao" id="agenda_salvar" value="Sim" onclick="excluirSim( ' + compId + ' );"> <input type="button" name="agenda_nao" class="agenda_rap_botao" id="agenda_salvar" value="N&atilde;o" onclick="excluirNao( ' + compId + ' )">' );
}

function excluirSim( compId )
{
	expansivel = document.getElementById( "DOM_expansivel" );
	expansivel.style.display = 'none';
	document.location.href = 'agenda.php' + document.getElementById( "parametros" ).value + '&deletar=' + compId;
}

// Excluir compromisso dentro do sistema OpenJuris -- Higor 23/11/2005
function excluirJuris( compId )
{
	if( confirm( 'Deseja realmente excluir este compromisso?\nEsta e uma operacao irreversivel!' ) )
	{
		excluirSim( compId, location );
	}
	//showExpansivel( 400, 114, 'Deseja realmente excluir este compromisso?<br><br>Esta &eacute; uma opera&ccedil;&atilde;o irrevers&iacute;vel.<br><br><input type="button" name="agenda_sim" class="agenda_rap_botao" id="agenda_salvar" value="Sim" onclick="excluirSim( ' + compId + ' );"> <input type="button" name="agenda_nao" class="agenda_rap_botao" id="agenda_salvar" value="N&atilde;o" onclick="excluirNao( ' + compId + ' )">' );
}

function excluirSimJuris( compId )
{
	expansivel = document.getElementById( "DOM_expansivel" );
	expansivel.style.display = 'none';
	document.location.href = 'juris_agenda_desenv.php' + document.getElementById( "parametros" ).value + '&deletar=' + compId;
}

function novoForm(array_compromissos)
{
	acao = 'agenda.php';
	if(typeof(array_compromissos) != "undefined")
	{
		select = "<select name='tipo_compromisso' id='tipo_compromisso'>";
		select = select+"<option value=''>Selecione</option>";
		for(i=0;i<array_compromissos.length;i+=2)
		{
			select = select+"<option value='"+array_compromissos[i]+"'>"+array_compromissos[i+1]+"</option>";
		}
		select = select+"</select>";
		acao = 'juris_agenda_desenv.php';
	}
	publica = document.getElementById( "agenda_publica" ).value;

	conteudo = '<form id="novo_form" action="'+acao+ document.getElementById( "parametros" ).value + '" method="POST"><br>';
	conteudo += '<table border="0" cellpadding="0" cellspacing="3">';
	conteudo += '<tr>';
	conteudo += '<td width="25%">Inicio <input type="text" name="novo_hora_inicio" id="novo_hora_inicio" class="agenda_hora" title="Horas (hh:mm)" onChange="verifica_hora(this.value)" maxlength="5"onKeyPress="formataHora(this, event);"></td>';
	conteudo += '<td width="25%">Fim <input type="text" name="novo_hora_fim" id="novo_hora_fim" class="agenda_hora" title="Horas (hh:mm)" maxlength="5" onChange="verifica_hora_fim(this.value)" onKeyPress="formataHora(this, event);"></td>';
	conteudo += '<td colspan="2" width="50%" align="right">Data:<input type="text" name="novo_data" id="novo_data" value="' + document.getElementById( "data_atual" ).value + '" class="agenda_data" title="Data (dd/mm/aaaa)" maxlength="10" onKeyPress="formataData(this, event);"></td>';
	conteudo += '</tr>';
	conteudo += '<tr><td colspan="4" style="height:1px;background-color: #9cbdd7;"></td></tr>';
	conteudo += '<tr>';
	conteudo += '<td width="25%" valign="top">T&iacute;tulo:</td>';
	conteudo += '<td colspan="3" width="75%"><input type="text" name="novo_titulo" id="novo_titulo" style="width:330px;"></td>';
	conteudo += '</tr>';
	conteudo += '<tr>';
	conteudo += '<td width="25%" valign="top">Descri&ccedil;&atilde;o:</td>';
	conteudo += '<td colspan="3" width="75%"><textarea name="novo_descricao" id="novo_descricao" class="agenda_conteudo" style="width:330px;"></textarea></td>';
	conteudo += '</tr>';
	conteudo += '<tr><td colspan="4" style="height:1px;background-color: #9cbdd7;"></td></tr>';
	conteudo += '<tr>';
	if(typeof(select) != "undefined")
	{
		conteudo += '<td colspan="1">Importante <input type="checkbox" name="novo_importante" id="novo_importante"></td><td>'+select+'</td>';
	}else
	{
		conteudo += '<td colspan="2">Importante <input type="checkbox" name="novo_importante" id="novo_importante"></td>';
	}
	if( publica == 1 )
	{
		conteudo += '<td colspan="2">Publico <input type="checkbox" name="novo_publico" id="novo_publico"></td>';
	}
	else
	{
		conteudo += '<td colspan="2">&nbsp;</td>';
	}
	conteudo += '</tr>';
	conteudo += '<tr><td colspan="4" style="height:1px;background-color: #9cbdd7;"></td></tr>';
	conteudo += '<tr>';
	conteudo += '<td colspan="4">Repetir este compromisso a cada: <input type="text" name="novo_repetir_dias" id="novo_repetir_dias" class="small" value="0" maxlength="2"> dias. ';
	conteudo += 'Repetir <input type="text" name="novo_repetir_qtd" id="novo_repetir_qtd" class="small" value="0" maxlength="2"> vezes</td></tr>';
	conteudo += '<tr><td width="25%">&nbsp;</td><td width="25%">&nbsp;</td><td width="25%">&nbsp;</td><td width="25%">&nbsp;</td></tr>';
	conteudo += '<tr><td colspan="4" align="center"><input type="button" name="agenda_sim" class="agenda_rap_botao" id="agenda_salvar" value="Salvar" onclick="checaEnvio();"> <input type="button" name="agenda_nao" class="agenda_rap_botao" id="agenda_salvar" value="Cancelar" onclick="document.getElementById(\'modal-close\').onclick();"></td></tr>';
	conteudo += '</table>';

	conteudo += '</form>';
	showExpansivel( 460, 360, conteudo, 'Cadastro de Compromisso' );
	document.getElementById( "novo_hora_inicio" ).focus();
}

function salvaNota( compId )
{
	conteudo = '<form id="novo_form" action="agenda.php' + document.getElementById( "parametros" ).value + '" method="POST"><br>';
	conteudo += '<input type="hidden" name="grava_compromisso" value="' + compId + '">';
	conteudo += '<table border="0" cellpadding="0" cellspacing="3">';
	conteudo += '<tr>';
	conteudo += '<td>Hora de Fim:</td>';
	conteudo += '<td><input type="text" name="grava_hora_fim" id="grava_hora_fim" class="agenda_hora" title="Horas (hh:mm)" maxlength="5"onKeyPress="formataHora(this, event);"></td>';
	conteudo += '</tr>';
	conteudo += '<tr><td colspan="2" style="height:1px;background-color: #9cbdd7;"></td></tr>';
	conteudo += '<tr>';
	conteudo += '<tr><td colspan="2" align="center"><input type="button" name="agenda_sim" class="agenda_rap_botao" id="agenda_salvar" value="Gravar" onclick="checaGravacao();" style="width:100px;"> <input type="button" name="agenda_nao" class="agenda_rap_botao" id="agenda_salvar" value="Cancelar" onclick="document.getElementById(\'modal-close\').onclick()" style="width:100px;"></td></tr>';
	conteudo += '</table>';

	conteudo += '</form>';
	showExpansivel( 270, 180, conteudo, 'Salvar como compromisso' );
	document.getElementById( "grava_hora_fim" ).focus();
}

function checaEnvio()
{

	if( !(/[0-9]{2}:[0-9]{2}/.test( document.getElementById( "novo_hora_inicio" ).value ) ) )
	{
		alert( 'Preencha o campo Inicio corretamente.\nFormato hora: hh:mm' );
		document.getElementById( "novo_hora_inicio" ).focus();
		return false;
	}
	if (!(/(((0[1-9]|[12][0-9])\/(02))|((0[1-9]|[12][0-9]|(30))\/(0[4689]|(11)))|((0[1-9]|[12][0-9]|3[01])\/(0[13578]|(10)|(12))))\/[1-2][0-9]{3}/.test( document.getElementById( "novo_data" ).value )))
	{
		alert( 'Preencha o campo Data corretamente.\nFormato data: dd/mm/aaaa' );
		document.getElementById( "novo_data" ).focus();
		return false;
	}
	//14-11-2005 -> inicio alteracao
	if( document.getElementById( "novo_hora_fim" ).value != "" && !(/[0-9]{2}:[0-9]{2}/.test( document.getElementById( "novo_hora_fim" ).value ) ) )
	{
		alert( 'Preencha o campo fim corretamente.\nFormato hora: hh:mm' );
		document.getElementById( "novo_hora_fim" ).focus();
		return false;
	}
	//14-11-2005 -> fim alteracao
	if( !(/[^ ]/.test( document.getElementById( "novo_titulo" ).value )) && !(/[^ ]/.test( document.getElementById( "novo_descricao" ).value )) )
	{
		alert( 'Preencha o campo Titulo ou o campo Descricao' );
		document.getElementById( "novo_titulo" ).focus();
		return false;
	}
	if(document.getElementById( "tipo_compromisso" ))
	{
		if( !(/[^ ]/.test( document.getElementById( "tipo_compromisso" ).value )))
		{
			alert( 'Selecione o Tipo de Compromisso' );
			document.getElementById( "tipo_compromisso" ).focus();
			return false;
		}
	}
	document.getElementById( "novo_form" ).submit();
}

function checaGravacao()
{
	if( !(/[0-9]{2}:[0-9]{2}/.test( document.getElementById( "grava_hora_fim" ).value ) ) )
	{
		alert( 'Preencha o campo Fim corretamente.\nFormato hora: hh:mm' );
		document.getElementById( "grava_hora_fim" ).focus();
		return false;
	}
	document.getElementById( "novo_form" ).submit();
}

function formataData(campo, e)
{

	if( typeof window.event != "undefined")
	{
		if (window.event.keyCode != 47)
		{
			if ((campo.value.length == 2) || (campo.value.length == 5))
			{
				campo.value += '/';
			}
		}
	}else
	{
		if (e.which != 47 && e.which != 45 && e.which != 46 && e.which != 8 && e.which != 32 && e.which != 13 && e.which != 0    )
		{
			if ((campo.value.length == 2) || (campo.value.length == 5))
			{
				campo.value += '/';
			}
		}
	}
}

function formataHora(campo, e)
{
	if( typeof window.event != "undefined" )
	{
		if (window.event.keyCode != 58)
			{
			if ((campo.value.length == 2))
			{
				campo.value += ':';
			}
		}
	}else
	{
		if (e.which != 45 && e.which != 46 && e.which != 8 && e.which != 32 && e.which != 13 && e.which != 0    )
		{
			if ((campo.value.length == 2))
			{
				campo.value += ':';
			}
		}
	}
}

function verifica_hora(novo_hora_inicio){ 
hrs = (document.forms[0].novo_hora_inicio.value.substring(0,2)); 
min = (document.forms[0].novo_hora_inicio.value.substring(3,5)); 
               

if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59)){ 
alert('Por favor, insira um hor\u00e1rio v\u00e1lido!');
document.forms[0].novo_hora_inicio.value = "";
}       
} 
function verifica_hora_fim(novo_hora_fim){ 
hrs = (document.forms[0].novo_hora_fim.value.substring(0,2)); 
min = (document.forms[0].novo_hora_fim.value.substring(3,5)); 
               

if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59)){ 
alert('Por favor, insira um hor\u00e1rio v\u00e1lido!');
document.forms[0].novo_hora_fim.value = "";
}       
} 