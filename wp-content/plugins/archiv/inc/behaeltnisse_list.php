<div class="wrap">
	<h1>
		Behältnisse
		<a href="/wp-admin/admin.php?page=asbd_dienstplan_sitzplaetze&action=create" class="page-title-action">Erstellen</a>	
	</h1>
	
	<table class="widefat">
		<thead>
			<tr>
				<th>Archiv</th>
				<th>ID</th>
				<th>Name</th>
				<th>Inhalt</th>
				<th>Type</th>
				<th>Größe</th>
				<th>Lagerort</th>
				<th>Bearbeiten</th>
				<th>Löschen</th>
			</tr>
		</thead>
		<tbody>
			<?php
				global $wpdb;
				$table_prefix = $wpdb->prefix . 'archiv_';
				
				$behaeltnisse = $wpdb->get_results("
					SELECT 
						{$table_prefix}behaeltnisse.*, 
						{$table_prefix}nummernkreise.prefix AS nummernkreise_prefix 
					FROM 
						{$table_prefix}behaeltnisse 
					LEFT JOIN 
						{$table_prefix}nummernkreise 
						ON {$table_prefix}nummernkreise.id = {$table_prefix}behaeltnisse.nummernkreise_id
				", OBJECT);
			
				foreach($behaeltnisse as $item){
			?>
				<tr>
					<td>
						<div class="nummernkreise-prefix nummernkreise-prefix--<?php echo strtolower($item->nummernkreise_prefix); ?>">
							<?php echo $item->nummernkreise_prefix; ?>
						</div>
					</td>
					<td>BEH<?php echo str_pad($item->id, 3, '0', STR_PAD_LEFT); ?></td>
					<td><?php echo $item->name; ?></td>
					<td><?php echo $item->inhalt; ?></td>
					<td><?php echo $item->type; ?></td>
					<td><?php echo $item->groesse; ?></td>
					<td><?php echo $item->lagerort; ?></td>
					<td><a class="button button-primary" href="/wp-admin/admin.php?page=asbd_dienstplan_sitzplaetze&action=update&id=<?php echo $item->id; ?>">Bearbeiten</a></td>
					<td><a class="button button-primary" href="/wp-admin/admin-post.php?action=asbd_sitzplaetze__delete&id=<?php echo $item->id; ?>">Löschen</a></td>
				</tr>
			<?php
				}
			
			?>
		</tbody>
	</table>
</div>