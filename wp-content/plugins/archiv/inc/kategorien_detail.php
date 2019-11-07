<div class="wrap">
	<h1>
		Neue Person anlegen
	</h1>
	
	<form action="/wp-admin/admin-post.php">
		<input type="hidden" name="action" value="asbd_personen__create">
		<table class="">
			<tbody>
				<tr>
					<th>Foto</th>
					<td>
						<div class='image-preview-wrapper'>
							<img id='image-preview' src='' height='100'>
						</div>
						<input id="upload_image_button" type="button" class="button" value="Foto auswählen" />
						<input type='hidden' name='image_attachment_id' id='image_attachment_id'>
					</td>
				</tr>
				<tr>
					<th>Vorname</th>
					<td><input type="text" id="vorname" name="vorname"></td>
				</tr>
				<tr>
					<th>Nachname</th>
					<td><input type="text" id="nachname" name="nachname"></td>
				</tr>
				<tr>
					<th>Telefon</th>
					<td><input type="text" id="telefon" name="telefon"></td>
				</tr>
				<tr>
					<th>E-Mail</th>
					<td><input type="text" id="email" name="email"></td>
				</tr>
				<tr>
					<th>Passwort</th>
					<td><input type="password" id="passwort" name="passwort"></td>
				</tr>
				<tr>
					<th>E-Mail Benachrichtigung</th>
					<td><select id="email_benachrichtigung" name="email_benachrichtigung"><option value="1">Ja</option><option value="0">Nein</option></select></td>
				</tr>
				<tr>
					<th>Status Aktiv</th>
					<td><select id="aktiv" name="aktiv"><option value="1">Ja</option><option value="0">Nein</option></select></td>
				</tr>
				<tr>
					<th>Rolle</th>
					<td><select id="role" name="role"><option value="asbd_user">Eingeschränkter Benutzer</option><option value="asbd_admin">Administrator</option></select></td>
				</tr>
			</tbody>
		</table>
		<button type="submit" class="button button-primary">Person anlegen</button>
	</form>
</div>