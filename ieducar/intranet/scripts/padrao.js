function openfoto( foto, altura, largura )
{
	abrir = 'visualizarfoto.php?id_foto='+foto;
	apr = 'width='+altura+', height='+largura+', scrollbars=no, top=10, left=10';
	var foto_ = window.open( abrir, 'JANELA_FOTO',  apr );
	foto_.focus();
}

function setFocus(campo){
	if(document.getElementById){
		var campo_ = document.getElementById(campo);
		campo_.focus();
	}
	else{
		if(document.forms[0]){
			var elements_ = document.forms[0].elements;
			for(var ct = 0 ; ct < elements_.length ; ct++){
				if(elements_[ct].getAttribute('type') != "hidden" && elements_[ct].disabled == false){
					elements_[ct].focus();
					break;
				}
			}
		}
	}
}

function openfotoagricultura( foto, altura, largura )
{
	abrir = 'visualizarfotoagricultura.php?id_foto='+foto;
	apr = 'width='+altura+', height='+largura+', scrollbars=no, top=10, left=10';
	var foto_ = window.open( abrir, 'JANELA_FOTO',  apr );
	foto_.focus();
}

function openurl(url)
{
	window.open(url,'PROCURAR','width=800, height=300, top=10, left=10, scrollbars=yes');
}

function openurlmaximized(url)
{
	var janela = window.open(url,'PROCURAR','status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=yes, scrollbars=yes');
	janela.innerWidth = screen.width;
	janela.innerHeight = screen.height;
	janela.screenX = 0;
	janela.screenY = 0;
}



function retorna(form, campo, valor)
{
	//window.opener.document.getElementById(campo).value=valor;
	window.parent.document.getElementById(campo).value=valor;
	campo = campo + "_";
	//window.opener.document.getElementById(campo).value=valor;
	window.parent.document.getElementById(campo).value=valor;
	//window.opener.insereSubmit();
	window.parent.insereSubmit();
	window.close();
	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
}
function insereSubmit( )
{
	document.getElementById('tipoacao').value = "";
	//document.formcadastro.tipoacao.value = "";
	document.getElementById('formcadastro').submit();
	//document.formcadastro.submit();

}
function insereSubmitLista( )
{
	document.getElementById('tipoacao').value = "";
	document.getElementById('lista').value = "1";
	document.getElementById('formcadastro').submit();
}

function insereSubmitNomeArquivo(nome)
{
	if(document.getElementById(nome).value)
	{
		document.getElementById('tipoacao').value = "";
		//document.formcadastro.tipoacao.value = "";
		document.getElementById('formcadastro').submit();
		//document.formcadastro.submit();
	}else
	{
		alert("Por Favor Insira um nome para o Arquivo");
	}
}


function insereSubmitValor(valor, campo )
{
	document.getElementById(campo).value = valor;
	document.getElementById('tipoacao').value = "";
	//document.formcadastro.tipoacao.value = "";
	document.getElementById('formcadastro').submit();
	//document.formcadastro.submit();

}

function reload( operador )
{
	if( document.getElementById('operador').value < 7 )
	{
		if( document.getElementById('valor').value != "" )
		{
			if( document.getElementById('logico').value != 0 )
			{
				document.getElementById('operador_logico').value = operador;
				document.formcadastro.submit();
			}
		}
		else
		{
			document.getElementById('logico_0').selected = true;
			alert("Insira o Valor");
		}
	}
	else
	{
		document.getElementById('operador_logico').value = operador;
		document.formcadastro.submit();
	}
}

