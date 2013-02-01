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
			xml.onreadystatechange = function(){ if( xml.readyState > 3 ) { if ( xml.status == 200 ) { xml.personalCallback( xml.responseXML, xml.args ) } else { alert('Erro: '+xml.status); } } };
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

function addLoadEvent(func)
{
	var oldonload = window.onload;
	if (typeof window.onload != 'function')
	{
		window.onload = func;
	}
	else {
		window.onload = function()
		{
			oldonload();
			func();
		}
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

function deleteCookie( name, path, domain )
{
	if ( getCookie( name ) ) document.cookie = name + "=" +
		( ( path ) ? ";path=" + path : "") +
		( ( domain ) ? ";domain=" + domain : "" ) +
		";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}

/*
	CONTROLE DE JANELAS
*/
function centralizaExpansivel(expansivel)
{
	screenWidth = 0;
	screenHeight = 0;
	for( i = 0; i<DOM_divs.length;i++)
	{
		expansivel = DOM_divs[i];
		largura = expansivel.offsetWidth;
		altura = expansivel.offsetHeight;
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
		else
		{
			alert( "Este navegador nao suporta os recursos desta pagina.\nPor favor contacte o CTIMA (9296) para que possamos adaptar o sistema para a sua configuracao." );
		}


		if( typeof window.pageXOffset == 'numeric' )
		{
			scrollY = window.pageYOffset;
			scrollX = window.pageXOffset;
		}
		else
		{
			scrollY = document.body.scrollTop;
			scrollX = document.body.scrollLeft;
		}

		expansivel.style.top = Math.round( ( screenHeight / 2 ) - ( altura / 2 ) ) + scrollY;
		expansivel.style.left = Math.round( ( screenWidth / 2 ) - ( largura / 2 ) ) + scrollX;
	}
}

/*
expansivel = document.getElementById( "DOM_expansivel" );

	expansivel.style.width = largura + 'px';
	expansivel.style.height = altura + 'px';

	centralizaExpansivel();

	// fffee0
	//expansivel.innerHTML = '<table border="1" cellpadding="2" cellspacing="0" width="' + largura + '" height="' + altura + '" style="border-style: solid; border-color: #000000;border-width: 2px;background-color: #' + bgColor + ';"><tr><td align="center" style="background-color: #' + bgColor + ';">' + conteudo + '</td></tr></table>';
	//expansivel.innerHTML = conteudo;
	if( typeof arguments[3] == "string" )
	{
		titulo = arguments[3];
	}
	else
	{
		titulo = ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ';
	}
	conteudoMoldurado = '<table border="0" cellpadding="0" cellspacing="0" width="' + largura + '" height="' + altura + '"><tr><td width="9" height="44" valign="top"><img src="imagens/moldura/top_01.gif" border="0" width="9" height="44"></td><td background="imagens/moldura/top_04.gif" height="44" valign="top"><table border="0" cellpadding="0" cellspacing="0" height="44"><tr><td background="imagens/moldura/top_02.gif" height="44">' + titulo + '</td><td width="44" height="44" valign="top"><img src="imagens/moldura/top_03.gif" border="0" width="44" height="44"></td></tr></table></td><td background="imagens/moldura/top_04.gif" height="44" align="right"><a href="javascript: fechaExpansivel();"><img src="imagens/moldura/top_bot.jpg" border="0" width="17" height="17"></a></td><td width="9" height="44" valign="top"><img src="imagens/moldura/top_05.gif" border="0" width="9" height="44"></td></tr><tr><td background="imagens/moldura/meio_esq.jpg" width="9">&nbsp;</td><td bgcolor="#FFFFFF" colspan="2">';
	conteudoMoldurado += conteudo;
	conteudoMoldurado += '</td><td background="imagens/moldura/meio_dir.jpg" width="9">&nbsp;</td></tr><tr><td width="9" height="20" valign="top"><img src="imagens/moldura/bottom_01.jpg" width="9" height="20"></td><td colspan="2" background="imagens/moldura/bottom_02.jpg" height="20">&nbsp;</td><td width="9" height="20" valign="top"><img src="imagens/moldura/bottom_03.jpg" width="9" height="20"></td></tr></table>';
	expansivel.innerHTML = conteudoMoldurado;

	expansivel.style.display = 'block';
	expansivel.style.width = largura + 'px';
	expansivel.style.height = altura + 'px';
	*/

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
	expansivel.style.zIndex = 30+exp_id;
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
	var cliqueFecha = '<a href="javascript:void(0);" id="linkFechar" onclick="fechaExpansivel( \'div_dinamico_'+exp_id+'\');" ><img src="imagens/moldura/top_bot.jpg" border="0" width="17" height="17"></a>';
	if (typeof arguments[3] == "number")
	{
		cliqueFecha = '';
	}
//	conteudoMoldurado = '<table border="0" id="tabela_conteudo" cellpadding="0" cellspacing="0" width="100%"><tr><td width="9" height="44" valign="top"><img src="imagens/moldura/top_01.gif" border="0" width="9" height="44"></td><td background="imagens/moldura/top_04.gif" height="44" valign="top"><table border="0" cellpadding="0" cellspacing="0" height="44"><tr><td background="imagens/moldura/top_02.gif" height="44">' + titulo + '</td><td width="44" height="44" valign="top"><img src="imagens/moldura/top_03.gif" border="0" width="44" height="44"></td></tr></table></td><td background="imagens/moldura/top_04.gif" height="44" align="right"><a href="javascript:void(0);" id="linkFechar" onclick="fechaExpansivel( \'div_dinamico_'+exp_id+'\');" ><img src="imagens/moldura/top_bot.jpg" border="0" width="17" height="17"></a></td><td width="9" height="44" valign="top"><img src="imagens/moldura/top_05.gif" border="0" width="9" height="44"></td></tr><tr><td background="imagens/moldura/meio_esq.jpg" width="9">&nbsp;</td><td bgcolor="#FFFFFF" colspan="2"><div id="expansivel_conteudo" style="overflow:auto;">';
	conteudoMoldurado = '<table border="0" id="tabela_conteudo" cellpadding="0" cellspacing="0" width="100%"><tr><td width="9" height="44" valign="top"><img src="imagens/moldura/top_01.gif" border="0" width="9" height="44"></td><td background="imagens/moldura/top_04.gif" height="44" valign="top"><table border="0" cellpadding="0" cellspacing="0" height="44"><tr><td background="imagens/moldura/top_02.gif" height="44">' + titulo + '</td><td width="44" height="44" valign="top"><img src="imagens/moldura/top_03.gif" border="0" width="44" height="44"></td></tr></table></td><td background="imagens/moldura/top_04.gif" height="44" align="right">'+cliqueFecha+'</td><td width="9" height="44" valign="top"><img src="imagens/moldura/top_05.gif" border="0" width="9" height="44"></td></tr><tr><td background="imagens/moldura/meio_esq.jpg" width="9">&nbsp;</td><td bgcolor="#FFFFFF" colspan="2"><div id="expansivel_conteudo" style="overflow:auto;">';
	conteudoMoldurado += conteudo;
	conteudoMoldurado += '</div></td><td background="imagens/moldura/meio_dir.jpg" width="9">&nbsp;</td></tr><tr><td width="9" height="20" valign="top"><img src="imagens/moldura/bottom_01.jpg" width="9" height="20"></td><td colspan="2" background="imagens/moldura/bottom_02.jpg" height="20">&nbsp;</td><td width="9" height="20" valign="top"><img src="imagens/moldura/bottom_03.jpg" width="9" height="20"></td></tr></table>';
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

function showExpansivelIframe( largura, altura, URL, fecha )
{
	showExpansivel( largura, altura, '<iframe src="' + URL + '" frameborder="0" height="100%" width="' + ( largura - 1 ) + '" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', fecha );
}


function showExpansivelUpload( largura, altura )
{
	showExpansivel( largura, altura, '<iframe src="upload.php" frameborder="0" height="100%" width="' + ( largura - 1 ) + '" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>' );
}

function showExpansivelImprimir( largura, altura, arquivo, array,  titulo )
{

	expansivel = document.createElement("div");

	exp_id = DOM_divs.length;
	expansivel.setAttribute("id", "div_dinamico_"+exp_id);
	insertAfter(expansivel, document.getElementById("DOM_expansivel"));
	DOM_divs[exp_id] = expansivel;
	expansivel.style.position = "absolute";


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


	conteudoMoldurado = '<table border="0" id="tabela_conteudo" cellpadding="0" cellspacing="0" ><tr><td width="9" height="44" valign="top"><img src="imagens/moldura/top_01.gif" border="0" width="9" height="44"></td><td background="imagens/moldura/top_04.gif" height="44" valign="top"><table border="0" cellpadding="0" cellspacing="0" height="44"><tr><td background="imagens/moldura/top_02.gif" height="44">' + titulo + '</td><td width="44" height="44" valign="top"><img src="imagens/moldura/top_03.gif" border="0" width="44" height="44"></td></tr></table></td><td background="imagens/moldura/top_04.gif" height="44" align="right"><a href="#" id="linkFechar" onclick="fechaExpansivel( \'div_dinamico_'+exp_id+'\');" ><img src="imagens/moldura/top_bot.jpg" border="0" width="17" height="17"></a></td><td width="9" height="44" valign="top"><img src="imagens/moldura/top_05.gif" border="0" width="9" height="44"></td></tr><tr><td background="imagens/moldura/meio_esq.jpg" width="9">&nbsp;</td><td bgcolor="#FFFFFF" colspan="2"><div id="expansivel_conteudo" style="overflow:auto;"><div id="LoadImprimir"><img style="margin-bottom: -8px;" src=\'imagens/carregando1.gif\'>Carregando...</div>';
	conteudoMoldurado += '<iframe name=\'miolo_' + exp_id + '\' id=\'miolo_' + exp_id + '\' frameborder=\'0\' height=\'100%\' width=\'100%\' marginheight=\'0\' marginwidth=\'0\' src=\''+arquivo+url+'\'></iframe>';
	conteudoMoldurado += '</div></td><td background="imagens/moldura/meio_dir.jpg" width="9">&nbsp;</td></tr><tr><td width="9" height="20" valign="top"><img src="imagens/moldura/bottom_01.jpg" width="9" height="20"></td><td colspan="2" background="imagens/moldura/bottom_02.jpg" height="20">&nbsp;</td><td width="9" height="20" valign="top"><img src="imagens/moldura/bottom_03.jpg" width="9" height="20"></td></tr></table>';
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




/*

function DOM_showLinks(e)
{
	loadXMLDoc( "teste_xml.php?s=" + atual.searchString );
}

function DOM_closeLinks()
{
	document.getElementById( "expansivel" ).style.display = 'none';
}

function DOM_focusInLink()
{
	DOM_acao = 2;
	if( DOM_itensArray.length  )
	{
		objExpansivel = document.getElementById( "expansivel" );
		objExpansivel.style.display = 'block';
		obj = document.getElementById( atual.objectId );
		posX = ObjectPosition_getPageOffsetLeft( obj );
		posY = ObjectPosition_getPageOffsetTop( obj );
		objExpansivel.style.left = posX - 1;
		objExpansivel.style.top = posY + 12;
	}
}

function preencheLink( itensArray )
{
	if( itensArray.length )
	{
		txt = "<table border=\"0\" cellpading=\"0\" cellspacing=\"0\" style=\"width:100%\">";
		for( i = 0; i < itensArray.length; i++ )
		{
			classe = ( i != opcao )? "DOM_listaNormal": "DOM_listaSelecionado";
			obj = itensArray[i].firstChild;
			txt += "<tr><td id=\"linha_" + i + "\" class=\"" + classe + "\" onmouseover=\"DOM_trocaClasse( DOM_opcao, '" + i + "');DOM_opcao=" + i + ";\"><a href=\"\" title=\"" + obj.data + "\" class=\"DOM_multilink\">" + obj.data + "</a></td></tr>\n";
		}
		total = i;
		txt += "</table>";

		objDom = document.getElementById( "expansivel" );
		objDom.innerHTML = txt;
		//objDom.style.display = "block";
		DOM_focusInLink();
	}
}
*/

/*
	FUNCOES PARA TRATAR O OUTPUT DE UM HTTPREQUEST
	(personalizado para cada pagina)
*/

function diaria_carrega_valores()
{
	dataPartida = document.getElementById( "data_partida" ).value;
	horaPartida = document.getElementById( "hora_partida" ).value;
	dataChegada = document.getElementById( "data_chegada" ).value;
	horaChegada = document.getElementById( "hora_chegada" ).value;

	tipo_estadual = document.getElementById( "estadual" ).value;
	tipo_grupo = document.getElementById( "ref_cod_diaria_grupo" ).value;

	// verifica se todos eles estao preenchidos
	if( dataPartida != "" && horaPartida != "" && dataChegada != "" && horaChegada != "" && tipo_estadual != "" && tipo_grupo != "" )
	{
		// define qual a funcao que devera ser executada quando o xml for carregado
		DOM_execute_when_xmlhttpChange = function() { diaria_trata_valores(); };

		dataPartida = dataPartida.split( "/" ).join( "_" );
		horaPartida = horaPartida.split( ":" ).join( "_" );
		dataChegada = dataChegada.split( "/" ).join( "_" );
		horaChegada = horaChegada.split( ":" ).join( "_" );
		
		chegadaDataArr = dataChegada.split( "_" );
		chegadaCompara = chegadaDataArr[2] + '_' + chegadaDataArr[1] + '_' + chegadaDataArr[0] + '_' + horaChegada;
		
		partidaDataArr = dataPartida.split( "_" );
		partidaCompara = partidaDataArr[2] + '_' + partidaDataArr[1] + '_' + partidaDataArr[0] + '_' + horaPartida;

		if( chegadaCompara > partidaCompara )
		{
			if( dataPartida.length == 10 && dataChegada.length == 10 && horaPartida.length == 5 && horaChegada.length == 5 )
			{
				document.getElementById( "sug100" ).value = "Carregando...";
				document.getElementById( "sug75" ).value = "Carregando...";
				document.getElementById( "sug50" ).value = "Carregando...";
				document.getElementById( "sug25" ).value = "Carregando...";

				strURL = "xml_diaria_sugestao.php?dp=" + dataPartida + "&hp=" + horaPartida + "&dc=" + dataChegada + "&hc=" + horaChegada + "&grupo=" + tipo_grupo + "&est=" + tipo_estadual;
				//alert( strURL );
				DOM_loadXMLDoc( strURL )
			}
		}
		else
		{
			alert( 'A data (e hora) de chegada devem ser maiores do que a data (e hora) de saida.' );
		}
	}
}

// Funcao para diaria_cad.php recebe dados do XML e preenche as sugestoes
function diaria_trata_valores()
{
	var valores = [];
	for( i = 0; i < DOM_itensArray.length; i++ )
	{
		objXML = DOM_itensArray[i].firstChild;
		valores[i] = objXML.data;
	}

	// preenche os campos de sugestao com os valores do XML
	document.getElementById( "sug100" ).value = valores[0];
	document.getElementById( "sug75" ).value = valores[1];
	document.getElementById( "sug50" ).value = valores[2];
	document.getElementById( "sug25" ).value = valores[3];
}

// Funcao para diaria_cad.php recebe dados do XML e preenche as sugestoes
function diaria_copia_valores()
{
	document.getElementById( "vl100" ).value = document.getElementById( "sug100" ).value;
	document.getElementById( "vl75" ).value = document.getElementById( "sug75" ).value;
	document.getElementById( "vl50" ).value = document.getElementById( "sug50" ).value;
	document.getElementById( "vl25" ).value = document.getElementById( "sug25" ).value;
}

function otopic_qtd_topicos( cod_grupo, cod_reuniao )
{
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { otopic_recarrega_pagina(); };

	strURL = "xml_otopic_qtdtopicos.php?&cr=" + cod_reuniao;
	DOM_loadXMLDoc( strURL );
}

function otopic_recarrega_pagina()
{
	var valores = [];
	for( i = 0; i < DOM_itensArray.length; i++ )
	{
		objXML = DOM_itensArray[i].firstChild;
		valores[i] = objXML.data;
	}

	// verifica se o numero de topicos eh diferente
	if( document.getElementById( "qtd_topicos" ).value != valores[0] )
	{
		if( confirm( 'Um novo tÃ³pico foi inserido para essa reuniÃ£o. VocÃª gostaia de atualizar a pagina ?' ) )
		{
			document.location.href = document.location.href;
		}
	}
	else
	{
		//alert( 'mesma qtd ' + valores[0] );
	}
}

function odes_renda( )
{
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { odes_recarrega_pagina(); };
	numero = document.getElementById('numero').value;
	idbai = document.getElementById('idbai').value;
	idlog = document.getElementById('idlog').value;
	cep = document.getElementById('cep').value;
	idpes = document.getElementById('cod_pessoa_fj').value;
	if(numero && idbai && idlog && cep)
	{
		strURL = "xml_odes_renda.php?idbai="+idbai+"&idlog="+idlog+"&cep="+cep+"&numero="+numero+"&idpes_atual="+idpes;
		DOM_loadXMLDoc( strURL );
	}
}


function atualiza_renda( )
{
	/// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { atualiza(); };
	if(document.getElementById('cod_pessoa_fj'))
	{
		var idpes = document.getElementById('cod_pessoa_fj').value;
		strURL = "xml_odes_renda.php?idpes="+idpes;
		DOM_loadXMLDoc( strURL );
	}

}

function atualiza()
{
	var valores = [];
	for( i = 0; i < DOM_itensArray.length; i++ )
	{
		objXML = DOM_itensArray[i].firstChild;
		valores[i] = objXML.data;
	}
	var renda_total_outros = parseFloat(valores[0]);
	var percapta = parseFloat(valores[0])/(parseInt(valores[1])+1);
	var pessoas = parseInt(valores[1]);
	var renda_total = 0;

	if(document.getElementById("outras").value != "")
	{
		renda_total = parseFloat((document.getElementById("outras").value).replace(".","").replace(",","."));
	}
	if(document.getElementById("aposentadoria").value != "")
	{
		renda_total += parseFloat((document.getElementById("aposentadoria").value.replace(".","")).replace(",","."));
	}
	if(document.getElementById("total_remuneracao").value != "")
	{
		renda_total += parseFloat((document.getElementById("total_remuneracao").value).replace(".","").replace(",","."));
	}
	if(document.getElementById("seguro_desemprego").value != "")
	{
		renda_total += parseFloat((document.getElementById("seguro_desemprego").value).replace(".","").replace(",","."));
	}
	if(document.getElementById("pensao").value != "")
	{
		renda_total += parseFloat((document.getElementById("pensao").value).replace(".","").replace(",","."));
	}

	document.getElementById("renda_total_ind").innerHTML = renda_total.toFixed(2);
	renda_total += renda_total_outros;
	document.getElementById("renda_total").innerHTML = renda_total.toFixed(2);
	document.getElementById("renda_percapta").innerHTML = (renda_total/(pessoas+1)).toFixed(2);
}


function atualiza2()
{

	var pessoas = 0;
	var renda_total = 0;
	if(document.getElementById("outras").value != "")
	{
		renda_total += Number((document.getElementById("outras").value).replace(".","").replace(",","."));
	}
	if(document.getElementById("aposentadoria").value != "")
	{
		renda_total += Number((document.getElementById("aposentadoria").value).replace(".","").replace(",","."));
	}
	if(document.getElementById("total_remuneracao").value != "")
	{
		renda_total += Number((document.getElementById("total_remuneracao").value).replace(".","").replace(",","."));
	}
	if(document.getElementById("seguro_desemprego").value != "")
	{
		renda_total += Number((document.getElementById("seguro_desemprego").value).replace(".","").replace(",","."));
	}
	if(document.getElementById("pensao").value != "")
	{
		renda_total += Number((document.getElementById("pensao").value).replace(".","").replace(",","."));
	}

	document.getElementById("renda_total_ind").innerHTML = renda_total.toFixed(2);
	document.getElementById("renda_total").innerHTML = renda_total.toFixed(2);
	document.getElementById("renda_percapta").innerHTML = (renda_total/(pessoas+1)).toFixed(2);

}

// Funï¿½ï¿½o para alterar a lista de fila disponï¿½vel por Instituiï¿½ï¿½o no sistema opencall
// no menu cadasotro de usuï¿½rio

function callCarregaFila( campo_modificar, campo_atual )
{
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { callFilaAtendimento( campo_modificar ); };
	strURL = "xml_call_fila.php?&cod_instituicao=" + document.getElementById(campo_atual).value;
	DOM_loadXMLDoc( strURL );
}


function callFilaAtendimento(campo_modificar, campo_atual)
{
	var valores = [];
	for( i = 0; i < DOM_itensArray.length; i++ )
	{
		objXML = DOM_itensArray[i].firstChild;
		valores[i] = objXML.data;
	}

	document.getElementById(campo_modificar).options.length=1;
	var length = 1;
	for(i = 0; i< valores.length; i+=2)
	{
		document.getElementById(campo_modificar).options[length]= new Option(valores[i+1], valores[i],false,false);
		length++;
	}

}

// Funï¿½ï¿½o para alterar a quantidade de produtos disponivel

function ce_disponivel( campo_modificar, campo_atual, estoque, valor_soma )
{

	// define qual a funcao que devera ser executada quando o xml for carregado
	//DOM_execute_when_xmlhttpChange = function() { ce_atualiza_disponivel( campo_modificar, campo_atual, valor_soma ); };

	var xml1 = new ajax(ce_atualiza_disponivel,campo_modificar,campo_atual,valor_soma);
	strURL = "xml_ce_disponivel.php?cod_produto=" + document.getElementById(campo_atual).value+"&cod_estoque="+estoque+"&rand="+Math.random()*100000000;
	xml1.envia(strURL);

	//strURL = "xml_ce_disponivel.php?cod_produto=" + document.getElementById(campo_atual).value+"&cod_estoque="+estoque+"&rand="+Math.random()*100000000;
	//DOM_loadXMLDoc( strURL );
}


function ce_atualiza_disponivel( objXML )
{
	var campo_modificar = arguments[1][0];
	var campo_atual = arguments[1][1];
	var valor_soma  = arguments[1][2];
	
	var itens = objXML.getElementsByTagName( "item" );
	
	var valores = [];
	for( i = 0; i < itens.length; i++ )
	{
		objXML = itens[i].firstChild;
		valores[i] = objXML.data;
	}
//	for(i = 0; i< valores.length; i+=2)
	for(i = 0; i< valores.length; i++)
	{
		//alert(valores[i]);
		/*var reducao = 0;
		var cod = document.getElementById(campo_atual).value;
		if( document.getElementById("quantidade_"+cod) )
		{
			reducao =  document.getElementById("quantidade_"+cod).value * 1;
		}

		document.getElementById(campo_modificar).value = valores[i] - reducao + parseInt(valor_soma);
		document.getElementById(campo_modificar.replace('[','_[')).value = valores[i] - reducao + parseInt(valor_soma);*/
		document.getElementById(campo_modificar).value = parseInt(valores[i]) + parseInt(valor_soma);
		
		if( document.getElementById(campo_modificar+'_') )
		{
			document.getElementById(campo_modificar+'_').value = parseInt(valores[i]) + parseInt(valor_soma);
		}
		else
		{
			document.getElementById(campo_modificar.replace('[','_[')).value = parseInt(valores[i]) + parseInt(valor_soma);
		}
	}
}

function oproDocumentoNextLvl( setorPai, proxNivel)
{
	if( typeof arguments[2] == 'string' )
	{
		nome = arguments[2];
	}
	else
	{
		nome = 'setor_';
	}
	nivel = proxNivel * 1;
	for( ; nivel < 5; nivel++ )
	{
		// desabilita todos os objetos de nivel maior
		obj = document.getElementById( nome + nivel );
		obj.disabled = true;
		obj.length=0;
		obj.options[0] = new Option( '-----------', '0', false, false );
	}

	if( setorPai )
	{
		obj = document.getElementById( nome + proxNivel );
		if( typeof obj == 'object' )
		{
			DOM_execute_when_xmlhttpChange = function() { oproDocumentoNextLvlDone( proxNivel, nome ); };
			strURL = "xml_oprot_setor.php?setor_pai=" + setorPai;
			if( typeof arguments[3] == 'string' )
			{
				strURL = "xml_oprot_setor_not_in.php?setor_pai=" + setorPai;
			}
			DOM_loadXMLDoc( strURL );
		}
	}
}

function oproDocumentoNextLvlNomeComleto( setorPai, proxNivel)
{
	if( typeof arguments[2] == 'string' )
	{
		nome = arguments[2];
	}
	else
	{
		nome = 'setor_';
	}
	nivel = proxNivel * 1;
	for( ; nivel < 5; nivel++ )
	{
		// desabilita todos os objetos de nivel maior
		obj = document.getElementById( nome + nivel );
		obj.disabled = true;
		obj.length=0;
		obj.options[0] = new Option( '-----------', '0', false, false );
	}

	if( setorPai )
	{
		obj = document.getElementById( nome + proxNivel );
		if( typeof obj == 'object' )
		{
			DOM_execute_when_xmlhttpChange = function() { oproDocumentoNextLvlDone( proxNivel, nome ); };
			strURL = "xml_oprot_setor.php?nm_completo=1&setor_pai=" + setorPai;
			if( typeof arguments[3] == 'string' )
			{
				strURL = "xml_oprot_setor_not_in.php?setor_pai=" + setorPai;
			}
			DOM_loadXMLDoc( strURL );
		}
	}
}

function oproDocumentoNextLvlDone( proxNivel, nome )
{
	// habilita o proximo nivel
	obj = document.getElementById( nome + proxNivel );
	obj.disabled = false;
	valores = new Array();
	for( i = 0; i < DOM_itensArray.length; i++ )
	{
		objXML = DOM_itensArray[i].firstChild;
		valores[i] = objXML.data;
	}

	if( valores.length )
	{
		obj.length=0;
		obj.options[0] = new Option( 'Selecione', '0', false, false );

		var length = 1;
		for(i = 0; i< valores.length; i+=2)
		{
			obj.options[length] = new Option( valores[i], valores[i+1], false, false );
			length++;
		}
	}
	else
	{
		obj.length=0;
		obj.options[0] = new Option( '-----------', '0', false, false );
	}
}

function getEndereco( rootDocument, desabilita)
{
	idpes = document.getElementById('proprietario').value;
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { getEnderecoPreenche( rootDocument, desabilita ); };

	strURL = "xml_endereco.php?idpes=" + idpes;
	//alert( strURL + ' => ' + rootDocument + ' => '+ desabilita);
	DOM_loadXMLDoc( strURL );
}

function getEnderecoPreenche(rootDocument, desabilita)
{
	var valores = [];
	for( i = 0; i < DOM_itensArray.length; i++ )
	{
		objXML = DOM_itensArray[i].firstChild;
		valores[i] = objXML.data;
	}
	//alert(valores);
	//alert(rootDocument+ ", " + desabilita+ ", " + valores[0]+ ", " + valores[1]+ ", " + valores[2]+ ", " + valores[3]+ ", " + valores[4]+ ", " + valores[5]+ ", " + valores[6]+ ", " + valores[7]+ ", " + valores[8]+ ", " + valores[9]+ ", " + valores[10]+ ", " + valores[11]+ ", " + valores[12]+ ", " + valores[13]);
	setValores(rootDocument, desabilita, valores[0], valores[1], valores[2], valores[3], valores[4], valores[5], valores[6], valores[7], valores[8], valores[9], valores[10], valores[11], valores[12], valores[13] );
}

function getPDFcoleta()
{
	nome = document.getElementById('nome').value;
	assinante = document.getElementById('assinante').value;
	numero_iptu = document.getElementById('numero_iptu').value;
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { getPDFcoletaDone(); };

	strURL = "xml_coleta_rel_adesao.php?nome="+nome+"&assinante="+assinante+"&numero_iptu="+numero_iptu;
	//alert( strURL + ' => ' + rootDocument + ' => '+ desabilita);
	DOM_loadXMLDoc( strURL );
	Obj = document.getElementById('imprimir');
	Obj.value = 'Gerando Arquivo...';
	Obj.onclick = function() { alert('O sistema estï¿½ gerando o arquivo. Aguarde!')};
	//document.location.href = strURL;
}

function getPDFcoletaDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	link = objXML.data;
	Obj = document.getElementById('imprimir');
	Obj.value = 'Clique para baixar arquivo!';
	Obj.onclick = function() { document.location.href = link;};
	//alert('Arquivo Concluï¿½do');
	document.location.href = link;
}

function getIPTUcoleta()
{
	numero_iptu = document.getElementById('numero_iptu').value;
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { getIPTUcoletaDone(); };

	strURL = "xml_coleta_iptu.php?numero_iptu="+numero_iptu;

	DOM_loadXMLDoc( strURL );


}

function getIPTUcoletaDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	valor = objXML.data;
	Obj = document.getElementById('retorno');
	Obj.value = valor;
	coletaUpdate(valor);
}
function coletaUpdate(valor)
{
	iptu = document.getElementById('numero_iptu').value;
	//alert(iptu);
	if(valor==1)
	{
		if (confirm("Passar cadastro Nï¿½"+iptu+" para 'Assiante'?"))
		{
				objForm = document.getElementById("formulario");
				objForm.action = 'coleta_update_status.php';
				objForm.submit();
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert('O nï¿½mero nï¿½o estï¿½ cadastrado na base de dados!');
	}
}

function atualiza_despesa()
{
	/// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { atualiza_despesa_updade(); };
	var pessoa = document.getElementById('idpes');
	if( pessoa )
	{
		var idpes = pessoa.value;
		strURL = "xml_odes_despesa.php?idpes="+idpes;
		DOM_loadXMLDoc( strURL );
	}
}

function atualiza_despesa_updade()
{
	var valores = [];
	for( i = 0; i < DOM_itensArray.length; i++ )
	{
		objXML = DOM_itensArray[i].firstChild;
		valores[i] = objXML.data;
	}
	var renda_total_outros = parseFloat(valores[0]);
	var percapta = parseFloat(valores[0])/(parseInt(valores[1])+1);
	var pessoas = parseInt(valores[1]);
	var renda_total = 0;

	var val = document.getElementById("aluguel").value;
	if( val != "")
	{
		renda_total += parseFloat(val.replace(".","").replace(",","."));
	}
	var val = document.getElementById("prestacao").value;
	if( val != "")
	{
		renda_total += parseFloat(val.replace(".","").replace(",","."));
	}
	var val = document.getElementById("alimentacao").value;
	if( val != "")
	{
		renda_total += parseFloat(val.replace(".","").replace(",","."));
	}
	var val = document.getElementById("agua").value;
	if( val != "")
	{
		renda_total += parseFloat(val.replace(".","").replace(",","."));
	}
	var val = document.getElementById("luz").value;
	if( val != "")
	{
		renda_total += parseFloat(val.replace(".","").replace(",","."));
	}
	var val = document.getElementById("transporte").value;
	if( val != "")
	{
		renda_total += parseFloat(val.replace(".","").replace(",","."));
	}
	var val = document.getElementById("medicamentos").value;
	if( val != "")
	{
		renda_total += parseFloat(val.replace(".","").replace(",","."));
	}
	var val = document.getElementById("gas").value;
	if( val != "")
	{
		renda_total += parseFloat(val.replace(".","").replace(",","."));
	}
	var val = document.getElementById("outras_despesas").value;
	if( val != "")
	{
		renda_total += parseFloat(val.replace(".","").replace(",","."));
	}
	var val = document.getElementById("dividendos").value;
	if( val != "")
	{
		renda_total += parseFloat(val.replace(".","").replace(",","."));
	}

	document.getElementById("despesa_total_ind").innerHTML = renda_total.toFixed(2);
	renda_total += renda_total_outros;
	document.getElementById("despesa_total").innerHTML = renda_total.toFixed(2);
	document.getElementById("despesa_percapta").innerHTML = (renda_total/(pessoas+1)).toFixed(2);
}
function EscondeDiv(nome)
{
	document.getElementById(nome).style.visibility = 'hidden';
}

function BuscaRua(campo){
	/// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { atualizaBairroLogCep(campo); };
	//var campo_ = document.getElementById(campo);
	//var valor = campo_.options[campo_.selectedIndex].value;

	/*if(valor != ""){
		get = "?"+campo+valor;
	}else{
		get = "?";
	}*/
	if(campo == 'nm_bairro'){
			strURL = "xml_bairro.php";//id_bairro="+id_bairro;
			DOM_loadXMLDoc( strURL );
	}else if(campo == 'nm_log'){
		var campo_ = document.getElementById('nm_bairro');
		var valor = campo_.options[campo_.selectedIndex].value;
		if(valor != ""){
			get = "?id_bairro="+valor;
		}else{
			get = "?";
		}
		strURL = "xml_logradouro.php"+get;
		DOM_loadXMLDoc( strURL );
	}else if(campo == 'cd_cep'){
		var campo_ = document.getElementById('nm_bairro');
		var valor = campo_.options[campo_.selectedIndex].value;
		if(valor != ""){
			bairro = "?id_bairro="+valor;
		}else{
			bairro = "";
		}

		var campo_ = document.getElementById('nm_log');
		var valor = campo_.options[campo_.selectedIndex].value;

		if(valor != ""){
			log = "&id_log="+valor;
		}else{
			log = "";
		}

		strURL = "xml_cep.php"+bairro+log;
		DOM_loadXMLDoc( strURL );
	}

}

function atualizaBairroLogCep(campo){
	document.getElementById(campo).options.length=1;
	var length = 1;
	if(campo == 'nm_bairro')
		attribute = 'idbai';
	else
		if(campo == 'nm_log')
			attribute = 'idlog';
		else
			if(campo == 'cd_cep')
				attribute = 'idcep';

		for( i = 0; i < DOM_itensArray.length; i++ )
		{
			objXML = DOM_itensArray[i].firstChild;
			var valor = objXML.data;
			var atributo = DOM_itensArray[i].getAttribute(attribute);

			document.getElementById(campo).options[length]= new Option(valor, atributo,false,false);
			length++;

		}

}

function trocaCampo(campo){
	if(campo == 'inicio'){
		document.getElementById('nm_log').disabled = true;
		document.getElementById('cd_cep').disabled = true;
	}else if(campo == 'nm_bairro'){
		var campo = document.getElementById('nm_bairro');
		if(campo.options[campo.selectedIndex].value == ''){
			document.getElementById('nm_log').disabled = true;
			document.getElementById('cd_cep').disabled = true;
			document.getElementById('nm_log').options.length = 1;
			document.getElementById('cd_cep').options.length = 1;
		}else{
			BuscaRua('nm_log');
			document.getElementById('nm_log').disabled = false;
			document.getElementById('cd_cep').options.length = 1;
			document.getElementById('cd_cep').disabled = true;
			//caregar xml
		}
	}else if(campo == 'nm_log'){
		if(document.getElementById('nm_log').value == ''){
			document.getElementById('cd_cep').disabled = true;
			document.getElementById('cd_cep').options.length = 1;
		}else{
			document.getElementById('cd_cep').disabled = false;
			BuscaRua('cd_cep');
			//caregar xml
		}
	}
}

function BuscaEscola(campo){
	/// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { atualizaEscola(campo); };

	var campo_ = document.getElementById('ref_cod_instituicao');
	var valor = campo_.options[campo_.selectedIndex].value;

	strURL = "xml_escola.php?cod_instituicao="+valor;
	DOM_loadXMLDoc( strURL );
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

function buscaEndereco(idpes)
{
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { atualizaEndereco(); };
	var source = document.getElementById('proprietario');
	var valor = source.options[source.selectedIndex].value;
	strURL = "xml_endereco.php?idpes="+valor;
	DOM_loadXMLDoc( strURL );
}

function atualizaEndereco()
{
	cep = ( DOM_itensArray[0].firstChild.data != 0 ) ? DOM_itensArray[0].firstChild.data: '';
	if( cep.length > 5 )
	{
		cep = cep.substr( 0, 5 ) + '-' + cep.substr(5,3);
	}
	document.getElementById("imovel_cep").value = cep;
	document.getElementById("imovel_num").value = ( DOM_itensArray[8].firstChild.data != 0 ) ? DOM_itensArray[8].firstChild.data: '';
	document.getElementById("imovel_letra").value = ( DOM_itensArray[9].firstChild.data != 0 ) ? DOM_itensArray[9].firstChild.data: '';
	document.getElementById("imovel_complemento").value = ( DOM_itensArray[10].firstChild.data != 0 ) ? DOM_itensArray[10].firstChild.data: '';
	document.getElementById("imovel_bloco").value = ( DOM_itensArray[11].firstChild.data != 0 ) ? DOM_itensArray[11].firstChild.data: '';
	document.getElementById("imovel_andar").value = ( DOM_itensArray[12].firstChild.data != 0 ) ? DOM_itensArray[12].firstChild.data: '';
	document.getElementById("imovel_apartamento").value = ( DOM_itensArray[13].firstChild.data != 0 ) ? DOM_itensArray[13].firstChild.data: '';
}


function getPDFouvidoriaEquipe()
{
	nm_equipe = document.getElementById('nm_equipe').value;
	num_integrantes = document.getElementById('num_integrantes').value;
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { getPDFouvidoriaEquipeDone(); };

	strURL = "xml_ouvidoria_rel_equipe.php?num_integrantes="+num_integrantes+"&nm_equipe="+nm_equipe;
	//alert( strURL + ' => ' + rootDocument + ' => '+ desabilita);
	DOM_loadXMLDoc( strURL );

	Obj = document.getElementById('imprimir');
	Obj.value = 'Gerando Arquivo...';
	Obj.onclick = function() { alert('O sistema está gerando o arquivo. Aguarde!')};
	//document.location.href = strURL;
}

function getPDFouvidoriaEquipeDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	link = objXML.data;
	Obj = document.getElementById('imprimir');
	Obj.value = 'Clique para baixar arquivo!';
	Obj.onclick = function() { document.location.href = link;};
	//alert('Arquivo Concluï¿½do');
	document.location.href = link;
}

function getPDFouvidoriaTipoServico()
{
	nm_tipo = document.getElementById('nm_tipo').value;
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { getPDFouvidoriaTipoServicoDone(); };

	strURL = "xml_ouvidoria_rel_tipo_servico.php?nm_tipo="+nm_tipo;
	//alert( strURL + ' => ' + rootDocument + ' => '+ desabilita);
	DOM_loadXMLDoc( strURL );
	Obj = document.getElementById('imprimir');
	Obj.value = 'Gerando Arquivo...';
	Obj.onclick = function() { alert('O sistema está gerando o arquivo. Aguarde!')};
	//document.location.href = strURL;
}

function getPDFouvidoriaAtendimento()
{
	//pegar parametros de busca
	cod_tipo_servico = document.getElementById('cod_tipo_servico').value;
	nm_pessoa_at = document.getElementById('nm_pessoa_at').value;
	cpf_at = document.getElementById('cpf_at').value;
	nm_pessoa_fin = document.getElementById('nm_pessoa_fin').value;
	tipo_atividade = document.getElementById('tipo_atividade').value; 
	ref_cod_setor = document.getElementById('ref_cod_setor').value;
	aberto = document.getElementById('aberto').value;
	descricao_atend = document.getElementById('descricao_atend').value;
	cod_atendimento = document.getElementById('cod_atendimento').value;
	data_cad = document.getElementById('data_cad').value;
	data_fim = document.getElementById('data_fim').value;
	idbai = document.getElementById('idbai').value;
	endereco = document.getElementById('endereco').value;
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { getPDFouvidoriaAtendimentoDone(); };

	strURL = "xml_ouvidoria_rel_atendimento.php?cod_tipo_servico="+cod_tipo_servico+"&nm_pessoa_at="+nm_pessoa_at+"&cpf_at="+cpf_at+"&nm_pessoa_fin="+nm_pessoa_fin+"&tipo_atividade="+tipo_atividade+"&ref_cod_setor="+ref_cod_setor+"&aberto="+aberto+"&descricao_atend="+descricao_atend+"&cod_atendimento="+cod_atendimento+"&data_cad="+data_cad+"&data_fim="+data_fim+"&idbai="+idbai+"&endereco="+endereco;
	//alert( strURL + ' => ' + rootDocument + ' => '+ desabilita);
	DOM_loadXMLDoc( strURL );
	Obj = document.getElementById('imprimir');
	Obj.value = 'Gerando Arquivo...';
	Obj.onclick = function() { alert('O sistema está gerando o arquivo. Aguarde!')};
}

function getPDFouvidoriaAtendimentoDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	link = objXML.data;
	Obj = document.getElementById('imprimir');
	Obj.value = 'Clique para baixar arquivo!';
	Obj.onclick = function() { document.location.href = link;};
	//alert('Arquivo Concluï¿½do');
	document.location.href = link;
}




function getPDFestoquesaida()
{
	//pegar parametros de busca
	var status = document.getElementById('status').value;
	var instituicao = document.getElementById('instituicao').value;

	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { getPDFestoquesaidaDone(); };

	strURL = "xml_ce_saida_produtos.php?instituicao="+instituicao+"&status="+status;
	//alert( strURL + ' => ' + rootDocument + ' => '+ desabilita);
	DOM_loadXMLDoc( strURL );
	Obj = document.getElementById('imprimir');
	Obj.value = 'Gerando Arquivo...';
	Obj.onclick = function() { alert('O sistema está gerando o arquivo. Aguarde!')};
}

function getPDFestoquesaidaDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	link = objXML.data;
	Obj = document.getElementById('imprimir');
	Obj.value = 'Clique para baixar arquivo!';
	Obj.onclick = function() { document.location.href = link;};
	//alert('Arquivo Concluï¿½do');
	document.location.href = link;
}

function getPDFouvidoriaAtendimentoSetor()
{
	cod_tipo_servico = document.getElementById('cod_tipo_servico').value;
	aberto = document.getElementById('aberto').value;
	cod_atendimento = document.getElementById('cod_atendimento').value;
	logradouro = document.getElementById('logradouro').value;
	bairro = document.getElementById('bairro').value;
	ref_cod_setor = document.getElementById('ref_cod_setor').value;
	
	DOM_execute_when_xmlhttpChange = function() { getPDFouvidoriaAtendimentoSetorDone(); };
	
	strURL = "xml_ouvidoria_rel_atendimento_setor.php?cod_tipo_servico="+cod_tipo_servico+"&aberto="+aberto+"&cod_atendimento="+cod_atendimento+"&logradouro="+logradouro+"&bairro="+bairro+"&ref_cod_setor="+ref_cod_setor;
	
	DOM_loadXMLDoc( strURL );
	Obj = document.getElementById('imprimir');
	Obj.value = 'Gerando Arquivo...';
	Obj.onclick = function() { alert('O sistema está gerando o arquivo. Aguarde!')};
	
}

function getPDFouvidoriaAtendimentoSetorDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	link = objXML.data;
	Obj = document.getElementById('imprimir');
	Obj.value = 'Clique para baixar arquivo!';
	Obj.onclick = function() { document.location.href = link;};
	//alert('Arquivo Concluï¿½do');
	document.location.href = link;
}

function getPDFouvidoriaTipoServicoDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	link = objXML.data;
	Obj = document.getElementById('imprimir');
	Obj.value = 'Clique para baixar arquivo!';
	Obj.onclick = function() { document.location.href = link;};
	//alert('Arquivo Concluï¿½do');
	document.location.href = link;
}
function getPDFouvidoriaOrdem()
{
	nm_tipo = document.getElementById('nm_tipo').value;
	// define qual a funcao que devera ser executada quando o xml for carregado
	DOM_execute_when_xmlhttpChange = function() { getPDFouvidoriaOrdemDone(); };

	strURL = "xml_ouvidoria_rel_tipo_servico.php?nm_tipo="+nm_tipo;
	//alert( strURL + ' => ' + rootDocument + ' => '+ desabilita);
	DOM_loadXMLDoc( strURL );
	Obj = document.getElementById('imprimir');
	Obj.value = 'Gerando Arquivo...';
	Obj.onclick = function() { alert('O sistema está gerando o arquivo. Aguarde!')};
	//document.location.href = strURL;
}

function getPDFouvidoriaOrdemDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	link = objXML.data;
	Obj = document.getElementById('imprimir');
	Obj.value = 'Clique para baixar arquivo!';
	Obj.onclick = function() { document.location.href = link;};
	//alert('Arquivo Concluído');
	document.location.href = link;
}

function fecha_notificacao( id_notificacao )
{
	DOM_execute_when_xmlhttpChange = function()
	{
		if( DOM_itensArray[0].firstChild.data == 0 )
		{
			alert( 'Erro de permissão. A notificação não foi deletada.' );
			document.getElementById('notificacao_' + id_notificacao).style.display='block';
		}
	}
	DOM_loadXMLDoc('deleta_notificacao.php?cod_not=' + id_notificacao);
	document.getElementById('notificacao_' + id_notificacao).style.display='none';
}