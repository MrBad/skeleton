{extends 'index.tpl'}
{block title}Users{/block}

{block content}
	<form method="post" action="/users/resetPasswd/{$user->token}/" class="pure-form pure-form-aligned">
	<fieldset>
	<legend>Reset Password</legend>

	<div class="err">{$err}</div>
	<div class="msg">{$msg}</div>

	<div class="pure-control-group">
		<label for="password">New password:</label>
		<input type="password" name="data[password]" id="password" value="{$password}">
		{if $err_msg.password}
			<div class="err_msg">{$err_msg.password}</div>
		{/if}
	</div>

	<div class="pure-control-group">
		<label for="re_password">Repeat password:</label>
		<input type="password" name="data[re_password]" id="re_password" value="{$re_password}">
		{if $err_msg.re_password}
			<div class="err_msg">{$err_msg.re_password}</div>
		{/if}
	</div>

	<div class="pure-controls">
		<button type="submit" class="pure-button pure-button-primary sbmt">
			<i class="fa fa-undo"></i> Reset Password
		</button>
	</div>
{/block}