function meusdadosReload( tipo )
{
	document.getElementById( 'reloading' ).value = 1;
	objSec = document.getElementById( 'f_ref_sec' );
	objDep = document.getElementById( 'f_ref_dept' );
	objSet = document.getElementById( 'f_ref_setor' );
	if( typeof objDep.selectedIndex == 'number' && tipo < 2 )
	{
		document.getElementById( 'f_ref_dept_0' ).selected = true;
	}
	if( typeof objSet.selectedIndex == 'number' && tipo < 3 )
	{
		document.getElementById( 'f_ref_setor_0' ).selected = true;
	}
	document.formcadastro.action='';
	document.formcadastro.submit();
}
function meusdadosReload2( tipo )
{
	document.getElementById( 'reloading' ).value = 1;
	objSec = document.getElementById( 'f_ref_sec' );
	objDep = document.getElementById( 'f_ref_dept' );
	objSet = document.getElementById( 'f_ref_setor' );
	if( typeof objDep.selectedIndex == 'number' && tipo < 2 )
	{
		document.getElementById( 'f_ref_dept_0' ).selected = true;
	}
	if( typeof objSet.selectedIndex == 'number' && tipo < 3 )
	{
		document.getElementById( 'f_ref_setor_0' ).selected = true;
	}
	document.formcadastro.action='';
	document.getElementById('TipoAcao').value='';
	document.formcadastro.submit();
}

function excluirSumit(id, nome_campo )
{
	if(id && nome_campo)
	{
		document.getElementById(nome_campo).value = id;
	}
	if( id == 0)
	{
		document.getElementById(nome_campo).value = "0";
	}

	document.getElementById('tipoacao').value = "";
	document.getElementById('formcadastro').submit();

}


function excluirSumitAcervo(id, nome_campo, descricao )
{
	if(id && nome_campo)
	{
		document.getElementById(nome_campo).value = id;
	}
	if( id == 0)
	{
		document.getElementById(nome_campo).value = "0";
	}
	document.getElementById('descricao').value = descricao;
	document.getElementById('tipoacao').value = "";
	document.getElementById('formcadastro').submit();
}




function exclui_idpes(idpes, campo)
{
	document.getElementById(campo).value = idpes;

	document.getElementById("numero2").value = document.getElementById("numero").value;
	document.getElementById("complemento2").value = document.getElementById("complemento").value;
	document.getElementById("letra2").value = document.getElementById("letra").value;
	document.getElementById("logradouro2").value = document.getElementById("logradouro").value;
	document.getElementById("bairro2").value = document.getElementById("bairro").value;
	document.getElementById("cidade2").value = document.getElementById("cidade").value;
	document.getElementById("sigla_uf2").value = document.getElementById("sigla_uf").value;
	document.getElementById("idtlog2").value = document.getElementById("idtlog").value;
	document.getElementById("apartamento2").value = document.getElementById("apartamento").value;
	document.getElementById("bloco2").value = document.getElementById("bloco").value;
	document.getElementById("andar2").value = document.getElementById("andar").value;

	document.getElementById("cep").value = document.getElementById("cep_").value;
	document.getElementById("tipoacao").value = "";
	document.getElementById("formcadastro").submit();
}


// scripts tirados do clsCampos.inc.php

