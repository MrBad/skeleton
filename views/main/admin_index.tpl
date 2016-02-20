{*
	This is the /admin/ layout page.
	The result of controllers actions are injected into content smarty block
*}<!DOCTYPE html>
<html lang="en">
<head>
	<link href="/htdocs/css/pure.css" rel="stylesheet">
	<link href="/htdocs/css/font-awesome.css" rel="stylesheet">
	<link href="/htdocs/css/admin.css" rel="stylesheet">
	<script src="/htdocs/js/MooTools-Core-1.5.2.js"></script>
	<script src="/htdocs/js/MooTools-More-1.5.2.js"></script>
</head>
<body>

<div class="pure-g">
	<div class="pure-u-1-1">

		<div class="pure-menu pure-menu-horizontal contextmenu">
			<ul class="pure-menu-list">
				<li class="pure-menu-item"><a href="/" class="pure-menu-link">Home</a></li>
				<li class="pure-menu-item"><a href="/admin/" class="pure-menu-link">Admin</a></li>
			</ul>
		</div>

		{block content}
		<h1>Admin</h1>
		{/block}

	</div>
</div>
</body>
</html>