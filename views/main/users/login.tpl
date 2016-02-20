{extends 'index.tpl'}
{block title}Users{/block}

{block content}
	<form method="post" action="/users/login/" class="pure-form pure-form-stacked"
		  style="max-width: 320px; margin:0 auto;">
		<fieldset>

			<div class="err">{$err}</div>
			<div class="msg">{$msg}</div>

			<div class="pure-control-group">
				<label for="username">Username:</label>
				<input type="text" name="data[username]" id="username" value="{$username}"
					   placeholder="Username or email">
				{if $err_msg.username}
					<div class="err_msg">{$err_msg.username}</div>{/if}
			</div>

			<div class="pure-control-group">
				<label for="passwd">Password:</label>
				<input type="password" id="password" name="data[password]" value="{$password}" placeholder="Password">
				{if $err_msg.password}
					<div class="err_msg">{$err_msg.passwd}</div>{/if}
			</div>
			<div class="pure-controls">
				<label for="autoLogin" class="pure-checkbox">
					<input id="autoLogin" name="data[autoLogin]" value="1"
						   type="checkbox"{if $autoLogin} checked="on" {/if}> Pastreaza-ma autentificat
				</label>
				<button type="submit" class="pure-button pure-button-primary"><i class="fa fa-sign-in"></i>
					Autentificare
				</button>
			</div>
			<div class="pure-controls" style="margin-top:5px;">
				<a href="/users/passwdLost/">Am uitat parola</a>
				<i class="fa fa-separator"></i>
				<a href="/users/register/">Inregistrare</a></div>
		</fieldset>
	</form>
{/block}