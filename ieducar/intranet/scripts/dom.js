function ajax( funcaoRetorno )
{

	if(arguments.length-1 > 0)
	{
		var args = new Array();
		for(var ct=1;ct<ajax.arguments.length;ct++)
			args[ct-1] = ajax.arguments[ct];
		//args.push(ajax.arguments[ct]);

	}
	else
		args = null;
//	alert('teste do sistema');
	// para navegadores que seguem o padrao (mozzila, firefox, etc)
	if ( window.XMLHttpRequest )
	{
		try
		{

			var xml;
			xml = new XMLHttpRequest();
			xml.args = args;
			xml.personalCallback = funcaoRetorno;
			xml.onreadystatechange = function(){
				if( xml.readyState > 3 ) {
					if ( xml.status == 200 ) {
						xml.personalCallback( xml.responseXML, xml.args )
					} else if (xml.status == 500){
						alert('N\u00e3o existem Componentes curriculares vinculados para a S\u00e9rie/Ano desta escola. Verifique em Cadastros > S\u00e9rie > Escola-s\u00e9rie se os Componentes curriculares foram selecionados/marcados para esta S\u00e9rie/Ano.');
					}else {
						alert('Erro: '+xml.status);
					}
				} };
			xml.envia = function(){xml.open("GET",addRandToURL(arguments[0]),true);xml.send(null)};
			return xml;
		} catch(e)
		{
			alert("Erro ajax: " + e.description);
		}
	}
	// pro IE :/
	else if ( window.ActiveXObject )
	{
		// pro bug infernal do IE
		var This = this;
		try
		{
			this.xml = new ActiveXObject( "Microsoft.XMLHTTP" );
			This.args = args;
			This.personalCallback = function() { funcaoRetorno(  This.xml.responseXML,This.args ); };
			This.callback=function(){ if( This.xml.readyState > 3 ) { if ( This.xml.status == 200 ) { This.personalCallback() } else { alert('erro'); } } };
			This.xml.onreadystatechange = function(){ This.callback() };
			This.envia = function(){This.xml.open("GET",addRandToURL(arguments[0]),true);This.xml.send()};
		} catch(e)
		{
			alert("Erro ajax: " + e.description);
		}
	}
}

function addRandToURL( url )
{
	var randVal = Math.round(Math.random() * 10000 );
	if(url.split('?').length > 1)
	{
		return url + '&rand=' + randVal;
	}
	return url + '?rand=' + randVal;
}


var DOM_opcao = 0;
var DOM_total = 0;
var DOM_itensArray = new Array();
var DOM_xmlhttp
var DOM_execute_when_xmlhttpChange = 0;
var DOM_divs = new Array();
/*
	UTILITIES
*/
function addEvent(elm, evType, fn, useCapture) {
	if (elm.addEventListener) {
		elm.addEventListener(evType, fn, useCapture);
		return true;
	}
	else if (elm.attachEvent) {
		var r = elm.attachEvent('on' + evType, fn);
		return r;
	}
	else {
		elm['on' + evType] = fn;
	}
}

function getElementsByClass(searchClass,node,tag) {
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}

function toggle(obj) {
	var el = document.getElementById(obj);
	if ( el.style.display != 'none' ) {
		el.style.display = 'none';
	}
	else {
		el.style.display = '';
	}

}

function insertAfter(parent, node, referenceNode) {
	parent.insertBefore(node, referenceNode.nextSibling);
}

Array.prototype.inArray = function (value) {
	var i;
	for (i=0; i < this.length; i++) {
		if (this[i] === value) {
			return true;
		}
	}
	return false;
};

function getCookie( name ) {
	var start = document.cookie.indexOf( name + "=" );
	var len = start + name.length + 1;
	if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) ) {
		return null;
	}
	if ( start == -1 ) return null;
	var end = document.cookie.indexOf( ";", len );
	if ( end == -1 ) end = document.cookie.length;
	return unescape( document.cookie.substring( len, end ) );
}

function setCookie( name, value, expires, path, domain, secure ) {
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires ) {
		expires = expires * 1000 * 60 * 60 * 24;
	}//DOM_expansivel
	var expires_date = new Date( today.getTime() + (expires) );
	document.cookie = name+"="+escape( value ) +
		( ( expires ) ? ";expires="+expires_date.toGMTString() : "" ) + //expires.toGMTString()
		( ( path ) ? ";path=" + path : "" ) +
		( ( domain ) ? ";domain=" + domain : "" ) +
		( ( secure ) ? ";secure" : "" );
}

