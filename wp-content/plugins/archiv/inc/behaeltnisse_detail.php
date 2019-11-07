<div class="wrap">
	<h1>
		Neuen Sitzplatz anlegen
	</h1>
	
	<form action="/wp-admin/admin-post.php">
		<input type="hidden" name="action" value="asbd_sitzplaetze__create">
		<table class="">
			<tbody>
				<tr>
					<th>Name</th>
					<td><input type="text" id="name" name="name"></td>
				</tr>
				<tr>
					<th>Fahrzeug</th>
					<td>
						<select id="fahrzeuge_id" name="fahrzeuge_id">
							<?php 
							
								global $wpdb;
								$fahrzeuge = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}asbd_fahrzeuge", OBJECT);
								
								foreach($fahrzeuge as $item){
							?>
								<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<button type="submit" class="button button-primary">Sitzplatz anlegen</button>
	</form>
</div>