<div class="wrap">
	<h1>
		Nummernkreise
	</h1>
	
	<table class="widefat">
		<thead>
			<tr>
				<th>ID</th>
				<th>Prefix</th>
				<th>Name</th>
			</tr>
		</thead>
		<tbody>
			<?php
				global $wpdb;
				$nummernkreise = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}archiv_nummernkreise ORDER BY id ASC", OBJECT);
			
				foreach($nummernkreise as $item){
			?>
			
				<tr>
					<td><?php echo $item->id; ?></td>
					<td><?php echo $item->prefix; ?></td>
					<td><?php echo $item->name; ?></td>
				</tr>

			<?php
				}
			?>
		</tbody>
	</table>
</div>