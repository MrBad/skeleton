{extends 'admin_index.tpl'}
{block title}Users{/block}

{block content}
<h1>Users</h1>

<div class="pure-menu pure-menu-horizontal contextmenu">
	<ul class="pure-menu-list">
		<li class="pure-menu-item"><a href="/admin/users/add/" class="pure-menu-link">Add</a></li>
	</ul>
</div>

{$pages}
<table width="100%" class="pure-table pure-table-bordered">
	<thead><tr>
		<th>Id</th>
		<th>Username</th>
		<th>Email</th>
		<th>Password</th>
		<th>First name</th>
		<th>Last name</th>
		<th colspan="3">Actions</th>
	</tr></thead>
	<tbody>
	{section name=u loop=$users}
	<tr>
		<td>{$users[u]->id}</td>
		<td>{$users[u]->username}</td>
		<td>{$users[u]->email}</td>
		<td>{$users[u]->password}</td>
		<td>{$users[u]->first_name}</td>
		<td>{$users[u]->last_name}</td>
		<td><a href="/admin/users/view/{$users[u]->id}/">View</a></td>
		<td><a href="/admin/users/edit/{$users[u]->id}/">Edit</a></td>
		<td><a href="/admin/users/delete/{$users[u]->id}/">Delete</a></td>
	</tr>
	{/section}
</tbody>
</table>
{$pages}
{/block}
