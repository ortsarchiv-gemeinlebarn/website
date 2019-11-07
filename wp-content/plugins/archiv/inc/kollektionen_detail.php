<div class="wrap">
	<h1>
		Neues Fahrzeug anlegen
	</h1>
	
	<form action="/wp-admin/admin-post.php">
		<input type="hidden" name="action" value="asbd_fahrzeuge__create">
		<table class="">
			<tbody>
				<tr>
					<th>Name</th>
					<td><input type="text" id="name" name="name"></td>
				</tr>
				<tr>
					<th>Standard Tagdienst Beginn</th>
					<td><input type="time" id="default_tagdienst_beginn" name="default_tagdienst_beginn"></td>
				</tr>
				<tr>
					<th>Standard Tagdienst Ende</th>
					<td><input type="time" id="default_tagdienst_ende" name="default_tagdienst_ende"></td>
				</tr>
				<tr>
					<th>Standard Nachtdienst Beginn</th>
					<td><input type="time" id="default_nachtdienst_beginn" name="default_nachtdienst_beginn"></td>
				</tr>
				<tr>
					<th>Standard Nachtdienst Ende</th>
					<td><input type="time" id="default_nachtdienst_ende" name="default_nachtdienst_ende"></td>
				</tr>
				<tr>
					<th>Kennzeichen</th>
					<td><input type="text" id="kennzeichen" name="kennzeichen"></td>
				</tr>
				<tr>
					<th>Notiz</th>
					<td><input type="text" id="notiz" name="notiz"></td>
				</tr>
			</tbody>
		</table>
		<button type="submit" class="button button-primary">Fahrzeug anlegen</button>
	</form>
</div>