/*
	CONTROLE DE JANELAS
*/
function centralizaExpansivel() {
  screenWidth = 0;
  screenHeight = 0;

  for (let i = 0; i < DOM_divs.length; i++) {
    let expansivel = DOM_divs[i];
    let largura = expansivel.offsetWidth;
    let altura = expansivel.offsetHeight;

    expansivel.style.position = 'fixed';
    expansivel.style.top = 'calc(50% - ' + (altura / 2) + 'px)';
    expansivel.style.left = 'calc(50% - ' + (largura / 2) + 'px)';
  }
}

 function insertAfter( node, referenceNode)
 {
 	referenceNode.parentNode.insertBefore(node, referenceNode.nextSibling);
 }

function showExpansivel( largura, altura, conteudo )
{

	expansivel = document.createElement("div");
	exp_id = DOM_divs.length;
	expansivel.setAttribute("id", "div_dinamico_"+exp_id);
	insertAfter(expansivel, document.getElementById("DOM_expansivel"));
	DOM_divs[exp_id] = expansivel;
	expansivel.style.zIndex = 1003+exp_id;
	expansivel.style.position = "absolute";

	if( typeof window.innerHeight == 'number' )
	{
		screenHeight = window.innerHeight;
		screenWidth = window.innerWidth;
	}
	else if( typeof document.body.offsetHeight == 'number' )
	{
		screenHeight = document.body.offsetHeight;
		screenWidth = document.body.offsetWidth;
	}
	else if( document.documentElement && typeof document.documentElement.clientWidth == 'number' )
	{
		screenHeight = document.documentElement.clientHeight;
		screenWidth = document.documentElement.clientWidth;
	}

	if( typeof arguments[3] == "string" )
	{
		titulo = arguments[3];
	}
	else
	{
		titulo = ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
	}
	var cliqueFecha = '<a href="javascript:void(0);" id="linkFechar" onclick="fechaExpansivel( \'div_dinamico_'+exp_id+'\');" ><img src="/intranet/imagens/close.png" border="0" width="17" height="17"></a>';
	if (typeof arguments[3] == "number")
	{
		cliqueFecha = '';
	}

	conteudoMoldurado = '<table border="0" id="tabela_conteudo" cellpadding="0" cellspacing="0" width="100%"><tr><td width="9" height="44" valign="top"></td><td id="modal-title" height="44" valign="top">'+ titulo + '</td><td id="modal-close" '+cliqueFecha+'</td><td width="9" height="44" valign="top"></td></tr><tr><td  width="9">&nbsp;</td><td bgcolor="#FFFFFF" colspan="2"><div id="expansivel_conteudo" class="modal-domjs-conteudo" style="overflow:hidden;">';
	conteudoMoldurado += conteudo;
	conteudoMoldurado += '</div></td><td width="9">&nbsp;</td></tr><tr><td width="9" height="20" valign="top"></td><td colspan="2"  height="20">&nbsp;</td><td width="9" height="20" valign="top"></td></tr></table>';
	expansivel.innerHTML = conteudoMoldurado;


	/*
		Tamanho da janela
	*/
	if(altura != 0)
	{
		expansivel.style.height = altura + 'px';
		document.getElementById("expansivel_conteudo").style.height = altura + 'px';
	}
	if(expansivel.offsetHeight > screenHeight-100 )
	{
		document.getElementById("expansivel_conteudo").style.height = screenHeight - 100;
		expansivel.style.height =  document.getElementById("tabela_conteudo").offsetHeight;
	}

	expansivel.style.display = 'block';

	if(largura != 0)
	{
		expansivel.style.width = largura + 'px';
		document.getElementById("expansivel_conteudo").style.width = largura + 'px';
	}
	else
	{
		expansivel.style.width = expansivel.offsetWidth;
	}

	centralizaExpansivel();
}

function showExpansivelIframe( largura, altura, URL, fecha )
{
	showExpansivel( largura, altura, '<iframe src="' + URL + '" frameborder="0" height="100%" width="' + ( largura - 1 ) + '" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', fecha );
}

