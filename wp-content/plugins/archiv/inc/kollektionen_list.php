<div class="wrap">
	<h1>
		Fahrzeuge
		<a href="/wp-admin/admin.php?page=asbd_dienstplan_fahrzeuge&action=create" class="page-title-action">Erstellen</a>	
	</h1>
	
	<table class="widefat">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Standard Tagdienst Beginn</th>
				<th>Standard Tagdienst Ende</th>
				<th>Standard Nachtdienst Beginn</th>
				<th>Standard Nachtdienst Ende</th>
				<th>Kennzeichen</th>
				<th>Notiz</th>
				<th>Bearbeiten</th>
				<th>Löschen</th>
			</tr>
		</thead>
		<tbody>
			<?php
				global $wpdb;
				$fahrzeuge = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}asbd_fahrzeuge", OBJECT);
			
				foreach($fahrzeuge as $item){
			?>
				<tr>
					<td><?php echo $item->id; ?></td>
					<td><?php echo $item->name; ?></td>
					<td><?php echo $item->default_tagdienst_beginn; ?></td>
					<td><?php echo $item->default_tagdienst_ende; ?></td>
					<td><?php echo $item->default_nachtdienst_beginn; ?></td>
					<td><?php echo $item->default_nachtdienst_ende; ?></td>
					<td><?php echo $item->kennzeichen; ?></td>
					<td><?php echo $item->notiz; ?></td>
					<td><a class="button button-primary" href="/wp-admin/admin.php?page=asbd_dienstplan_fahrzeuge&action=update&id=<?php echo $item->id; ?>">Bearbeiten</a></td>
					<td><a class="button button-primary" href="/wp-admin/admin-post.php?action=asbd_fahrzeuge__delete&id=<?php echo $item->id; ?>">Löschen</a></td>
				</tr>
			<?php
				}
			
			?>
		</tbody>
	</table>
</div>