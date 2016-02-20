{extends 'index.tpl'}

{block title}About {/block}
{block content}
	About skeleton - small PHP framework<br/>
	You can edit this page - <strong>View</strong> is in views/main/homepages/about.tpl<br/>
	<strong>Controller</strong> is in controllers/Homepages.php<br/>
	<strong>Model</strong> is in models/Homepage.php<br>
	<strong>Admin interface</strong> is in <a href="/admin/">admin</a>
	Edit <strong>config.ini</strong>, [local] section to suit your needs - db and paths<br/>
	Run <strong>sh create_tmp.sh</strong> to create tmp (temporary directory) structure<br/>
	Create for example <strong>users</strong> table and cd to bin/console.<br/>
	run <strong>php generate.php users</strong> to generate controller/model/views.<br/>
{/block}