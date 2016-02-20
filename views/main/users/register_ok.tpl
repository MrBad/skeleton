{extends 'index.tpl'}
{block title}Users{/block}

{block content}
	<h2>Account activation</h2>
	<div class="hmessage">
		<div>
			Dear <b>{$user->first_name} {$user->last_name}</b>,<br/>
			Your account was successfully created, but is <b>inactive</b>.<br/>
			To activate it, <b>read your email</b> (<b>{$user->email}</b>) <br/>
			and follow the link into it.
		</div>
	</div>
{/block}