function colocaMenos(campo)
{
	if(campo.value.indexOf("0") != -1 || campo.value.indexOf("1") != -1 ||campo.value.indexOf("2") != -1 || campo.value.indexOf("3") != -1 ||campo.value.indexOf("4") != -1 || campo.value.indexOf("5") != -1 ||campo.value.indexOf("6") != -1 || campo.value.indexOf("7") != -1 ||campo.value.indexOf("8") != -1 || campo.value.indexOf("9") != -1 )
	{
		if(campo.value.indexOf("-") == -1)
		campo.value = '-' + campo.value;
	}
	return false;
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

function formataCEP(campo, e)
{
	if( typeof window.event != "undefined" )
	{
		if (window.event.keyCode != 45)
		if (campo.value.length == 5)
		campo.value += '-';
	}
	else
	{
		if (e.which != 45 && e.which != 46 && e.which != 8 && e.which != 32 && e.which != 13 && e.which != 0 )
		if (campo.value.length == 5)
		campo.value += '-';
	}
}

function formataCPF(campo, e)
{
	if( typeof window.event != "undefined" )
	{
		if (window.event.keyCode != 46)
		if ((campo.value.length == 3) || (campo.value.length == 7))
		campo.value += '.';
		if (window.event.keyCode != 45)
		if (campo.value.length == 11)
		campo.value += '-';
	}
	else
	{
		if (e.which != 8)
		{
			if (e.which != 46)
			if ((campo.value.length == 3) || (campo.value.length == 7))
			campo.value += '.';
			if (e.which != 45)
			if (campo.value.length == 11)
			campo.value += '-';
		}

	}
}

function formataIdFederal(campo, e)
{
	if(campo.value.length > 13)
	{
		if( typeof window.event != "undefined" )
		{
			if (window.event.keyCode != 45 && window.event.keyCode != 46 && window.event.keyCode != 8 /*&& window.event.keyCode != 0*/ )
			{
				var str = campo.value;
				str = str.replace('.','');
				str = str.replace('.','');
				str = str.replace('-','');
				str = str.replace('/','');
				temp = str.substr(0,2);
				if(temp.length == 2)
				temp += '.';
				temp += str.substr(2,3);
				if(temp.length == 6)
				temp += '.';
				temp += str.substr(5,3);
				if(temp.length == 10)
				temp += '/';
				temp += str.substr(8,4);
				if(temp.length == 15)
				temp += '-';
				temp += str.substr(12,2);
				campo.value= temp;
			}

		}else
		{
			if (e.which != 45 && e.which != 46 && e.which != 8 && e.which != 32 && e.which != 13 /*&& e.which != 0*/    )
			{
				var str = campo.value;
				str = str.replace('.','');
				str = str.replace('.','');
				str = str.replace('-','');
				str = str.replace('/','');

				temp = str.substr(0,2);
				if(temp.length == 2)
				temp += '.';
				temp += str.substr(2,3);
				if(temp.length == 6)
				temp += '.';
				temp += str.substr(5,3);
				if(temp.length == 10)
				temp += '/';
				temp += str.substr(8,4);
				if(temp.length == 15)
				temp += '-';
				temp += str.substr(12,2);
				campo.value= temp;
			}

		}
	}else
	{
		if( typeof window.event != "undefined" )
		{
			if (window.event.keyCode != 45 && window.event.keyCode != 46 && window.event.keyCode != 8 /*&& window.event.keyCode != 0*/ )
			{
				var str = campo.value;
				str = str.replace('.','');
				str = str.replace('.','');
				str = str.replace('/','');
				str = str.replace('-','');

				temp = str.substr(0,3) ;
				if(temp.length == 3)
				temp += '.';
				temp += str.substr(3,3);
				if(temp.length == 7)
				temp += '.';
				temp += str.substr(6,3);
				if(temp.length == 11)
				temp += '-';
				temp += str.substr(9,2);
				campo.value= temp;
			}
		}else
		{
			if (e.which != 45 && e.which != 46 && e.which != 8 && e.which != 32 && e.which != 13 /*&&  e.which != 0*/ )
			{
				var str = campo.value;
				str = str.replace('.','');
				str = str.replace('.','');
				str = str.replace('/','');
				str = str.replace('-','');
				temp = str.substr(0,3) ;
				if(temp.length == 3)
				temp += '.';
				temp += str.substr(3,3);
				if(temp.length == 7)
				temp += '.';
				temp += str.substr(6,3);
				if(temp.length == 11)
				temp += '-';
				temp += str.substr(9,2);
				campo.value= temp;
			}
		}
	}
}

function formataMonetario(campo, e)
{
	if( typeof window.event != "undefined" )
	{
		if (window.event.keyCode != 44 && window.event.keyCode != 46 )
		{
			var valor = campo.value;

			valor = valor.replace(",","");
			valor = valor.replace(" ","");
			valor = valor.split(".").join("");
			valor = valor.split(",").join("");

			for(var i=0; i<valor.length; i++)
			{

				if(valor.substr(i,1) != 0)
				{
					valor = valor.substr(i);


					break;
				}
			}
			if (valor.length < 3  )
			{
				if(valor.length == 2 && valor != 00)
				{
					campo.value = "0,"+valor;
				}else
				{
					campo.value = "";
				}
				if (valor.length == 1 )
				campo.value = "0,0"+valor;
				if(valor.length == 0)
				{
					campo.value = "";
				}
			}else
			{
				var centavos = valor.substr(valor.length-2,2);
				var resto = valor.substr(0,valor.length-2);
				valor = "";
				var count = 0;
				for( var i=resto.length; i>0; i--)
				{
					count++;
					if(count % 3 == 1 && count >1)
					{
						valor = resto.substr(i-1,1)+"."+valor;
					}else
					{
						valor = resto.substr(i-1,1)+valor;
					}

				}
				campo.value = valor+","+centavos;
			}

		}
	}
	else
	{

		if (e.which != 46 && e.which != 44 )
		{
			var valor = campo.value;

			valor = valor.replace(",","");
			valor = valor.replace(" ","");
			valor = valor.split(".").join("");
			valor = valor.split(",").join("");

			for(var i=0; i<valor.length; i++)
			{

				if(valor[i] != 0)
				{
					valor = valor.substr(i);
					break;
				}
			}
			if (valor.length < 3  )
			{
				if(valor.length == 2 && valor != 00)
				{
					campo.value = "0,"+valor;
				}else
				{
					campo.value = "";
				}

				if (valor.length == 1 )
				campo.value = "0,0"+valor;
				if(valor.length == 0)
				{
					campo.value ="";
				}
			}else
			{
				var centavos = valor.substr(valor.length-2,2);
				var resto = valor.substr(0,valor.length-2);
				valor = "";
				var count = 0;
				for( var i=resto.length; i>0; i--)
				{
					count++;
					if(count % 3 == 1 && count >1)
					{
						valor = resto.substr(i-1,1)+"."+valor;
					}else
					{
						valor = resto.substr(i-1,1)+valor;
					}

				}
				campo.value = valor+","+centavos;
			}

		}
	}


}

function formataCNPJ(campo, e)
{
	if( typeof window.event != "undefined" )
	{
		if (window.event.keyCode != 46)
		if ((campo.value.length == 2) || (campo.value.length == 6))
		campo.value += '.';
		if (window.event.keyCode != 47)
		if (campo.value.length == 10)
		campo.value += '/';
		if (window.event.keyCode != 45)
		if (campo.value.length == 15)
		campo.value += '-';
	}
	else
	{
		if (e.which != 8)
		{
			if (e.which != 46)
			{
				if ((campo.value.length == 2) || (campo.value.length == 6))
				{
					campo.value += '.';
				}
			}
			if (e.which != 47)
			{
				if (campo.value.length == 10)
				{
					campo.value += '/';
				}
			}
			if (e.which != 45)
			{
				if (campo.value.length == 15)
				{
					campo.value += '-';
				}
			}
		}
	}
}

function formataFone(campo, e)
{
	if( typeof window.event != "undefined" )
	{
		if (window.event.keyCode != 40 && (campo.value.length == 0))
		campo.value += '(';
		if (window.event.keyCode != 41 && (campo.value.length == 3))
		campo.value += ')';
		if (window.event.keyCode != 45 && campo.value.length == 8 && campo.value.substr(7,1) != '-' )
		campo.value += '-';
	}else
	{
		if (e.which != 32 && e.which != 13 && e.which != 8 )
		{
			if(e.which != 40 && (campo.value.length == 1))
			campo.value = '(' + campo.value;
			if (e.which != 41 && (campo.value.length == 4))
			campo.value = campo.value.substr(0,3)+ ')' + campo.value.substr(3,1);
			if (e.which != 45 && campo.value.length == 8 && campo.value.substr(7,1) != '-' )
			campo.value += '-';
		}else
		{
			campo.value = campo.value.substr(0, campo.value.length-1);
		}
	}

}

function pesquisa_valores_f(caminho, campo, flag, pag_cadastro)
{
	jar = window.open(caminho+'?campo='+campo+'&flag='+flag+'&pag_cadastro='+pag_cadastro, 'JANELAPESQUISA', 'width=800, height=300, scrollbars=yes' );
	jar.focus();
}

function pesquisa_valores_popless(caminho, campo)
{
	new_id = DOM_divs.length;
	div = 'div_dinamico_' + new_id;
	if ( caminho.indexOf( '?' ) == -1 )
		showExpansivel( 500, 500, '<iframe src="' + caminho + '?campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="500" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores' );
	else
		showExpansivel( 500, 500, '<iframe src="' + caminho + '&campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="500" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores' );
}

function ativaCampo(campo)
{
	campo2 = document.getElementById(campo+'_lst');
	campo3 = document.getElementById(campo+'_val');
	if (campo2.disabled)
	{
		campo2.disabled=false;
		campo3.disabled=false;
	}
	else
	{
		campo2.disabled=true;
		campo3.disabled=true;
	}

}

function trocaConteudo( obj, conteudo )
{
	if( typeof obj.HTMLContent == "string" )
	{
		obj.HTMLContent = conteudo;
	}
	else
	{
		obj.innerHTML = conteudo;
	}
}

function LimpaSelect( campos )
{
	for( i = 0; i < campos.length; i++ )
	{
		ObjSelect = document.getElementById(campos[i]);
		ObjSelect.options.length = 0;
		ObjSelect.options[ObjSelect.options.length] = new Option ( 'Selecione','0', true, true);
	}
}

function coletaLocation(status, iptu)
{
	if (status == 2)
	{
		if (confirm("Passar cadastro Nº"+iptu+" para \"Isento?\""))
		{
			document.location = 'coleta_adesao_status.php?cod_adesao='+iptu+'&status='+status;
		}
		else
		{
			return false;
		}
	}
	if (status == 1)
	{
		if (confirm("Passar cadastro Nº"+iptu+" para \"Não Assiante?\""))
		{
			document.location = 'coleta_adesao_status.php?cod_adesao='+iptu+'&status='+status;
		}
		else
		{
			return false;
		}
	}
	if (status == 0)
	{
		if (confirm("Passar cadastro Nº"+iptu+" para \"Assiante\"?"))
		{
			document.location = 'coleta_adesao_status.php?cod_adesao='+iptu+'&status='+status;
		}
		else
		{
			return false;
		}
	}
}

function MenuCarregaDados(key, ordem, menu_pai, menu, submenu, titulo, ico_menu, alvo, suprime_menu )
{
	texto = titulo;
	if(submenu==0)
	{
		texto = "Selecione";
	}
	document.getElementById("ref_cod_menu_submenu").options[document.getElementById("ref_cod_menu_submenu").options.length] = new Option (texto, submenu, true, true);
	document.getElementById("ord_menu").value = ordem;
	document.getElementById("ref_cod_menu_pai").value = menu_pai;
	document.getElementById("ref_cod_menu").value = menu;
	document.getElementById("ref_cod_menu_submenu").value = submenu;
	document.getElementById("tt_menu").value = titulo;
	document.getElementById("img_banco_").value = ico_menu;
	document.getElementById("img_banco").value = ico_menu;
	document.getElementById("alvo").value = alvo;
	document.getElementById("suprime_menu").value = suprime_menu;
	document.getElementById("editar").value = key;
	document.getElementById("editando").value = '1';
	document.getElementById("id_deletar").value = key;
	//var submenu=document.getElementById("ref_cod_menu_submenu")
	//	mylist.options[mylist.selectedIndex].text = titulo;
}
function MenuExcluiDado()
{
	document.getElementById("editando").value = '2';
	document.getElementById('tipoacao').value = "";
	document.getElementById('formcadastro').submit();
}

function insertAtCursor(myField, myValue) {
	var ini = 0, fim=0, tam=0;
	var pos = arguments[2];

	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos 	 = myField.selectionEnd;

		ini = startPos;
		fim = endPos;
		tam = myValue.length;

		myField.value = myField.value.substring(0, startPos)+ myValue+ myField.value.substring(endPos, myField.value.length);
		myField.selectionStart = pos ? ini+pos : ini+tam;
		myField.selectionEnd = pos?  ini+pos : ini+tam;
		myField.focus();
	} else {
		myField.value += myValue;
	}
}

