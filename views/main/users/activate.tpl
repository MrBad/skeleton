{extends 'index.tpl'}
{block title}Users{/block}

{block content}
{if $user}
	<div>
		<h2 style="margin-left:0px">Congratulation {$user->first_name} {$user->last_name}</h2>

		Your email address is confirmed
		<br/>
		{if $user->status=='active'}
			<b>Your account is now active</b>
			.
			<br/>
		{/if}
		<a href="/">Click here to continue</a>
		<br/>
	</div>
{/if}
{/block}