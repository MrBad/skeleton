{extends 'index.tpl'}
{block title}Users{/block}

{block content}
	<form method="post" action="/users/passwdLost/" class="pure-form pure-form-stacked"
		  style="max-width: 320px; margin:0 auto;">
		<fieldset>

			<div class="err">{$err}</div>
			<div class="msg">{$msg}</div>

			<div class="pure-control-group">
				<label for="email">{#email_addr#}:</label>
				<input type="text" name="data[email]" id="email" value="{$email}" placeholder="{#email_addr#}">
				{if $err_msg.email}
					<div class="err_msg">{$err_msg.email}</div>
				{/if}
			</div>

			<div class="pure-controls">
				<button type="submit" class="pure-button pure-button-primary"><i class="fa fa-undo"></i>
					{#reset_password#}
				</button>
			</div>
			<div class="pure-controls" style="margin-top:5px;">
				<a href="/users/passwdLost/">{#passwd_lost#}</a>
				<i class="fa fa-separator"></i>
				<a href="/users/register/">{#register#}</a></div>
		</fieldset>
	</form>
{/block}