function ouvidoria_set_campo(campo, valor, texto)
{
	obj = parent.document.getElementById( campo );
	novoIndice = obj.options.length;
	obj.options[novoIndice] = new Option( texto );
	opcao = obj.options[novoIndice];
	opcao.value = valor;
	opcao.selected = true;
	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
	obj.onchange();
}

function vistoria_set_campo(campo1, valor1, campo2, valor2, campo3, valor3, campo4, valor4, campo5, valor5)
{
	obj1 = parent.document.getElementById( campo1 );
	obj1.value = valor1;

	obj2 = parent.document.getElementById( campo2 );
	obj2.value = valor2;

	obj3 = parent.document.getElementById( campo3 + "_" );
	obj3.value = valor3;

	var cep_oculto = parent.document.getElementById( campo3 );
	cep_oculto.value = valor3.replace("-","");

	obj4 = parent.document.getElementById( campo4 );
	obj4.value = valor4;

	obj5 = parent.document.getElementById( campo5 );
	obj5.value = valor5;

	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
	//obj.onchange();
}

//exibe ou esconde um campo da tela
function setVisibility(f,visible){
	var field = typeof(f) == 'object' ? f : document.getElementById(f);

	var browser = navigator.appName;
	if(browser.indexOf("Netscape") == 0){
		//Netscape and Mozilla
		if(field)
			field.style.visibility = (visible == true)?'visible':'collapse';
		else
			f.style.visibility = (visible == true)?'visible':'collapse';
	}else{
		//internet explorer
		if(field)
			field.style.display = (visible == true)?'inline':'none';
		else{
			//alert('how');
			f.style.display = (visible == true)?'inline':'none';
			//f.style.visibility = (visible == true)?'inline':'none';

		}
	}
}

