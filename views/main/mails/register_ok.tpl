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
								<td>Hello {$message.to_name} ({$message.to_email}),</td>
							</tr>
							<tr>
								<td>Welcome to SKELETON!<br />
									<br />
									Your account was successfully created, but is inactive!
									To activate it, click on the following link:</td>
							</tr>
							<tr>
								<td><a href="http://{$smarty.server.SERVER_NAME}/users/activate/{$message.token}/">http://{$smarty.server.SERVER_NAME}/users/activate/{$message.token}/</a></td>
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
