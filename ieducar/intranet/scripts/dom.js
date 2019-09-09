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
	Obj.onclick = function() { alert('O sistema est� gerando o arquivo. Aguarde!')};
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
	//alert('Arquivo Conclu�do');
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
		if (confirm("Passar cadastro N�"+iptu+" para 'Assiante'?"))
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
		alert('O n�mero n�o est� cadastrado na base de dados!');
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
	Obj.onclick = function() { alert('O sistema est?gerando o arquivo. Aguarde!')};
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
	//alert('Arquivo Conclu�do');
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
	Obj.onclick = function() { alert('O sistema est?gerando o arquivo. Aguarde!')};
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
	Obj.onclick = function() { alert('O sistema est?gerando o arquivo. Aguarde!')};
}

function getPDFouvidoriaAtendimentoDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	link = objXML.data;
	Obj = document.getElementById('imprimir');
	Obj.value = 'Clique para baixar arquivo!';
	Obj.onclick = function() { document.location.href = link;};
	//alert('Arquivo Conclu�do');
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
	Obj.onclick = function() { alert('O sistema est?gerando o arquivo. Aguarde!')};
}

function getPDFestoquesaidaDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	link = objXML.data;
	Obj = document.getElementById('imprimir');
	Obj.value = 'Clique para baixar arquivo!';
	Obj.onclick = function() { document.location.href = link;};
	//alert('Arquivo Conclu�do');
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
	Obj.onclick = function() { alert('O sistema est?gerando o arquivo. Aguarde!')};

}

function getPDFouvidoriaAtendimentoSetorDone()
{
	var valores = [];
	objXML = DOM_itensArray[0].firstChild;
	link = objXML.data;
	Obj = document.getElementById('imprimir');
	Obj.value = 'Clique para baixar arquivo!';
	Obj.onclick = function() { document.location.href = link;};
	//alert('Arquivo Conclu�do');
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
	//alert('Arquivo Conclu�do');
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
	Obj.onclick = function() { alert('O sistema est?gerando o arquivo. Aguarde!')};
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
	//alert('Arquivo Conclu?o');
	document.location.href = link;
}