//retorna true  se o campo estiver visivel ou falso caso contrario
function getVisibility(f){
	var field = document.getElementById(f);
	var browser = navigator.appName;
	field = field ? field : f;
	if(browser.indexOf("Netscape") == 0){
		//Netscape and Mozilla
			if(field.style.visibility == 'visible' || field.style.visibility == '')
				return true;
			else if (field.style.visibility == 'collapse')
				return false;
	}else{
		//internet explorer
			if(field.style.display == 'inline' || field.style.display == 'block' || field.style.display == '')
				return true;
			else if (field.style.display == 'none')
				return false;
	}
}


function cv_set_campo(campo1, valor1, campo2, valor2, campo3, valor3, campo4, valor4, campo5, valor5, campo6, valor6, campo7, valor7, campo8, valor8, campo9, valor9, campo10, valor10, campo11, valor11, campo12,campo13,valor13)
{



	obj1 = parent.document.getElementById( campo1 );
	obj1.value = valor1;
	obj1.disabled = true;

	obj2 = parent.document.getElementById( campo2 );
	obj2.value = valor2;

	//obj3 = parent.document.getElementById( campo3 + "_" );

	obj3 = parent.document.getElementById( campo3 );
	obj3.value = valor3;

	//var cep_oculto = parent.document.getElementById( campo3 );
	//cep_oculto.value = valor3.replace("-","");

	obj4 = parent.document.getElementById( campo4 );
	obj4.value = valor4;
	obj4.disabled = true;

	obj5 = parent.document.getElementById( campo5 );
	obj5.value = valor5;

	obj6 = parent.document.getElementById( campo6 );
	obj6.value = valor6;

	obj7 = parent.document.getElementById( campo7 );
	obj7.value = valor7;
	obj7.disabled = true;

	obj8 = parent.document.getElementById( campo8 );
	obj8.value = valor8;

	obj9 = parent.document.getElementById( campo9 );
	if(obj9)
		obj9.value = valor9;

	obj10 = parent.document.getElementById( campo10 );
	obj10.value = valor10;
	obj10.disabled = true;

	obj11 = parent.document.getElementById( campo11 );
	obj11.value = valor11;
	obj11.disabled = true;

	obj12 = parent.document.getElementById( campo12 );
	obj12.value = valor8;
	obj12.disabled = true;

	if(parent.document.getElementById( campo13 )){
		obj13 = parent.document.getElementById( campo13 );
		obj13.value = valor13;
		//alert('d');
		//obj13.disabled = true;
	}

	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
	//obj.onchange();
}

