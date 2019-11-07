<?php

	$von = isset($_GET['von']) ? $_GET['von'] : (date("Y") . '-01-01');
	$bis = isset($_GET['bis']) ? $_GET['bis'] : (date("Y-m-d", time() - 60 * 60 * 24));

?>

<div class="wrap">
	<h1>
		Statistik
	</h1>
	
	<br>
	<form action="/wp-admin/admin.php">
		<input type="hidden" name="page" value="asbd_dienstplan_statistik">
		<input type="date" name="von" value="<?php echo $von; ?>">
		<input type="date" name="bis" value="<?php echo $bis; ?>">
		<button type="submit" class="button button-primary">Filtern</button>
	</form>
	<br>
	
	<table class="widefat">
		<thead>
			<tr>
				<th>ID</th>
				<th></th>
				<th>Vorname</th>
				<th>Nachname</th>
				<th>Anzahl Tagdienste</th>
				<th>Anzahl Nachtdienste</th>
				<th class="widefat-bold">Summe Dienste</th>
				<th>Stunden Tagdienste</th>
				<th>Stunden Nachtdienste</th>
				<th class="widefat-bold">Summe Stunden</th>
				<th>Details</th>
			</tr>
		</thead>
		<tbody>
			<?php
				global $wpdb;
				$personen = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}asbd_personen ORDER BY nachname ASC", OBJECT);
			
				foreach($personen as $item){
					
					$dienste = $wpdb->get_results("SELECT {$wpdb->prefix}asbd_besetzungen.*, {$wpdb->prefix}asbd_sitzplaetze.name AS sitzplaetze_name FROM {$wpdb->prefix}asbd_besetzungen LEFT JOIN {$wpdb->prefix}asbd_sitzplaetze ON {$wpdb->prefix}asbd_sitzplaetze.id = {$wpdb->prefix}asbd_besetzungen.sitzplaetze_id WHERE personen_id={$item->id} AND datum >= '{$von}' AND datum <= '{$bis}'", OBJECT);
					
					$item->anzahl_tagdienste = 0;
					$item->anzahl_nachtdienste = 0;
					$item->stunden_tagdienste = 0.0;
					$item->stunden_nachtdienste = 0.0;
					
					foreach($dienste as $d){
						
						$beginn = new DateTime($d->beginn);
						$ende = new DateTime($d->ende);
						$nulltime = new DateTime('24:00:00');
						$nulltime2 = new DateTime('00:00:00');
						
						if ($beginn > $ende){
							$diff = $beginn->diff($nulltime);
							$hours = ($diff->days * 24) + $diff->h
          + ($diff->i / 60) + ($diff->s / 3600);
							
							$diff = $nulltime2->diff($ende);
							$hours += ($diff->days * 24) + $diff->h
          + ($diff->i / 60) + ($diff->s / 3600);
						}else{
							$diff = $beginn->diff($ende);
							$hours = ($diff->days * 24) + $diff->h
          + ($diff->i / 60) + ($diff->s / 3600);
						}
						
						if ($d->dienst == 'tagdienst'){
							$item->anzahl_tagdienste++;
							$item->stunden_tagdienste += $hours;
						}else{
							$item->anzahl_nachtdienste++;
							$item->stunden_nachtdienste += $hours;
						}
					}
					
			?>
				<tr>
					<td><?php echo $item->id; ?></td>
					<td><div class="person-bild" <?php if ($item->image_attachment_id != '0'){ echo 'style="background-image:url('.wp_get_attachment_url($item->image_attachment_id).');"';} ?>></div></td>
					<td><?php echo $item->vorname; ?></td>
					<td><?php echo $item->nachname; ?></td>
					<td><?php echo $item->anzahl_tagdienste; ?></td>
					<td><?php echo $item->anzahl_nachtdienste; ?></td>
					<td class="widefat-bold"><?php echo ($item->anzahl_tagdienste + $item->anzahl_nachtdienste); ?></td>
					<td><?php echo number_format($item->stunden_tagdienste, 2, ',', '.'); ?></td>
					<td><?php echo number_format($item->stunden_nachtdienste, 2, ',', '.'); ?></td>
					<td class="widefat-bold"><?php echo number_format(($item->stunden_tagdienste + $item->stunden_nachtdienste), 2, ',', '.'); ?></td>
					<td>
						<div class="remodal" data-remodal-id="popup-statistik-<?php echo $item->id; ?>">
							<h2><?php echo $item->vorname; ?> <?php echo $item->nachname; ?></h2>
							<button data-remodal-action="cancel" class="button button-primary">Schließen</button>
							
							<br><br>
							
							<table class="widefat">
								<thead>
									<tr>
										<th>Datum</th>
										<th>Dienst</th>
										<th>von</th>
										<th>bis</th>
										<th>Sitz</th>
									</tr>
								</thead>
								<tbody>
							
							<?php
								foreach($dienste as $d){
							?>
									
									<tr>
										<td><?php echo (new DateTime($d->datum))->format('d.m.Y'); ?></td>
										<td><?php echo ucfirst($d->dienst); ?></td>
										<td><?php echo (new DateTime($d->beginn))->format('H:i'); ?></td>
										<td><?php echo (new DateTime($d->ende))->format('H:i'); ?></td>
										<td><?php echo $d->sitzplaetze_name; ?></td>
									</tr>
									
							<?php
								}
							?>
								</tbody>
							</table>
						</div>
						<button class="button" data-remodal-target="popup-statistik-<?php echo $item->id; ?>">Details</button>
					</td>
				</tr>
			<?php
				}
			
			?>
		</tbody>
	</table>
</div>