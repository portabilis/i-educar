<!doctype HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="pt">
	<head>

	   	<title> <!-- #&TITULO&# --> </title>
		<link rel=stylesheet type='text/css' href='styles/styles.css' />
		<link rel=stylesheet type='text/css' href='styles/novo.css' />
		<link rel=stylesheet type='text/css' href='styles/menu.css' />
		<!-- #&ESTILO&# -->

		<script type='text/javascript' src='scripts/padrao.js?1'></script>
		<script type='text/javascript' src='scripts/novo.js?1'></script>
		<script type='text/javascript' src='scripts/dom.js?1'></script>
		<script type='text/javascript' src='scripts/menu.js?1'></script>
		<script src="scripts/scriptaculous/prototype.js" type="text/javascript"></script>
		<script src="scripts/scriptaculous/scriptaculous.js?load=effects" type="text/javascript"></script>
		<script type="text/javascript">
			var running = false;
			var altura = null;
			function changeImage(div_id)
			{
				var id = /[0-9]+/.exec(div_id.element.id);
				var imagem = $('seta_'+id);
				var src = imagem.src.indexOf('arrow-up') ;
				imagem.src = (src!= -1) ? 'imagens/arrow-down2.gif' : 'imagens/arrow-up2.gif' ;
				imagem.title = (src!= -1) ? imagem.title.replace('Abrir','Fechar') : imagem.title.replace('Fechar','Abrir');
				if(src != -1)
					setCookie('menu_'+id, 'I', 30);
				else
					setCookie('menu_'+id, 'V', 30);

				running = false;
				$('tablenum1').style.height = $('tablenum1').offsetHeight - altura;
			}

			function teste(div_id)
			{
				altura = div_id.element.offsetHeight;
			}

			function toggleMenu(div_id)
			{
				if(running)
					return;

				var src = $('link1_'+div_id).title.indexOf('Abrir') ;
				 $('link1_'+div_id).title = (src!= -1) ? $('link1_'+div_id).title.replace('Abrir','Fechar') : $('link1_'+div_id).title.replace('Fechar','Abrir');
				 $('link2_'+div_id).title = (src!= -1) ? $('link2_'+div_id).title.replace('Abrir','Fechar') : $('link2_'+div_id).title.replace('Fechar','Abrir');

				running = true;

				new Effect.toggle($('div_'+div_id),'slide',{afterFinish:changeImage,duration:0.3,beforeStart:teste});


			}
		</script>
		<!-- #&SCRIPT&# -->

		<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />
		<!-- #&REFRESH&# -->

		<meta name='Author' content='Prefeitura de Itajaí' />
		<meta name='Description' content='Portal da Prefeitura de Itajaí' />
		<meta name='Keywords' content='portal, prefeitura, itajaí, serviço, cidadão' />

		<link rel="icon" href="imagens/logo_itajai.ico" type="image/x-icon">

		<script type="text/javascript">
		<!-- #&SCRIPT_HEADER&# -->
		</script>
	</head>
	<body <!-- #&BODYSCRIPTS&# -->>

	<div id="DOM_expansivel" class="DOM_expansivel"></div>
<!doctype HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="pt">
	<head>

	   	<title> <!-- #&TITULO&# --> </title>
		<link rel=stylesheet type='text/css' href='styles/styles.css' />
		<link rel=stylesheet type='text/css' href='styles/novo.css' />
		<link rel=stylesheet type='text/css' href='styles/menu.css' />
		<!-- #&ESTILO&# -->

		<script type='text/javascript' src='scripts/padrao.js?1'></script>
		<script type='text/javascript' src='scripts/novo.js?1'></script>
		<script type='text/javascript' src='scripts/dom.js?1'></script>
		<script type='text/javascript' src='scripts/menu.js?1'></script>

		<!-- #&SCRIPT&# -->

		<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />
		<!-- #&REFRESH&# -->

		<meta name='Author' content='Prefeitura de Itajaí' />
		<meta name='Description' content='Portal da Prefeitura de Itajaí' />
		<meta name='Keywords' content='portal, prefeitura, itajaí, serviço, cidadão' />

		<link rel="icon" href="imagens/logo_itajai.ico" type="image/x-icon">

		<script type="text/javascript">
		<!-- #&SCRIPT_HEADER&# -->
		</script>
	</head>
	<body <!-- #&BODYSCRIPTS&# -->>

	<div id="DOM_expansivel" class="DOM_expansivel"></div>
