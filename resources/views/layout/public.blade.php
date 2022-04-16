<?php
	define('HOST', isset($_SERVER['HTTP_HOST']) === true ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_ADDR']) === true ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME']));
	$domain_parts = explode('.', HOST); 
	$icoTecsis = "favicon2.ico";

	// if($domain_parts[2] != "tec"){
	// 	$icoTecsis = "favicon.ico";
	// }
?>

<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8"/>
		<title>EducaSis</title>
		
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="icon" type="image/x-icon" href="{!! url($icoTecsis) !!}" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Love+Ya+Like+A+Sister&display=swap" rel="stylesheet">
		
		<link rel="stylesheet" type="text/css" href="{{ url('intranet/styles/login.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ url('intranet/styles/login-custom.css') }}">

		

		<!-- Google Tag Manager -->
		<script>
			dataLayer = [{
				'slug': '{{$config['app']['database']['dbname']}}',
				'user_id': 0
			}];
			
			(function (w, d, s, l, i) {
				w[l] = w[l] || [];
				w[l].push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
				var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
				j.async = true;
				j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
				f.parentNode.insertBefore(j, f);
			})(window, document, 'script', 'dataLayer', '{{ config('legacy.gtm') }}');
		</script>
		<!-- End Google Tag Manager -->
		
		@if($errors->count() && str_contains($errors->first(), 'errou a senha muitas vezes' ))
		<script>
			window.onload = function() {
				document.getElementById("form-login-submit").disabled = true;
				setTimeout(function () {
					document.getElementById("form-login-submit").disabled = false;
				}, 60000);
			}
		</script>
		@endif

	</head>
	
	<body>
		<div class="divBody">
			<!-- Google Tag Manager (noscript) -->
			<noscript>
				<iframe src="https://www.googletagmanager.com/ns.html?id={{ config('legacy.gtm') }}" height="0" width="0" style="display:none;visibility:hidden"></iframe>
			</noscript>
			<!-- End Google Tag Manager (noscript) -->
			<div id="main">
				
				<div class="lateralLogin">
					
					<?php //<h1>{{ config('legacy.config.ieducar_entity_name') }}</h1>  ?>
					<h1 class="fontLogin fontLoginMobile">Secretaria Municipal de Educação</h1>
					
					@if (session('status'))
					<p class="success">{{ session('status') }}</p>
					@endif
					
					@if($errors->count())
					<p class="error">{{ $errors->first() }}</p>
					@endif
					
					<div class="container"></div>
					<div id="login-form" class="shadow_M">
						
						<img alt="Logo" class="entLogoMobile" src="{{ config('legacy.config.ieducar_image') ?? url('https://tecsisdoc.com.br/wp-content/uploads/2022/02/cloud-logo.png') }}"/>
						@yield('content')
					</div>
					<div id="footer" class="link">
						<div class="divLogo" id="divLogoId"></div>
						<?php /*	<p>Mantido por <a href="https://tecsisdoc.com.br/" target="_blank">Tecsis</a>.
						{!! config('legacy.config.ieducar_login_footer') !!} </p> */?>
					</div>
				</div>
				<div class="imgBorda" id="imgBordaId"></div>
				<div class="container lateral_2">
					<div class="container">
						<img alt="Logo" class="entLogo" src="{{ config('legacy.config.ieducar_image') ?? url('https://tecsisdoc.com.br/wp-content/uploads/2022/01/tecsis_png.png') }}"/>
					</div>
					<div class="container">
						<h1 class="fontLogin font-b">Secretaria Municipal de Educação</h1>
					</div>
					
					<div id="footer" class="link">
						<div class="footer-social">
							<?php /* {!! config('legacy.config.ieducar_external_footer') !!} */ ?>
							@if(config('legacy.config.facebook_url') || config('legacy.config.linkedin_url') || config('legacy.config.twitter_url'))
							
							<div class="social-icons">
								<p class="fontLogin font-s"> Siga-nos nas redes sociais&nbsp;&nbsp;</p>
								@if(config('legacy.config.facebook_url'))
								<a target="_blank" href="{{ config('legacy.config.facebook_url')}}"><img src="{{ url('intranet/imagens/icon-social-facebook.png') }}"></a>
								@endif
								@if(config('legacy.config.linkedin_url'))
								<a target="_blank" href="{{ config('legacy.config.linkedin_url')}}"><img src="{{ url('intranet/imagens/icon-social-linkedin.png') }}"></a>
								@endif
								@if(config('legacy.config.twitter_url'))
								<a target="_blank" href="{{ config('legacy.config.twitter_url')}}"><img src="{{ url('intranet/imagens/icon-social-twitter.png') }}"></a>
								@endif
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="divLogoMobile" id="divLogoMobileId"></div>
		</div>
		<?php /* <div class="footer-socialMobile">
			{!! config('legacy.config.ieducar_external_footer') !!} 
			@if(config('legacy.config.facebook_url') || config('legacy.config.linkedin_url') || config('legacy.config.twitter_url'))
			
			<div class="social-icons">
			<p class="fontLogin font-s"> Siga-nos nas redes sociais&nbsp;&nbsp;</p>
			@if(config('legacy.config.facebook_url'))
			<a target="_blank" href="{{ config('legacy.config.facebook_url')}}"><img src="{{ url('intranet/imagens/icon-social-facebook.png') }}"></a>
			@endif
			@if(config('legacy.config.linkedin_url'))
			<a target="_blank" href="{{ config('legacy.config.linkedin_url')}}"><img src="{{ url('intranet/imagens/icon-social-linkedin.png') }}"></a>
			@endif
			@if(config('legacy.config.twitter_url'))
			<a target="_blank" href="{{ config('legacy.config.twitter_url')}}"><img src="{{ url('intranet/imagens/icon-social-twitter.png') }}"></a>
			@endif
			</div>
			@endif
		</div> */ ?>
	</body>

	<script>
        let tecsis = window.location.hostname;
		let tecsisSplit = tecsis.split(".");
        (function basetecsis(){
            if(tecsisSplit[2] == "tec"){
                document.getElementById('divLogoId').style.backgroundImage="url(../intranet/imagens/login/svg/Tecsis-animation_02.svg)"; 
                document.getElementById('divLogoMobileId').style.backgroundImage="url(../intranet/imagens/login/svg/Tecsis-bordaMobile.svg)"; 
                document.getElementById('imgBordaId').style.backgroundImage="url(../intranet/imagens/login/svg/Tecsis_borda.svg)";
            }
        })()
		console.log(tecsisSplit[2]);
        </script>
</html>
