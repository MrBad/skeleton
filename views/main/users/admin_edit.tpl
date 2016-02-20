{extends 'admin_index.tpl'}
{block title}Users{/block}

{block content}
<h1>Users</h1>

<div class="pure-menu pure-menu-horizontal contextmenu">
	<ul class="pure-menu-list">
		<li class="pure-menu-item"><a href="/admin/users/" class="pure-menu-link">All</a></li>
		<li class="pure-menu-item"><a href="/admin/users/edit/{$user->id}/" class="pure-menu-link">Edit</a></li>
		<li class="pure-menu-item"><a href="/admin/users/delete/{$user->id}/" class="pure-menu-link">Delete</a></li>
	</ul>
</div>

<form method="post" action="/admin/users/edit/{$user->id}/" class="pure-form pure-form-aligned">
	<fieldset>
		<legend>Edit User</legend>

		<div class="pure-controls err">{$err}</div>
		<div class="pure-controls msg">{$msg}</div>

		<div class="pure-control-group">
			<label for="username">Username:</label>
			<input type="text" name="data[username]" id="username" value="{$user->username}">
			{if $err_msg.username}<div class="err_msg">{$err_msg.username}</div>{/if}
		</div>

		<div class="pure-control-group">
			<label for="email">Email:</label>
			<input type="text" name="data[email]" id="email" value="{$user->email}">
			{if $err_msg.email}<div class="err_msg">{$err_msg.email}</div>{/if}
		</div>

		<div class="pure-control-group">
			<label for="password">Password:</label>
			<input type="text" name="data[password]" id="password" value="{$user->password}">
			{if $err_msg.password}<div class="err_msg">{$err_msg.password}</div>{/if}
		</div>

		<div class="pure-control-group">
			<label for="first_name">First name:</label>
			<input type="text" name="data[first_name]" id="first_name" value="{$user->first_name}">
			{if $err_msg.first_name}<div class="err_msg">{$err_msg.first_name}</div>{/if}
		</div>

		<div class="pure-control-group">
			<label for="last_name">Last name:</label>
			<input type="text" name="data[last_name]" id="last_name" value="{$user->last_name}">
			{if $err_msg.last_name}<div class="err_msg">{$err_msg.last_name}</div>{/if}
		</div>

		<div class="pure-controls">
		<input type="hidden" name="data[id]" value="{$user->id}">
			<input type="submit" value="Save User" class="pure-button pure-button-primary sbmt">
		</div>
	</fieldset>
</form>
{/block}