function cv_libera_campos(campo1, campo2, campo3, campo4, campo5, campo6, campo7)
{
	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
	parent.document.getElementById( campo1 ).disabled = false;
	parent.document.getElementById( campo1 ).value = "";
	parent.document.getElementById( campo2 ).disabled = false;
	parent.document.getElementById( campo2 ).value = false;
	parent.document.getElementById( campo3 ).disabled = false;
	parent.document.getElementById( campo3 ).value = "";
	parent.document.getElementById( campo4 ).disabled = false;
	parent.document.getElementById( campo4 ).value = "";
	parent.document.getElementById( campo5 ).disabled = false;
	parent.document.getElementById( campo5 ).value = "";
	parent.document.getElementById( campo6 ).disabled = false;
	parent.document.getElementById( campo6 ).value = "";
	if(parent.document.getElementById( campo7 ))
	{
		parent.document.getElementById( campo7 ).disabled = false;
		parent.document.getElementById( campo7 ).value = "1";
	}
}


function setCampoFoco(campo){

	document.getElementById(campo).focus();

}


/**
	Os parametros para campos do tipo texto devem ser "nome do campo", "valor do campo";
	Os parametros para campos do tipo select devem ser "nome do campo", "indice da opcao", "valor da opcao";
	Os parametros devem sempre seguir a ordem citada acima;
	O ultimo parametro pode ser utilizado para definir se vai existir submit ou nao, caso tenha, o ultimo parametro deve ser passado como "submit", caso contrario, não precisa passar nada;
*/

