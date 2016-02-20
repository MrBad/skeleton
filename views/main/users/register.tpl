{extends 'index.tpl'}
{block title}Users{/block}

{block content}
<h1>Users</h1>

<form method="post" action="/users/register/" class="pure-form pure-form-aligned">
	<fieldset>
		<legend>Register</legend>

		<div class="err">{$err}</div>
		<div class="msg">{$msg}</div>

		<div class="pure-control-group">
			<label for="username">Username:</label>
			<input type="text" name="data[username]" id="username" value="{$username}">
			{if $err_msg.username}<div class="err_msg">{$err_msg.username}</div>{/if}
		</div>

		<div class="pure-control-group">
			<label for="email">Email:</label>
			<input type="text" name="data[email]" id="email" value="{$email}">
			{if $err_msg.email}<div class="err_msg">{$err_msg.email}</div>{/if}
		</div>

		<div class="pure-control-group">
			<label for="password">Password:</label>
			<input type="text" name="data[password]" id="password" value="{$password}">
			{if $err_msg.password}<div class="err_msg">{$err_msg.password}</div>{/if}
		</div>

		<div class="pure-control-group">
			<label for="re_password">Repeat Password:</label>
			<input type="text" name="data[re_password]" id="re_password" value="{$re_password}">
			{if $err_msg.re_password}<div class="err_msg">{$err_msg.re_password}</div>{/if}
		</div>

		<div class="pure-control-group">
			<label for="first_name">First name:</label>
			<input type="text" name="data[first_name]" id="first_name" value="{$first_name}">
			{if $err_msg.first_name}<div class="err_msg">{$err_msg.first_name}</div>{/if}
		</div>

		<div class="pure-control-group">
			<label for="last_name">Last name:</label>
			<input type="text" name="data[last_name]" id="last_name" value="{$last_name}">
			{if $err_msg.last_name}<div class="err_msg">{$err_msg.last_name}</div>{/if}
		</div>

		<div class="pure-controls">
			<button type="submit" class="pure-button pure-button-primary sbmt"><i class="fa fa-user-plus"></i> Register</button>
		</div>
	</fieldset>
</form>
{/block}
