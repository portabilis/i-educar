function subgoogle()
{				
	formulario = document.getElementById("formg");				
	caminho = "q="+encodeURI(formulario.w.value) + " site:itajai.sc.gov.br"
	formulario.w.value = "";				
	caminho = "http://www.google.com/search?hl=en&"+caminho;
	janelaa = window.open(caminho, "janelaa", "");
	janelaa.focus();
}

function tamanhoTela()
{
	if( typeof( window.innerWidth ) == 'number' )
	{
	    winW = window.innerWidth;
	    winH = window.innerHeight;
	}
	else if( document.documentElement &&
	      ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )
	{
	 /* IE 6+ in 'standards compliant mode' */
	    winW = document.documentElement.clientWidth;
	    winH = document.documentElement.clientHeight;
	}
	else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) )
	{
	 /*IE 4 compatible*/
	    winW = document.body.clientWidth;
	    winH = document.body.clientHeight;
	}
	
	if (winW < 955 )
	{
		document.getElementById('padraoPMI').style['visibility']='hidden';
		document.getElementById('padraoPMI').style.display='none';
		document.getElementById('padraoPMI').style['width']='0px';
		document.getElementById('padraoPMI').style['height']='0px';
		
		
		document.getElementById('tbPrincipalP').style['width']='0';	
		document.getElementById('tbPrincipalP').style['height']='0';
		document.getElementById('r1c1').style['height']='160';
		document.getElementById('r1c2').style['height']='160';
		document.getElementById('tbPrincipal').style['width']='756';
		
	}
}

function FiltraCampo(codigo) {
    var s = "";
	
	tam = codigo.length;
	for (i = 0; i < tam ; i++) {  
		if (codigo.substring(i,i + 1) == "0" || 
           	codigo.substring(i,i + 1) == "1" ||
            codigo.substring(i,i + 1) == "2" ||
            codigo.substring(i,i + 1) == "3" ||
            codigo.substring(i,i + 1) == "4" ||
            codigo.substring(i,i + 1) == "5" ||
            codigo.substring(i,i + 1) == "6" ||
            codigo.substring(i,i + 1) == "7" ||
            codigo.substring(i,i + 1) == "8" ||
            codigo.substring(i,i + 1) == "9"  )
		 		s = s + codigo.substring(i,i + 1);
	}
	return s;
}

function DvCnpjOk(e) {
    var dv = false;
    controle = "";
    s = FiltraCampo(e.value);
    tam = s.length
    if ( tam  == 14 ) {
        dv_cnpj = s.substring(tam-2,tam);
        for ( i = 0; i < 2; i++ ) {
            soma = 0;
            for ( j = 0; j < 12; j++ ) 
                soma += s.substring(j,j+1)*((11+i-j)%8+2);
            if ( i == 1 ) soma += digito * 2;
            digito = 11 - soma  % 11;
            if ( digito > 9 ) digito = 0;
            controle += digito;
        }
        if ( controle == dv_cnpj )
            dv = true;
     }
     if ( ! dv && tam > 0) {
         mensagem = "           Erro de digitação:\n";
         mensagem+= "          ===============\n\n";
         mensagem+= " O CNPJ: " + e.value + " não existe!!\n";
         alert(mensagem);
     }
     return dv;
}

function DvCpfOk(e) {
    var dv = false;

    controle = "";
    s = FiltraCampo(e.value);
    tam = s.length;
    if ( tam == 11 ) {
        dv_cpf = s.substring(tam-2,tam);
        for ( i = 0; i < 2; i++ ) {
            soma = 0;
            for ( j = 0; j < 9; j++ )
                soma += s.substring(j,j+1)*(10+i-j);
            if ( i == 1 ) soma += digito * 2;
            digito = (soma * 10) % 11;
            if ( digito == 10 ) digito = 0;
            controle += digito;
        }
        if ( controle == dv_cpf )
            dv = true;
    }
     if ( ! dv && tam > 0) {
         mensagem = "           Erro de digitação:\n";
         mensagem+= "          ===============\n\n";
         mensagem+= " O CPF: " + e.value + " não existe!!\n";
         alert(mensagem);
         e.value = "";
     }
    return dv;
}

function addSel( campo, valor, texto )
{
	obj = document.getElementById( campo );
	novoIndice = obj.options.length;
	obj.options[novoIndice] = new Option( texto );
	opcao = obj.options[novoIndice];
	opcao.value = valor;
	opcao.selected = true;
	setTimeout( "obj.onchange", 100 );
}

function addVal( campo, valor )
{
	obj = document.getElementById( campo );
	obj.value = valor;
}

function openPage(url_pagina, nome_pagina, largura, altura, scroll, top, left)
{
	janela = window.open(url_pagina,  nome_pagina, largura, altura, top, left, statusbar=scroll);
	janela.focus();
}
		
function verificaTamanhoEmail(campo, e)
{
	if( typeof window.event != "undefined" )
	{
		if(window.event.keyCode != 13 && window.event.keyCode != 8 && window.event.keyCode != 32)
		{
			if(document.getElementById(campo).value.length>16)
			{
				alert("Excedido nъmero maximo de caracteres, por favor use no mбximo e 16 caracteres!");
			}
		}
		
	}
	else
	{
		if(e.which != 13 && e.which != 8 && e.which != 32)
		{
			if(document.getElementById(campo).value.length>16)
			{
				alert("Excedido nъmero maximo de caracteres, por favor use no mбximo e 16 caracteres!");
			}
		}
	}
}