function showExpansivelImprimir( largura, altura, arquivo, array,  titulo )
{

	expansivel = document.createElement("div");

	exp_id = DOM_divs.length;
	expansivel.setAttribute("id", "div_dinamico_"+exp_id);
	insertAfter(expansivel, document.getElementById("DOM_expansivel"));
	DOM_divs[exp_id] = expansivel;
	expansivel.style.position = "absolute";
	expansivel.style.zIndex = 9;


	url = '';
	junta = '?';
	var value;
	if(array)
		for( i = 0; i < array.length; i++ )
		{
			if(document.getElementById( array[i] ).type == 'checkbox')
				value = document.getElementById( array[i] ).checked ? 'on': '';
			else
				value = document.getElementById( array[i] ).value;
			url += junta+array[i]+'='+value;
			junta = '&';
		}

	if( typeof window.innerHeight == 'number' )
	{
		screenHeight = window.innerHeight;
		screenWidth = window.innerWidth;
	}
	else if( typeof document.body.offsetHeight == 'number' )
	{
		screenHeight = document.body.offsetHeight;
		screenWidth = document.body.offsetWidth;
	}
	else if( document.documentElement && typeof document.documentElement.clientWidth == 'number' )
	{
		screenHeight = document.documentElement.clientHeight;
		screenWidth = document.documentElement.clientWidth;
	}


	conteudoMoldurado = '<table border="0" id="tabela_conteudo" cellpadding="0" cellspacing="0" ><tr><td width="9" height="44" valign="top"></td><td height="44" valign="top">' + titulo + '</td><td height="44" align="right"><a href="#" id="linkFechar" onclick="fechaExpansivel( \'div_dinamico_'+exp_id+'\');" ><img src="/intranet/imagens/moldura/close.png" border="0" width="17" height="17"></a></td><td width="9" height="44" valign="top"></td></tr><tr><td width="9">&nbsp;</td><td bgcolor="#FFFFFF" colspan="2"><div id="expansivel_conteudo" class="modal-domjs-conteudo" style="overflow:auto;"><div id="LoadImprimir"><img style="margin-bottom: -8px;" src=\'imagens/carregando1.gif\'>Carregando...</div>';
	conteudoMoldurado += '<iframe name=\'miolo_' + exp_id + '\' id=\'miolo_' + exp_id + '\' frameborder=\'0\' height=\'100%\' width=\'100%\' marginheight=\'0\' marginwidth=\'0\' src=\''+arquivo+url+'\'></iframe>';
	conteudoMoldurado += '</div></td><td width="9">&nbsp;</td></tr><tr><td width="9" height="20" valign="top"></td><td colspan="2" height="20">&nbsp;</td><td width="9" height="20" valign="top"></td></tr></table>';
	expansivel.innerHTML = conteudoMoldurado;


	/*
		Tamanho da janela
	*/
	if(altura != 0)
	{
		expansivel.style.height = altura + 'px';
		document.getElementById("expansivel_conteudo").style.height = altura + 'px';
	}
	if(expansivel.offsetHeight > screenHeight-100 )
	{
		document.getElementById("expansivel_conteudo").style.height = screenHeight - 100;
		expansivel.style.height =  document.getElementById("tabela_conteudo").offsetHeight;
		//alert(document.getElementById("tabela_conteudo").offsetHeight);

	}

	expansivel.style.display = 'block';

	if(largura != 0)
	{
		expansivel.style.width = largura + 'px';
		document.getElementById("expansivel_conteudo").style.width = largura + 'px';
	}
	else
	{
		expansivel.style.width = expansivel.offsetWidth;
	}


	centralizaExpansivel(expansivel);
	document.onscroll = function() { centralizaExpansivel(); };
}

function fechaExpansivel(expansivel)
{
	//alert( expansivel );
	document.getElementById(expansivel).style.display = 'none';
	document.onscroll = function() { return false; };
}


/*
	HINTS EM DRH -> FUNCIONARIOS
*/

