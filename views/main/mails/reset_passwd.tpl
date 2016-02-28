<table align="center" border="0" cellpadding="5" cellspacing="3" style="width:580px">
	<tbody>
	<tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="5" style="width:100%">
				<tbody>
				<tr>
					<td>
						<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
							<tbody>
							<tr>
								<td>&nbsp;</td>
								<td><a href="#">Skeleton</a></td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0" cellpadding="0" cellspacing="5" style="width:100%">
							<tbody>
							<tr>
								<td>Hello {$user->first_name} {$user->last_name} ({$user->email}),</td>
							</tr>
							<tr>
								<td>Reset your account password by clicking the following link: <br/></td>
							</tr>
							<tr>
								<td><a href="http://{$smarty.server.SERVER_NAME}/users/resetPasswd/{$user->token}/">http://{$smarty.server.SERVER_NAME}/users/resetPasswd/{$user->token}/</a></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
							</tbody>
						</table>
					</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr>
	</tbody>
</table>