/*
**funcao a ser executada antes de fechar a janela
*/
var exec = null;
function set_campo_pesquisa()
{
	var i = 0;
	var submit = false;
	while ( i < arguments.length ) {
		if ( typeof arguments[i] != "undefined" && arguments[i] != "submit" ) {
			if ( parent.document.getElementById( arguments[i] ) )
				obj = parent.document.getElementById( arguments[i] );
			else if ( window.opener.document.getElementById( arguments[i] ) )
				obj = window.opener.document.getElementById( arguments[i] );
			if ( obj.type == "select-one" ) {
				novoIndice              = obj.options.length;
				obj.options[novoIndice] = new Option( arguments[i + 2] );
				opcao                   = obj.options[novoIndice];
				opcao.value				= arguments[i + 1];
				opcao.selected			= true;
				obj.onchange();
				i                      += 3;
			}
			else {
				obj.value               = arguments[i + 1];
				i                      += 2;
			/*	if ( parent.document.createEvent ) {
			        var evt = parent.document.createEvent( "KeyEvents" );
					//var evt = parent.document.createEvent( "UIEvents" );
			        //evt.initKeyEvent( "keypress", true, true, null, false, false, false, false, evt.DOM_VK_ENTER, 0 );
					evt.initKeyEvent( "keypress", true, true, window, false, false, false, false, 100, 0 );
					//evt.initUIEvent( "keypress", false, false, window, 1);
			        obj.dispatchEvent( evt );
			    } 
				else if ( parent.document.createEventObject ) {
			        var evt = parent.document.createEventObject();
			        obj.fireEvent( 'onkeypress', evt );
			    }*/
			}
		}
		else if ( arguments[i] == "submit" ) {
			submit = true;
			i     += 1;
		}
	}
	if ( submit ) {
		if ( window == top ) {
			tmpObj = window.opener.document.getElementById( 'tipoacao' )
			if( hasProperties( tmpObj ) )
				tmpObj.value = '';

			tmpObj = window.opener.document.getElementById( 'formcadastro' )
			if( hasProperties( tmpObj ) )
				tmpObj.submit();
		}
		else
		{
			tmpObj = window.parent.document.getElementById( 'tipoacao' )
			if( hasProperties( tmpObj ) ){
				tmpObj.value = '';
			}

			tmpObj = window.parent.document.getElementById( 'formcadastro' )
			if( hasProperties( tmpObj ) ) {
				tmpObj.submit();
			}
		}
	}
	
	if(exec)
	{
		exec();
	}
	if ( window == top )
		window.close();
	else
		window.parent.fechaExpansivel( 'div_dinamico_' + ( parent.DOM_divs.length * 1 - 1 ) );
}