function trocaHora() 	{ 		

	tempo++;
	dias = Math.floor(tempo / 86400);
	var temp_tempo;
	temp_tempo = tempo - dias*86400;
	horas = Math.floor( temp_tempo / 3600);
	temp_tempo = temp_tempo - horas*3600;
	min = Math.floor(temp_tempo / 60);
	temp_tempo = temp_tempo - min*60;
	seg = temp_tempo;
	var data = "";
	if(dias)
	{
		data = dias+" dias  "; 
	}
	if(horas)
	{
		if(horas <10)
		{
			horas = "0"+horas;
		}
		data = data+horas;
	}else
	{
		data = data+"00";
	}
	if(min)
	{
		if(min <10)
		{
			min = "0"+min;
		}
		data = data+":"+min;
	}else
	{
		data = data+":00";
	}
	if(seg)
	{
		if(seg < 10)
		{
			seg = "0"+seg;
		}
		data = data+":"+seg;
	}else
	{
		data  = data+":00"
	}
	document.getElementById( 'tempo' ).innerHTML = data;
}

function move_pessoa_reuniao(idpes,acao,reuniao,grupo,div)
{
	DOM_execute_when_xmlhttpChange = function() {};
	DOM_loadXMLDoc( 'xml_reuniao_pessoa.php?pessoa='+idpes+'&acao='+acao+"&cod_reuniao="+reuniao+"&cod_grupo="+grupo);
	if(acao ==1)
	{
		document.getElementById(div).innerHTML = "<a href='#' onclick='move_pessoa_reuniao("+idpes+",2,"+reuniao+","+grupo+","+div+")'><img src='imagens/nvp_bot_sai_reuniao.gif' border='0'></a>";
	}else
	{
		document.getElementById(div).innerHTML = "<a href='#' onclick='move_pessoa_reuniao("+idpes+",1,"+reuniao+","+grupo+","+div+")'><img src='imagens/nvp_bot_entra_reuniao.gif' border='0'></a>";
	}
	
}

function marcar_todos()
{
		document.getElementById("desmarcar").checked = false;
		if(document.getElementById("marcar").checked )
		{
			for(i=0; i<marcar.length;i++)
			{
				document.getElementById("top_"+marcar[i]).checked = true;
			}
		}	
}

function desmarcar_todos()
{

		document.getElementById("marcar").checked = false;
		if( document.getElementById("desmarcar").checked )
		{
			for(i=0; i<marcar.length;i++)
			{
				document.getElementById("top_"+marcar[i]).checked = false;
			}
		}
}		

function desmarcar_marcar(id)
{
	if(!document.getElementById(id).checked )
	{	
		document.getElementById("marcar").checked = false;
	}else
	{
		document.getElementById("desmarcar").checked = false;
	}
}

function setValores(rootDocument, desabilita, cep, idlog, nm_log, idtlog, nm_mun, sigla_uf, idbai, nm_bai, numero, letra, complemento, bloco, andar, apartamento)
{
	rootDocument.getElementById('cep_').value = cep.substr(0,5)+'-'+cep.substr(5);
	rootDocument.getElementById('cep').value = cep.substr(0,5)+'-'+cep.substr(5);
	rootDocument.getElementById('cep_').disabled = desabilita;
	
	//campo oculto
	rootDocument.getElementById('idlog').value = idlog;
	
	rootDocument.getElementById('logradouro').value = nm_log;
	rootDocument.getElementById('logradouro').disabled = desabilita;
	
	//campo oculto
	rootDocument.getElementById('idtlog').value = idtlog;
	
	rootDocument.getElementById('cidade').value = nm_mun;
	rootDocument.getElementById('cidade').disabled = desabilita;
	
	rootDocument.getElementById('sigla_uf').value = sigla_uf;
	rootDocument.getElementById('sigla_uf').disabled = desabilita;
	
	//campo oculto
	rootDocument.getElementById('idbai').value = idbai;
	rootDocument.getElementById('bairro').value = nm_bai;
	rootDocument.getElementById('bairro').disabled = desabilita;
	
	rootDocument.getElementById('numero').value = numero;
	
	if(rootDocument.getElementById('letra'))
	{
		rootDocument.getElementById('letra').value = letra;
	}
	
	if(rootDocument.getElementById('complemento'))
	{
		rootDocument.getElementById('complemento').value = complemento;
	}

	if(rootDocument.getElementById('bloco'))
	{
		rootDocument.getElementById('bloco').value = bloco;
	}
	
	if(rootDocument.getElementById('andar'))
	{
		rootDocument.getElementById('andar').value = andar;
	}
	if(rootDocument.getElementById('apartamento'))
	{
		rootDocument.getElementById('apartamento').value = apartamento;
	}
}

function definirOrdenacao(campo_ordenacao)
{
	controle = document.getElementById('ordenacao');
	if(controle.value != campo_ordenacao+" ASC")
	{
		setas = document.getElementById('fonte');
		setas.value = 'imagens/nvp_setinha_up.gif';
		controle.value = campo_ordenacao+" ASC";
	}
	else
	{
		setas = document.getElementById('fonte');
		setas.value = 'imagens/nvp_setinha_down.gif';
		controle.value = campo_ordenacao+" DESC";
	}
}

