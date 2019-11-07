<div class="wrap">
	<h1>
		Personen
		<a href="/wp-admin/admin.php?page=asbd_dienstplan_personen&action=create" class="page-title-action">Erstellen</a>	
	</h1>
	
	<table class="widefat">
		<thead>
			<tr>
				<th>ID</th>
				<th>Vorname</th>
				<th>Nachname</th>
				<th>Telefon</th>
				<th>E-Mail</th>
				<th>E-Mail Benachrichtigung</th>
				<th>Aktiv</th>
				<th>Bearbeiten</th>
				<th>Löschen</th>
			</tr>
		</thead>
		<tbody>
			<?php
				global $wpdb;
				$personen = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}asbd_personen ORDER BY nachname ASC", OBJECT);
			
				foreach($personen as $item){
			?>
				<tr>
					<td><?php echo $item->id; ?></td>
					<td><?php echo $item->vorname; ?></td>
					<td><?php echo $item->nachname; ?></td>
					<td><?php echo $item->telefon; ?></td>
					<td><?php echo $item->email; ?></td>
					<td><?php echo ($item->email_benachrichtigung == 1) ? "Ja" : "Nein"; ?></td>
					<td><?php echo ($item->aktiv == 1) ? "Ja" : "Nein"; ; ?></td>
					<td><a class="button button-primary" href="/wp-admin/admin.php?page=asbd_dienstplan_personen&action=update&id=<?php echo $item->id; ?>">Bearbeiten</a></td>
					<td><a class="button button-primary" href="/wp-admin/admin-post.php?action=asbd_personen__delete&id=<?php echo $item->id; ?>">Löschen</a></td>
				</tr>
			<?php
				}
			
			?>
		</tbody>
	</table>
</div>