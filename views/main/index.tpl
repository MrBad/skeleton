<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="skeleton">
	<meta name="description" content="{block description}skeleton{/block}">
	<title>{block title}skeleton{/block}</title>
	<link rel="icon" href="data:;base64,iVBORw0KGgo=">

	<link href="//fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet" type="text/css">
	<link href="/htdocs/css/pure.css" rel="stylesheet">
	<!--[if lte IE 8]>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css">
	<![endif]-->
	<!--[if gt IE 8]><!-->
	<link rel="stylesheet" href="/htdocs/css/grids-responsive.css">
	<!--<![endif]-->

	<link href="/htdocs/css/new_style.css" rel="stylesheet">
	<link rel="stylesheet" href="/htdocs/css/font-awesome.css">
</head>

<body>
<div id="page_container">
	<div id="header">
		<div class="logo">
			<h2><a href="/">skeleton</a></h2>
		</div>
		<a class="" id="toggle_menu">
			<i class="fa fa-bars"></i></a>
	</div>
	<div id="main_container">
		<div id="main_menu">
			<ul class="menu">
				<li>
					<a href="/" {if $controller=='loads'} class="active"{/if}><i class="fa fa-cubes"></i> Prima
						pagina</a>
				</li>
				<li>
					<a href="/homepages/about/"><i class="fa fa-truck"></i> About</a>
				</li>
			</ul>
			<div class="clearboth"></div>
		</div>

		<div class="breadcrumbs" style="">
			<div class="inner">
				<ul class="cf">
					<li><a href="/"> Prima pagina</a></li>
				</ul>
			</div>
		</div>
		<div id="middle_column">
			<div>
				<div id="content">
					{block content}{/block}
				</div>
			</div>
		</div>

	</div>
	<div id="footer">
		<a href="#">Termeni si conditii</a> <i class="fa fa-separator"></i>
		<a href="#">Confidentialitate</a> <i class="fa fa-separator"></i>
		<a href="#">Contact</a>
		<br/>
		© 2016, skeleton, {$generated} s - {$mem}KB
	</div>
</div>

{literal}
<script type="text/javascript" src="/htdocs/js/jsl.js"></script>
<script type="text/javascript">
	var controller = '{$controller}';
	var action = '{$action}';
	var lang = '{$lang}';

	jsl.load('/js/MooTools-Core-1.5.2.js', function () {
		jsl.load('/js/MooTools-More-1.5.2.js', function () {
			jsl.load('/js/languages/{/literal}{$lang}{literal}.js', function () {
				Locale.use(lang);
				jsl.load('/js/app.js?v=1', function () {
				});
			})
		});
	});
</script>
{/literal}

</body>
</html>