{extends 'admin_index.tpl'}
{block title}Users{/block}

{block content}
<h1>Users</h1>

<div class="pure-menu pure-menu-horizontal contextmenu">
	<ul class="pure-menu-list">
		<li class="pure-menu-item"><a href="/admin/users/" class="pure-menu-link">All</a></li>
		<li class="pure-menu-item"><a href="/admin/users/add/" class="pure-menu-link">Add</a></li>
		<li class="pure-menu-item"><a href="/admin/users/edit/{$user->id}/" class="pure-menu-link">Edit</a></li>
		<li class="pure-menu-item"><a href="/admin/users/delete/{$user->id}/" class="pure-menu-link">Delete</a></li>
	</ul>
</div>

<table width="100%" class="pure-table pure-table-bordered">
<tbody>
	<tr>
		<td>Id</td>
		<td>{$user->id}</td>
	</tr>
	<tr>
		<td>Username</td>
		<td>{$user->username}</td>
	</tr>
	<tr>
		<td>Email</td>
		<td>{$user->email}</td>
	</tr>
	<tr>
		<td>Password</td>
		<td>{$user->password}</td>
	</tr>
	<tr>
		<td>First name</td>
		<td>{$user->first_name}</td>
	</tr>
	<tr>
		<td>Last name</td>
		<td>{$user->last_name}</td>
	</tr>
</tbody>
</table>
{/block}