function DOM_trocaClasse( id )
{
	obj = document.getElementById( "linha_" + DOM_opcao );
	if( typeof obj == "object" )
	{
		obj = document.getElementById( "linha_" + id );
		if( typeof obj == "object" )
		{
			linhaOpcao = document.getElementById( "linha_" + DOM_opcao );
			linhaId = document.getElementById( "linha_" + id );

			if( typeof linhaOpcao != "undefined" )
			{
				linhaOpcao.className = "DOM_listaNormal";
				linhaId.className = "DOM_listaSelecionado";
				if( typeof DOM_itensArray[id] != "undefined" )
				{
					DOM_setVal( DOM_itensArray[id].firstChild.data );
					DOM_opcao = id;
				}
			}
		}
	}
}

function DOM_setVal( value )
{
	document.getElementById( DOM_atual.objectId ).value = value;
}

function DOM_navegaUpDown(e)
{
	var tecla = ( window.event ) ? event.keyCode : e.keyCode;
	if( tecla > 32 && tecla < 41 )
	{
		if( tecla == 33 )// page up || 36 Home
		{
			DOM_trocaClasse( 0 );
		} else if( tecla == 34 )// page down || 35 End
		{
			DOM_trocaClasse( total - 1 );
		} else if( tecla == 38 )// UP
		{
			if( DOM_opcao  ) {
				DOM_trocaClasse( DOM_opcao - 1 );
			}
			else
			{
				DOM_trocaClasse( DOM_opcao );
			}
		} else if( tecla == 40 )// DOWN
		{
			if( DOM_opcao + 1 < total )
			{
				DOM_trocaClasse( DOM_opcao + 1 );
			}
			else
			{
				DOM_trocaClasse( DOM_opcao );
			}
		}
	}
	else
	{
		if( tecla != 16 && tecla != 17 && tecla != 0 && tecla != 20 )
		{
			DOM_showHint();
		}
	}
}

function DOM_focusOut()
{
	//setTimeout( "document.getElementById( \"DOM_expansivel\" ).style.display = 'none';", 100 );
	document.getElementById( "DOM_expansivel" ).style.display = 'none';
}

function DOM_focusIn()
{
	DOM_acao = 1;
	if( DOM_itensArray.length  )
	{
		objExpansivel = document.getElementById( "DOM_expansivel" );
		objExpansivel.style.display = 'block';
		obj = document.getElementById( DOM_atual.objectId );
		posX = DOM_ObjectPosition_getPageOffsetLeft( obj );
		posY = DOM_ObjectPosition_getPageOffsetTop( obj );
		objExpansivel.style.left = posX - 1;
		objExpansivel.style.top = posY + 21;
	}
}

function DOM_showHint()
{
	obj = document.getElementById( DOM_atual.objectId );
	minChars = 3;
	strText = obj.value;
	if( strText.length >= minChars )
	{
		DOM_loadXMLDoc( "xml_pessoas.php?s=" + strText );
	}
	else
	{
		document.getElementById( "DOM_expansivel" ).style.display = 'none';
	}
}

function DOM_preencheSelect( DOM_itensArray )
{
	if( DOM_itensArray.length )
	{
		txt = "<table border=\"1\" cellpading=\"0\" cellspacing=\"1\" style=\"width:100%\">";
		for( i = 0; i < DOM_itensArray.length; i++ )
		{
			classe = ( i != DOM_opcao )? "DOM_listaNormal": "DOM_listaSelecionado";
			obj = DOM_itensArray[i].firstChild;
			txt += "<tr><td id=\"linha_" + i + "\" class=\"" + classe + "\"onclick=\"trocaClasse( '" + i + "' );\">" + obj.data + "</a></td></tr>\n";
		}
		total = i;
		txt += "</table>";

		objDom = document.getElementById( "DOM_expansivel" );
		objDom.innerHTML = txt;
		//objDom.style.display = "block";
		DOM_focusIn();
	}
}

/*
	HTTPREQUEST
*/

function DOM_loadXMLDoc( url )
{
	// code for Mozilla, etc.
	if ( window.XMLHttpRequest )
	{
		DOM_xmlhttp = new XMLHttpRequest();
		DOM_xmlhttp.onreadystatechange=DOM_xmlhttpChange;
		DOM_xmlhttp.open( "GET", url, true );
		DOM_xmlhttp.send( null );
	}
	// code for IE
	else if ( window.ActiveXObject )
	{
		DOM_xmlhttp = new ActiveXObject( "Microsoft.XMLHTTP" );
		if ( DOM_xmlhttp )
		{
			DOM_xmlhttp.onreadystatechange=DOM_xmlhttpChange;
			DOM_xmlhttp.open( "GET", url, true );
			DOM_xmlhttp.send();
		}
	}
}