// retorna 0 (zero) se nao tiver propriedades
function hasProperties( obj )
{
	prop = '';
	if( typeof obj == 'string' )
	{
		obj = document.getElementById( obj );
	}

	if( typeof obj == 'object' )
	{
		for( i in obj )
		{
			prop += i;
		}
	}
	return prop.length;
}

function enviar() {
	if( ( typeof arguments[0] != "undefined" ) && ( typeof arguments[1] != "undefined" ) )
		window.opener.addVal( arguments[0], arguments[1] );
	if( typeof arguments[2] != "undefined" )
		window.opener.document.getElementById( 'formcadastro' ).submit();
	window.close();
}

function enviar2() {
	if( ( typeof arguments[0] != "undefined" ) && ( typeof arguments[1] != "undefined" ) )
		window.parent.addVal( arguments[0], arguments[1] );
	if( typeof arguments[2] != "undefined" )
		window.parent.fechaExpansivel( 'div_dinamico_' + ( parent.DOM_divs.length * 1 - 1 ) );
	window.parent.document.getElementById( 'formcadastro' ).submit();
}

function getElementsByClassName(oElm, strTagName, strClassName)
{
	var arrElements = (strTagName == "*" && document.all)? document.all : oElm.getElementsByTagName(strTagName);
	var arrReturnElements = new Array();
	strClassName = strClassName.replace(/\-/g, "\\-");
	var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
	var oElement;
	for(var i=0; i<arrElements.length; i++)
	{
		oElement = arrElements[i];
		if(oRegExp.test(oElement.className))
		{
			arrReturnElements.push(oElement);
		}
	}
	return (arrReturnElements);
}

function mudaClassName( strClassTargetName, strNewClassName )
{
	tagName = ( arguments[2] ) ? arguments[2]: '*';
	arrObjs = getElementsByClassName( document, tagName, strClassTargetName );
	for( i in arrObjs )
	{
		arrObjs[i].className = strNewClassName;
	}
}

function addEvent_(evt,func,field)
{
	if(!field)
		field = window;
	else
		field = document.getElementById(field);

	if( field.addEventListener ) {
		//mozilla
	  field.addEventListener(evt,func,false);
	} else if ( field.attachEvent ) {
		//ie
	  field.attachEvent('on' + evt,func);
	}
}

function removeEvent(evt,func,field)
{

	if(!field)
		field = window;
	else
		field = document.getElementById(field);

	if( field.addEventListener ) {
		//mozilla
	  field.removeEventListener(evt,null,false);
	} else if ( field.detachEvent ) {
		//ie
	  field.detachEvent('on' + evt,func);
	}
}