<?php

    $app->get('/v1/stats', function ($request, $response, $args) {

        $data = new stdClass();

        $sth = $this->db->prepare(
            "SELECT 
                COUNT(*) AS eintraege_anzahl_gesamt,
                (SELECT COUNT(*) FROM eintraege WHERE eintraege.bestaende_id=1) AS eintraege_anzahl_goa,
                (SELECT COUNT(*) FROM eintraege WHERE eintraege.bestaende_id=2) AS eintraege_anzahl_gfa,
                (SELECT COUNT(*) FROM eintraege WHERE eintraege.bestaende_id=3) AS eintraege_anzahl_gsa
            FROM 
                eintraege
           ");
        $sth->execute();
        $row_all = $sth->fetch();

        $data->meta = new stdClass();
        $data->meta->jahre_spanne = new stdClass();
        $data->meta->jahre_spanne->min = 3000;
        $data->meta->jahre_spanne->max = 0;

        $data->eintraege = new stdClass();
        $data->eintraege->anzahl = new stdClass();
        $data->eintraege->anzahl->gesamt = $row_all['eintraege_anzahl_gesamt'];;
        $data->eintraege->anzahl->bestaende = new stdClass();
        $data->eintraege->anzahl->bestaende->GOA = $row_all['eintraege_anzahl_goa'];
        $data->eintraege->anzahl->bestaende->GFA = $row_all['eintraege_anzahl_gfa'];
        $data->eintraege->anzahl->bestaende->GSA = $row_all['eintraege_anzahl_gsa'];

        $sth = $this->db->prepare(
            "SELECT
                YEAR (eintraege.datum_aufnahme) AS jahr,
                COUNT(*) AS anzahl,
                bestaende.signatur AS signatur
            FROM 
                eintraege
            LEFT JOIN 
                bestaende ON bestaende.id = eintraege.bestaende_id 
            GROUP BY
                YEAR (eintraege.datum_aufnahme), eintraege.bestaende_id
           ");
        $sth->execute();

        $row_years_aufnahme = array();
        foreach($sth->fetchAll() as $row){
            if ($data->meta->jahre_spanne->min > $row['jahr']) $data->meta->jahre_spanne->min = $row['jahr'];
            if ($data->meta->jahre_spanne->max < $row['jahr']) $data->meta->jahre_spanne->max = $row['jahr'];

            if (!isset($row_years_aufnahme[$row['jahr']]['gesamt'])){
                $row_years_aufnahme[$row['jahr']]['gesamt'] = 0;
            }
            $row_years_aufnahme[$row['jahr']]['gesamt'] += $row['anzahl'];
            $row_years_aufnahme[$row['jahr']]['bestaende'][$row['signatur']] = $row['anzahl'];
        }
        $data->eintraege->anzahl->aufnahme = $row_years_aufnahme;

        $sth = $this->db->prepare(
            "SELECT
                thema_hauptkategorien.titel AS thema_hauptkategorien_titel,
                thema_hauptkategorien.id AS thema_hauptkategorien_id,
                thema_unterkategorien.titel AS thema_unterkategorien_titel,
                thema_unterkategorien.id AS thema_unterkategorien_id,
                COUNT(eintraege.id) AS anzahl,
                bestaende.signatur AS signatur
            FROM 
                thema_unterkategorien
            LEFT JOIN 
                thema_hauptkategorien ON thema_hauptkategorien.id = thema_unterkategorien.thema_hauptkategorien_id 
            LEFT JOIN 
                bestaende ON bestaende.id = thema_hauptkategorien.bestaende_id 
            RIGHT JOIN 
                eintraege ON eintraege.thema_unterkategorien_id = thema_unterkategorien.id 
            GROUP BY
                thema_hauptkategorien.bestaende_id, thema_hauptkategorien.id, thema_unterkategorien.id
           ");
        $sth->execute();
        $data->eintraege->themen = $sth->fetchAll();

        $sth = $this->db->prepare(
            "SELECT
                medium_hauptkategorien.titel AS medium_hauptkategorien_titel,
                medium_hauptkategorien.id AS medium_hauptkategorien_id,
                medium_unterkategorien.titel AS medium_unterkategorien_titel,
                medium_unterkategorien.id AS medium_unterkategorien_id,
                COUNT(eintraege.id) AS anzahl_gesamt,
                (SELECT COUNT(eintraege.id) FROM eintraege WHERE eintraege.bestaende_id=1 AND eintraege.medium_unterkategorien_id = medium_unterkategorien.id) AS anzahl_goa,
                (SELECT COUNT(eintraege.id) FROM eintraege WHERE eintraege.bestaende_id=2 AND eintraege.medium_unterkategorien_id = medium_unterkategorien.id) AS anzahl_gfa,
                (SELECT COUNT(eintraege.id) FROM eintraege WHERE eintraege.bestaende_id=3 AND eintraege.medium_unterkategorien_id = medium_unterkategorien.id) AS anzahl_gsa
            FROM 
                medium_unterkategorien
            LEFT JOIN 
                medium_hauptkategorien ON medium_hauptkategorien.id = medium_unterkategorien.medium_hauptkategorien_id 
            RIGHT JOIN 
                eintraege ON eintraege.medium_unterkategorien_id = medium_unterkategorien.id 
            GROUP BY
                medium_hauptkategorien.id, medium_unterkategorien.id
           ");
        $sth->execute();
        $data->eintraege->medien = $sth->fetchAll();

        $sth = $this->db->prepare(
            "SELECT
                YEAR (eintraege.datum_bearbeitet) AS jahr,
                COUNT(*) AS anzahl,
                bestaende.signatur AS signatur
            FROM 
                eintraege
            LEFT JOIN 
                bestaende ON bestaende.id = eintraege.bestaende_id 
            GROUP BY
                YEAR (eintraege.datum_bearbeitet), eintraege.bestaende_id
           ");
        $sth->execute();

        $row_years_aufnahme = array();
        foreach($sth->fetchAll() as $row){
            if (!isset($row_years_aufnahme[$row['jahr']]['gesamt'])){
                $row_years_aufnahme[$row['jahr']]['gesamt'] = 0;
            }
            $row_years_aufnahme[$row['jahr']]['gesamt'] += $row['anzahl'];
            $row_years_aufnahme[$row['jahr']]['bestaende'][$row['signatur']] = $row['anzahl'];
        }
        $data->eintraege->anzahl->bearbeitet = $row_years_aufnahme;



        // Items
        $data->items = new stdClass();
        $data->items->archiv_physisch = new stdClass();
        $data->items->archiv_digital = new stdClass();
        $data->items->extern_physisch = new stdClass();
        
        $sth = $this->db->prepare(
            "SELECT 
                COUNT(*) AS anzahl_gesamt,
                (SELECT COUNT(*) FROM items_physisch WHERE eintraege_bestaende_id=1 AND besitzer_goa=1) AS anzahl_goa,
                (SELECT COUNT(*) FROM items_physisch WHERE eintraege_bestaende_id=2 AND besitzer_goa=1) AS anzahl_gfa,
                (SELECT COUNT(*) FROM items_physisch WHERE eintraege_bestaende_id=3 AND besitzer_goa=1) AS anzahl_gsa
            FROM 
                items_physisch
            WHERE
                besitzer_goa=1
           ");
        $sth->execute();
        $row_items_archiv_physisch_anzahl = $sth->fetch();

        $data->items->archiv_physisch->anzahl = new stdClass();
        $data->items->archiv_physisch->anzahl->gesamt = $row_items_archiv_physisch_anzahl['anzahl_gesamt'];;
        $data->items->archiv_physisch->anzahl->bestaende = new stdClass();
        $data->items->archiv_physisch->anzahl->bestaende->GOA = $row_items_archiv_physisch_anzahl['anzahl_goa'];
        $data->items->archiv_physisch->anzahl->bestaende->GFA = $row_items_archiv_physisch_anzahl['anzahl_gfa'];
        $data->items->archiv_physisch->anzahl->bestaende->GSA = $row_items_archiv_physisch_anzahl['anzahl_gsa'];

        $sth = $this->db->prepare(
            "SELECT 
                COUNT(*) AS anzahl_gesamt,
                (SELECT COUNT(*) FROM items_physisch WHERE eintraege_bestaende_id=1 AND besitzer_goa=0) AS anzahl_goa,
                (SELECT COUNT(*) FROM items_physisch WHERE eintraege_bestaende_id=2 AND besitzer_goa=0) AS anzahl_gfa,
                (SELECT COUNT(*) FROM items_physisch WHERE eintraege_bestaende_id=3 AND besitzer_goa=0) AS anzahl_gsa
            FROM 
                items_physisch
            WHERE
                besitzer_goa=0
           ");
        $sth->execute();
        $row_items_extern_physisch_anzahl = $sth->fetch();

        $data->items->extern_physisch->anzahl = new stdClass();
        $data->items->extern_physisch->anzahl->gesamt = $row_items_extern_physisch_anzahl['anzahl_gesamt'];;
        $data->items->extern_physisch->anzahl->bestaende = new stdClass();
        $data->items->extern_physisch->anzahl->bestaende->GOA = $row_items_extern_physisch_anzahl['anzahl_goa'];
        $data->items->extern_physisch->anzahl->bestaende->GFA = $row_items_extern_physisch_anzahl['anzahl_gfa'];
        $data->items->extern_physisch->anzahl->bestaende->GSA = $row_items_extern_physisch_anzahl['anzahl_gsa'];

        $sth = $this->db->prepare(
            "SELECT 
                COUNT(*) AS anzahl_gesamt,
                (SELECT COUNT(*) FROM items_digital WHERE eintraege_bestaende_id=1) AS anzahl_goa,
                (SELECT COUNT(*) FROM items_digital WHERE eintraege_bestaende_id=2) AS anzahl_gfa,
                (SELECT COUNT(*) FROM items_digital WHERE eintraege_bestaende_id=3) AS anzahl_gsa
            FROM 
                items_digital
           ");
        $sth->execute();
        $row_items_archiv_digital_anzahl = $sth->fetch();

        $data->items->archiv_digital->anzahl = new stdClass();
        $data->items->archiv_digital->anzahl->gesamt = $row_items_archiv_digital_anzahl['anzahl_gesamt'];
        $data->items->archiv_digital->anzahl->bestaende = new stdClass();
        $data->items->archiv_digital->anzahl->bestaende->GOA = $row_items_archiv_digital_anzahl['anzahl_goa'];
        $data->items->archiv_digital->anzahl->bestaende->GFA = $row_items_archiv_digital_anzahl['anzahl_gfa'];
        $data->items->archiv_digital->anzahl->bestaende->GSA = $row_items_archiv_digital_anzahl['anzahl_gsa'];


        $sth = $this->db->prepare(
            "SELECT
                filetypen.titel AS filetypen_titel,
                COUNT(*) AS anzahl,
                bestaende.signatur AS signatur
            FROM 
                items_digital
            LEFT JOIN 
                filetypen ON filetypen.id = items_digital.filetypen_id 
            LEFT JOIN 
                bestaende ON bestaende.id = items_digital.eintraege_bestaende_id 
            GROUP BY
                filetypen.titel, items_digital.eintraege_bestaende_id
           ");
        $sth->execute();

        $row_digital_filetypen = array();
        foreach($sth->fetchAll() as $row){
            if (!isset($row_digital_filetypen[$row['filetypen_titel']]['gesamt'])){
                $row_digital_filetypen[$row['filetypen_titel']]['gesamt'] = 0;
            }
            $row_digital_filetypen[$row['filetypen_titel']]['gesamt'] += $row['anzahl'];
            $row_digital_filetypen[$row['filetypen_titel']]['bestaende'][$row['signatur']] = $row['anzahl'];
        }
        $data->items->archiv_digital->filetypen = $row_digital_filetypen;


        $sth = $this->db->prepare(
            "SELECT
                dpi,
                COUNT(*) AS anzahl,
                bestaende.signatur AS signatur
            FROM 
                items_digital
            LEFT JOIN 
                bestaende ON bestaende.id = items_digital.eintraege_bestaende_id 
            GROUP BY
                dpi, items_digital.eintraege_bestaende_id
           ");
        $sth->execute();

        $row_digital_dpi = array();
        foreach($sth->fetchAll() as $row){
            if (!isset($row_digital_dpi[$row['dpi']]['gesamt'])){
                $row_digital_dpi[$row['dpi']]['gesamt'] = 0;
            }
            $row_digital_dpi[$row['dpi']]['gesamt'] += $row['anzahl'];
            $row_digital_dpi[$row['dpi']]['bestaende'][$row['signatur']] = $row['anzahl'];
        }
        $data->items->archiv_digital->dpi = $row_digital_dpi;



        $sth = $this->db->prepare(
            "SELECT
                YEAR (datum) AS jahr,
                COUNT(*) AS anzahl,
                bestaende.signatur AS signatur
            FROM 
                items_digital
            LEFT JOIN 
                bestaende ON bestaende.id = items_digital.eintraege_bestaende_id 
            GROUP BY
                YEAR (datum), items_digital.eintraege_bestaende_id
           ");
        $sth->execute();

        $row_digital_digitalisiert = array();
        foreach($sth->fetchAll() as $row){
            if (!isset($row_digital_digitalisiert[$row['jahr']]['gesamt'])){
                $row_digital_digitalisiert[$row['jahr']]['gesamt'] = 0;
            }
            $row_digital_digitalisiert[$row['jahr']]['gesamt'] += $row['anzahl'];
            $row_digital_digitalisiert[$row['jahr']]['bestaende'][$row['signatur']] = $row['anzahl'];
        }
        $data->items->archiv_digital->digitalisiert = $row_digital_digitalisiert;

        $sth = $this->db->prepare(
            "SELECT 
                LPAD(behaeltnisse.id, 3, '0') AS id_formatted,
                behaeltnisse.id, 
                behaeltnisse.name, 
                behaeltnisse.inhalt, 
                bestaende.id AS bestaende_id, 
                bestaende.signatur AS bestaende_signatur,
                COUNT(*) AS anzahl_gesamt
            FROM 
                items_physisch
            LEFT JOIN behaeltnisse ON 
                behaeltnisse.id = items_physisch.behaeltnisse_id AND 
                items_physisch.behaeltnisse_bestaende_id = behaeltnisse.bestaende_id
            LEFT JOIN bestaende ON 
                bestaende.id = items_physisch.behaeltnisse_bestaende_id
            WHERE
                items_physisch.besitzer_goa = 1
            GROUP BY 
                items_physisch.behaeltnisse_bestaende_id,
                items_physisch.behaeltnisse_id");
        $sth->execute();
        
        $data->behaeltnisse = $sth->fetchAll();

        return $this->response->withJson($data);
    });

?>