function DOM_xmlhttpChange()
{
	// if xmlhttp shows "loaded"
	if( DOM_xmlhttp.readyState > 3 )
	{
		// if "OK"
		if ( DOM_xmlhttp.status == 200 )
		{
			DOM_itensArray = DOM_xmlhttp.responseXML.getElementsByTagName( "item" );
			if( typeof DOM_acao != "undefined" )
			{
				if( DOM_acao == 1 )
				{
					DOM_preencheSelect( DOM_itensArray );
				}
				else if( DOM_acao == 2 )
				{
					preencheLink( DOM_itensArray );
				}
			}
			else if( typeof DOM_execute_when_xmlhttpChange == "function" )
			{
				DOM_execute_when_xmlhttpChange();
			}
		}
		else
		{
			document.getElementById( "DOM_expansivel" ).innerHTML = "Erro obtendo dados [" + DOM_xmlhttp.status + "]";
			document.getElementById( "DOM_expansivel" ).style.display = "block";
		}
	}
}

/*
	LOCALIZACAO DE ITENS NA PAGINA
*/

function DOM_ObjectPosition_getPageOffsetLeft ( el )
{
	return DOM_ObjectPosition_getPageOffsetTopLeft( el,"Left" );
}
function DOM_ObjectPosition_getPageOffsetTop ( el )
{
	return DOM_ObjectPosition_getPageOffsetTopLeft( el,"Top" );
}
function DOM_ObjectPosition_getPageOffsetTopLeft( el,type )
{
	if ( el.offsetParent && el.parentNode && el.offsetParent==document.body && el.parentNode!=document.body )
	{
		var considerScrolls = true;
		if ( window.opera )
		{
			considerScrolls = false;
		}
		var offset=el["offset"+type];
		while( ( el=el.parentNode ) != null )
		{
			if ( el==document.body )
			{
				if ( typeof( el["offset"+type] )=="number" )
				{
					offset += el["offset"+type];
				}
				considerScrolls = false;
			}
			if ( considerScrolls && el!=document.body && el["scroll"+type] )
			{
				offset -= el["scroll"+type];
			}
		}
		return offset;
	}
	else
	{ //IE
		var offset=el["offset"+type];
		var padding=0;
		var considerScrolls = true;
		if (window.opera)
		{
			considerScrolls = false;
		}
		while ( ( el=el.offsetParent ) != null )
		{
			padding+=2;
			offset += el["offset"+type];
			if ( considerScrolls && el!=document.body && el["scroll"+type] )
			{
				offset -= el["scroll"+type];
			}
		}

		return offset;
	}
}

function EscondeDiv(nome)
{
	document.getElementById(nome).style.visibility = 'hidden';
}

function atualizaEscola(campo){
	document.getElementById(campo).options.length=1;
	var length = 1;

	for( i = 0; i < DOM_itensArray.length; i++ )
	{
		objXML = DOM_itensArray[i].firstChild;
		var valor = objXML.data;
		var atributo = DOM_itensArray[i].getAttribute('cod_escola');

		document.getElementById(campo).options[length]= new Option(valor, atributo,false,false);
		length++;
	}

}

function buscaTabela(campo)
{
	obj = document.getElementById(campo);
	obj.disabled=true;
	obj.options[0]= new Option('Carregando', '',false,false);

	/// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { atualizaTabela(campo); };
	var source = document.getElementById('schema_');
	var valor = source.options[source.selectedIndex].value;
	strURL = "xml_tabelas.php?schema="+valor;
	DOM_loadXMLDoc( strURL );
}

function atualizaTabela(campo)
{
	obj = document.getElementById(campo);
	obj.disabled=true;
	obj.options[0]= new Option('Selecione', '',false,false);

	obj.options.length=1;
	var length = 1;
	for( i = 0; i < DOM_itensArray.length; i++ )
	{
		objXML = DOM_itensArray[i].firstChild;
		var valor = objXML.data;
		obj.options[length]= new Option(valor, valor,false,false);
		length++;
	}
	obj.disabled=false;
}
