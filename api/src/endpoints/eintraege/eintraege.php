<?php

    $app->get('/v1/eintraege', function ($request, $response, $args) {

        $sth = $this->db->prepare(
            "SELECT 
                eintraege.id AS eintraege_id,
                LPAD(eintraege.id, 6, '0') AS eintraege_id_formatted,
                bestaende.id AS bestaende_id,
                bestaende.signatur AS bestaende_signatur,
                eintraege.titel AS eintraege_titel,
                eintraege.zeit_von AS datierung_von_ymd,
                DATE_FORMAT(eintraege.zeit_von, '%d.%m.%Y') AS datierung_von_dmy,
                UNIX_TIMESTAMP(eintraege.zeit_von) AS datierung_von_unix,
                eintraege.zeit_bis AS datierung_bis_ymd,
                DATE_FORMAT(eintraege.zeit_bis, '%d.%m.%Y') AS datierung_bis_dmy,
                UNIX_TIMESTAMP(eintraege.zeit_bis) AS datierung_bis_unix,
                eintraege.zeit_text AS datierung_text,
                eintraege.urheber_name,
                medium_unterkategorien.id AS medium_unterkategorien_id,
                medium_unterkategorien.titel AS medium_unterkategorien_titel,
                medium_hauptkategorien.id AS medium_hauptkategorien_id,
                medium_hauptkategorien.titel AS medium_hauptkategorien_titel,
                thema_unterkategorien.id AS thema_unterkategorien_id,
                thema_unterkategorien.titel AS thema_unterkategorien_titel,
                thema_hauptkategorien.id AS thema_hauptkategorien_id,
                thema_hauptkategorien.titel AS thema_hauptkategorien_titel,

                (
                    SELECT 
                        COUNT(*) 
                    FROM 
                        items_physisch 
                    WHERE 
                        items_physisch.eintraege_id = eintraege.id AND 
                        items_physisch.eintraege_bestaende_id = bestaende.id AND
                        besitzer_goa = 1
                ) AS items_physisch_archiv,

                (
                    SELECT 
                        COUNT(*) 
                    FROM 
                        items_physisch 
                    WHERE 
                        items_physisch.eintraege_id = eintraege.id AND 
                        items_physisch.eintraege_bestaende_id = bestaende.id AND
                        besitzer_goa = 0
                ) AS items_physisch_extern,

                (
                    SELECT 
                        COUNT(*) 
                    FROM 
                        items_digital 
                    WHERE 
                        items_digital.eintraege_id = eintraege.id AND 
                        items_digital.eintraege_bestaende_id = bestaende.id 
                ) AS items_digital_archiv 
                
            FROM 
                eintraege 
            LEFT JOIN 
                bestaende ON bestaende.id = eintraege.bestaende_id
            LEFT JOIN 
                medium_unterkategorien ON medium_unterkategorien.id = eintraege.medium_unterkategorien_id
            LEFT JOIN
                medium_hauptkategorien ON medium_hauptkategorien.id = medium_unterkategorien.medium_hauptkategorien_id
            LEFT JOIN 
                thema_unterkategorien ON thema_unterkategorien.id = eintraege.thema_unterkategorien_id
            LEFT JOIN
                thema_hauptkategorien ON thema_hauptkategorien.id = thema_unterkategorien.thema_hauptkategorien_id
            WHERE 
                (bestaende.id = 1 AND 1=:bestaende_1_bool) OR
                (bestaende.id = 2 AND 1=:bestaende_2_bool) OR
                (bestaende.id = 3 AND 1=:bestaende_3_bool)
            ORDER BY 
                bestaende.id, eintraege.id"
        );
        $bestaende_1_bool = $request->getParam('bestaende_1_bool');
        $bestaende_2_bool = $request->getParam('bestaende_2_bool');
        $bestaende_3_bool = $request->getParam('bestaende_3_bool');

        $sth->bindParam(':bestaende_1_bool', $bestaende_1_bool, PDO::PARAM_INT);
        $sth->bindParam(':bestaende_2_bool', $bestaende_2_bool, PDO::PARAM_INT);
        $sth->bindParam(':bestaende_3_bool', $bestaende_3_bool, PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetchAll();

        return $this->response->withJson($data);
    });

    $app->get('/v1/eintraege/{bestaende_signatur}[/{id}]', function ($request, $response, $args) {

        if (isset($args['id'])){
            $sth = $this->db->prepare(
                "SELECT 
                    eintraege.id AS eintraege_id,
                    LPAD(eintraege.id, 6, '0') AS eintraege_id_formatted,
                    bestaende.id AS bestaende_id,
                    bestaende.signatur AS bestaende_signatur,
                    eintraege.titel AS eintraege_titel,
                    eintraege.kommentar AS eintraege_kommentar,
                    eintraege.oeffentliche_freigabe AS eintraege_oeffentliche_freigabe,
                    eintraege.zeit_von AS zeit_von_ymd,
                    DATE_FORMAT(eintraege.zeit_von, '%d.%m.%Y') AS zeit_von_dmy,
                    UNIX_TIMESTAMP(eintraege.zeit_von) AS zeit_von_unix,
                    eintraege.zeit_bis AS zeit_bis_ymd,
                    DATE_FORMAT(eintraege.zeit_bis, '%d.%m.%Y') AS zeit_bis_dmy,
                    UNIX_TIMESTAMP(eintraege.zeit_bis) AS zeit_bis_unix,
                    eintraege.zeit_text,
                    eintraege.urheber_name,
                    eintraege.urheber_infos,
                    eintraege.urheber_verstaendnis_eingeholt,
                    eintraege.urheber_sperrfrist_erloschen,
                    eintraege.tags_hauptobjekte,
                    eintraege.tags_nebenobjekte,
                    eintraege.tags_personen,
                    medium_unterkategorien.id AS medium_unterkategorien_id,
                    medium_unterkategorien.titel AS medium_unterkategorien_titel,
                    medium_hauptkategorien.id AS medium_hauptkategorien_id,
                    medium_hauptkategorien.titel AS medium_hauptkategorien_titel,
                    eintraege.medium_freitext AS medium_freitext,
                    thema_unterkategorien.id AS thema_unterkategorien_id,
                    thema_unterkategorien.titel AS thema_unterkategorien_titel,
                    thema_hauptkategorien.id AS thema_hauptkategorien_id,
                    thema_hauptkategorien.titel AS thema_hauptkategorien_titel,

                    eintraege.topothek_eintrag_vorgesehen,
                    eintraege.topothek_status,
                    eintraege.topothek_gml,

                    eintraege.quelle_name,
                    eintraege.quelle_infos,

                    eintraege.serien_id,

                    eintraege.ereignisse_id,

                    eintraege.datum_aufnahme AS datum_aufnahme_ymd,
                    DATE_FORMAT(eintraege.datum_aufnahme, '%d.%m.%Y') AS datum_aufnahme_dmy,
                    UNIX_TIMESTAMP(eintraege.datum_aufnahme) AS datum_aufnahme_unix,
                    eintraege.datum_bearbeitet AS datum_bearbeitet_ymd,
                    DATE_FORMAT(eintraege.datum_bearbeitet, '%d.%m.%Y') AS datum_bearbeitet_dmy,
                    UNIX_TIMESTAMP(eintraege.datum_bearbeitet) AS datum_bearbeitet_unix,

                    (SELECT eintraege.id FROM eintraege 
                    WHERE eintraege.bestaende_id = bestaende.id AND eintraege.id < :id2
                    ORDER BY eintraege.id DESC LIMIT 1) as previous_id,
                    (SELECT eintraege.id FROM eintraege
                    WHERE eintraege.bestaende_id = bestaende.id AND eintraege.id > :id3
                    ORDER BY eintraege.id ASC LIMIT 1) as next_id
                FROM 
                    eintraege 
                LEFT JOIN 
                    bestaende ON bestaende.id = eintraege.bestaende_id
                LEFT JOIN 
                    medium_unterkategorien ON medium_unterkategorien.id = eintraege.medium_unterkategorien_id
                LEFT JOIN
                    medium_hauptkategorien ON medium_hauptkategorien.id = medium_unterkategorien.medium_hauptkategorien_id
                LEFT JOIN 
                    thema_unterkategorien ON thema_unterkategorien.id = eintraege.thema_unterkategorien_id
                LEFT JOIN
                    thema_hauptkategorien ON thema_hauptkategorien.id = thema_unterkategorien.thema_hauptkategorien_id
                WHERE 
                    bestaende.signatur = :bestaende_signatur AND eintraege.id = :id"
            );

            $sth->bindParam(':bestaende_signatur', $args['bestaende_signatur'], PDO::PARAM_STR);
            $sth->bindParam(':id', intval($args['id']), PDO::PARAM_INT);
            $sth->bindParam(':id2', intval($args['id']), PDO::PARAM_INT);
            $sth->bindParam(':id3', intval($args['id']), PDO::PARAM_INT);
            $sth->execute();
            $eintrag = $sth->fetch();

            $data = new stdClass();
            $data->id = $eintrag['eintraege_id'];
            $data->id_formatted = $eintrag['eintraege_id_formatted'];
            $data->titel = $eintrag['eintraege_titel'];
            $data->kommentar = $eintrag['eintraege_kommentar'];
            $data->oeffentliche_freigabe = boolval($eintrag['eintraege_oeffentliche_freigabe']);
            
            $data->bestand = new stdClass();
            $data->bestand->id = $eintrag['bestaende_id'];
            $data->bestand->signatur = $eintrag['bestaende_signatur'];
            
            $data->datierung = new stdClass();
            $data->datierung->von = new stdClass();
            $data->datierung->von->dmy = $eintrag['zeit_von_dmy'];
            $data->datierung->von->ymd = $eintrag['zeit_von_ymd']; 
            $data->datierung->von->unix = $eintrag['zeit_von_unix'];
            $data->datierung->bis = new stdClass();
            $data->datierung->bis->dmy = $eintrag['zeit_bis_dmy'];
            $data->datierung->bis->ymd = $eintrag['zeit_bis_ymd']; 
            $data->datierung->bis->unix = $eintrag['zeit_bis_unix'];
            $data->datierung->text = $eintrag['zeit_text'];
            
            $data->urheber = new stdClass();
            $data->urheber->name = $eintrag['urheber_name'];
            $data->urheber->infos = $eintrag['urheber_infos'];
            $data->urheber->verstaendnis_eingeholt = boolval($eintrag['urheber_verstaendnis_eingeholt']);
            $data->urheber->sperrfrist_erloschen = boolval($eintrag['urheber_sperrfrist_erloschen']);
            
            $data->tags = new stdClass();
            $data->tags->hauptobjekte = $eintrag['tags_hauptobjekte'];
            $data->tags->nebenobjekte = $eintrag['tags_nebenobjekte'];
            $data->tags->personen = $eintrag['tags_personen'];

            $data->medium = new stdClass();
            $data->medium->hauptkategorie = new stdClass();
            $data->medium->hauptkategorie->id = $eintrag['medium_hauptkategorien_id'];
            $data->medium->hauptkategorie->titel = $eintrag['medium_hauptkategorien_titel'];
            $data->medium->unterkategorie = new stdClass();
            $data->medium->unterkategorie->id = $eintrag['medium_unterkategorien_id'];
            $data->medium->unterkategorie->titel = $eintrag['medium_unterkategorien_titel'];
            $data->medium->freitext = $eintrag['medium_freitext'];

            $data->thema = new stdClass();
            $data->thema->hauptkategorie = new stdClass();
            $data->thema->hauptkategorie->id = $eintrag['thema_hauptkategorien_id'];
            $data->thema->hauptkategorie->titel = $eintrag['thema_hauptkategorien_titel'];
            $data->thema->nebenkategorie = new stdClass();
            $data->thema->nebenkategorie->id = $eintrag['thema_unterkategorien_id'];
            $data->thema->nebenkategorie->titel = $eintrag['thema_unterkategorien_titel'];

            $data->topothek = new stdClass();
            $data->topothek->eintrag_vorgesehen = boolval($eintrag['topothek_eintrag_vorgesehen']);
            $data->topothek->status = $eintrag['topothek_status'];
            $data->topothek->gml = $eintrag['topothek_gml'];

            if ($eintrag['serien_id']){
                $data->serie = new stdClass();
            }else{
                $data->serie = null;
            }

            if ($eintrag['ereignisse_id']){
                $data->ereigniss = new stdClass();
            }else{
                $data->ereigniss = null;
            }
            
            $data->quelle = new stdClass();
            $data->quelle->name = $eintrag['quelle_name'];
            $data->quelle->infos = $eintrag['quelle_infos'];

            $data->meta = new stdClass();
            $data->meta->around = new stdClass();
            $data->meta->around->prev = $eintrag['previous_id'];
            $data->meta->around->next = $eintrag['next_id'];
            
            $data->meta->daten = new stdClass();
            $data->meta->daten->aufnahme = new stdClass();
            $data->meta->daten->aufnahme->dmy = $eintrag['datum_aufnahme_dmy'];
            $data->meta->daten->aufnahme->ymd = $eintrag['datum_aufnahme_ymd']; 
            $data->meta->daten->aufnahme->unix = $eintrag['datum_aufnahme_unix'];
            $data->meta->daten->bearbeitet = new stdClass();
            $data->meta->daten->bearbeitet->dmy = $eintrag['datum_bearbeitet_dmy'];
            $data->meta->daten->bearbeitet->ymd = $eintrag['datum_bearbeitet_ymd']; 
            $data->meta->daten->bearbeitet->unix = $eintrag['datum_bearbeitet_unix'];

            $data->items = new stdClass();
            $data->items->archiv_physisch = array();
            $data->items->archiv_digital = array();
            $data->items->extern = array();

            // Physisch im Archiv
            $sth = $this->db->prepare(
                "SELECT 
                    items_physisch.id AS id,
                    originalitaeten.id AS originalitaeten_id,
                    originalitaeten.titel AS originalitaeten_titel,
                    farbraeume.id AS farbraeume_id,
                    farbraeume.titel AS farbraeume_titel,
                    items_physisch.groesse,
                    items_physisch.besitzer_goa,
                    behaeltnisse.id AS behaeltnisse_id,
                    LPAD(behaeltnisse.id, 3, '0') AS behaeltnisse_id_formatted,
                    behaeltnisse.name AS behaeltnisse_name,
                    behaeltnisse.inhalt AS behaeltnisse_inhalt,
                    behaeltnisse_bestaende.id AS behaeltnisse_bestaende_id,
                    behaeltnisse_bestaende.signatur AS behaeltnisse_bestaende_signatur,
                    items_physisch.quellstueck
                FROM 
                    items_physisch 
                LEFT JOIN 
                    farbraeume ON farbraeume.id = items_physisch.farbraeume_id
                LEFT JOIN 
                    behaeltnisse ON behaeltnisse.id = items_physisch.behaeltnisse_id AND behaeltnisse.bestaende_id = items_physisch.behaeltnisse_bestaende_id 
                LEFT JOIN 
                    bestaende AS behaeltnisse_bestaende ON behaeltnisse_bestaende.id = behaeltnisse.bestaende_id
                LEFT JOIN 
                    bestaende ON bestaende.id = items_physisch.eintraege_bestaende_id
                LEFT JOIN 
                    originalitaeten ON originalitaeten.id = items_physisch.originalitaeten_id
                WHERE 
                    bestaende.signatur = :bestaende_signatur AND items_physisch.eintraege_id = :id AND items_physisch.besitzer_goa=1"
            );

            $sth->bindParam(':bestaende_signatur', $args['bestaende_signatur'], PDO::PARAM_STR);
            $sth->bindParam(':id', $args['id'], PDO::PARAM_INT);
            $sth->execute();
            $tmp = $sth->fetchAll();

            foreach($tmp as $item){
                $obj = new stdClass();
                $obj->id = $item['id'];
                $obj->originalitaet = new stdClass();
                $obj->originalitaet->id = $item['originalitaeten_id'];
                $obj->originalitaet->titel = $item['originalitaeten_titel'];
                $obj->farbraum = new stdClass();
                $obj->farbraum->id = $item['farbraeume_id'];
                $obj->farbraum->titel = $item['farbraeume_titel'];
                $obj->groesse = $item['groesse'];
                $obj->besitzer_goa = boolval($item['besitzer_goa']);
                $obj->behaeltniss = new stdClass();
                $obj->behaeltniss->bestaende_id = $item['behaeltnisse_bestaende_id'];
                $obj->behaeltniss->bestaende_signatur = $item['behaeltnisse_bestaende_signatur'];
                $obj->behaeltniss->id = $item['behaeltnisse_id'];
                $obj->behaeltniss->id_formatted = $item['behaeltnisse_id_formatted'];
                $obj->behaeltniss->name = $item['behaeltnisse_name'];
                $obj->behaeltniss->inhalt = $item['behaeltnisse_inhalt'];
                $obj->quellstueck = boolval($item['quellstueck']);
                array_push($data->items->archiv_physisch, $obj);
            }

            // Extern
            $sth = $this->db->prepare(
                "SELECT 
                    items_physisch.id AS id,
                    originalitaeten.id AS originalitaeten_id,
                    originalitaeten.titel AS originalitaeten_titel,
                    farbraeume.id AS farbraeume_id,
                    farbraeume.titel AS farbraeume_titel,
                    items_physisch.groesse,
                    items_physisch.besitzer_goa,
                    items_physisch.besitzer_extern_name,
                    items_physisch.besitzer_extern_geburtsdatum AS besitzer_extern_geburtsdatum_ymd,
                    DATE_FORMAT(items_physisch.besitzer_extern_geburtsdatum, '%d.%m.%Y') AS besitzer_extern_geburtsdatum_dmy,
                    UNIX_TIMESTAMP(items_physisch.besitzer_extern_geburtsdatum) AS besitzer_extern_geburtsdatum_unix,
                    items_physisch.besitzer_extern_adresse,
                    items_physisch.besitzer_extern_plz,
                    items_physisch.besitzer_extern_ort,
                    items_physisch.besitzer_extern_email,
                    items_physisch.besitzer_extern_telefon,
                    items_physisch.besitzer_extern_plz,
                    items_physisch.quellstueck
                FROM 
                    items_physisch 
                LEFT JOIN 
                    farbraeume ON farbraeume.id = items_physisch.farbraeume_id
                LEFT JOIN 
                    bestaende ON bestaende.id = items_physisch.eintraege_bestaende_id
                LEFT JOIN 
                    originalitaeten ON originalitaeten.id = items_physisch.originalitaeten_id
                WHERE 
                    bestaende.signatur = :bestaende_signatur AND items_physisch.eintraege_id = :id AND items_physisch.besitzer_goa=0"
            );

            $sth->bindParam(':bestaende_signatur', $args['bestaende_signatur'], PDO::PARAM_STR);
            $sth->bindParam(':id', $args['id'], PDO::PARAM_INT);
            $sth->execute();
            $tmp = $sth->fetchAll();

            foreach($tmp as $item){
                $obj = new stdClass();
                $obj->id = $item['id'];
                $obj->originalitaet = new stdClass();
                $obj->originalitaet->id = $item['originalitaeten_id'];
                $obj->originalitaet->titel = $item['originalitaeten_titel'];
                $obj->farbraum = new stdClass();
                $obj->farbraum->id = $item['farbraeume_id'];
                $obj->farbraum->titel = $item['farbraeume_titel'];
                $obj->groesse = $item['groesse'];
                $obj->besitzer_goa = boolval($item['besitzer_goa']);
                $obj->behaeltniss = new stdClass();
                $obj->behaeltniss->id = null;
                $obj->besitzer_extern = new stdClass();
                $obj->besitzer_extern->name = $item['besitzer_extern_name'];
                $obj->besitzer_extern->geburtsdatum = new stdClass();
                $obj->besitzer_extern->geburtsdatum->ymd = $item['besitzer_extern_geburtsdatum_ymd'];
                $obj->besitzer_extern->geburtsdatum->dmy = $item['besitzer_extern_geburtsdatum_dmy'];
                $obj->besitzer_extern->geburtsdatum->unix = $item['besitzer_extern_geburtsdatum_unix'];
                $obj->besitzer_extern->adresse = $item['besitzer_extern_adresse'];
                $obj->besitzer_extern->plz = $item['besitzer_extern_plz'];
                $obj->besitzer_extern->ort = $item['besitzer_extern_ort'];
                $obj->besitzer_extern->email = $item['besitzer_extern_email'];
                $obj->besitzer_extern->telefon = $item['besitzer_extern_telefon'];
                $obj->besitzer_extern->plz = $item['besitzer_extern_plz'];
                $obj->quellstueck = boolval($item['quellstueck']);
                array_push($data->items->extern, $obj);
            }

            // Digital im Archiv
            $sth = $this->db->prepare(
                "SELECT 
                    items_digital.id,
                    items_digital.eintraege_id,
                    items_digital.lfnr,
                    filetypen.id AS filetypen_id,
                    filetypen.titel AS filetypen_titel,
                    items_digital.dpi,
                    items_digital.nachbearbeitet,
                    items_digital.beschreibung,
                    items_digital.datum AS datum_ymd,
                    DATE_FORMAT(items_digital.datum, '%d.%m.%Y') AS datum_dmy,
                    UNIX_TIMESTAMP(items_digital.datum) AS datum_unix,
                    items_physisch.id AS items_physisch_link_id,
                    items_physisch.besitzer_goa AS items_physisch_link_besitzer_goa
                FROM 
                    items_digital 
                LEFT JOIN 
                    items_physisch ON items_physisch.id = items_digital.items_physisch_link
                LEFT JOIN 
                    filetypen ON filetypen.id = items_digital.filetypen_id
                LEFT JOIN 
                    bestaende ON bestaende.id = items_digital.eintraege_bestaende_id
                WHERE 
                    bestaende.signatur = :bestaende_signatur AND items_digital.eintraege_id = :id"
            );

            $sth->bindParam(':bestaende_signatur', $args['bestaende_signatur'], PDO::PARAM_STR);
            $sth->bindParam(':id', $args['id'], PDO::PARAM_INT);
            $sth->execute();
            $tmp = $sth->fetchAll();

            foreach($tmp as $item){
                $obj = new stdClass();
                $obj->id = $item['id'];
                $obj->lfnr = $item['lfnr'];
                $obj->physisch_link = new stdClass();
                $obj->physisch_link->id = $item['items_physisch_link_id'];
                $obj->physisch_link->besitzer_goa = boolval($item['items_physisch_link_besitzer_goa']);
                $obj->filetypen = new stdClass();
                $obj->filetypen->id = $item['filetypen_id'];
                $obj->filetypen->titel = $item['filetypen_titel'];
                $obj->dpi = $item['dpi'];
                $obj->nachbearbeitet = boolval($item['nachbearbeitet']);
                $obj->beschreibung = $item['beschreibung'];
                $obj->datum = new stdClass();
                $obj->datum->ymd = $item['datum_ymd'];
                $obj->datum->dmy = $item['datum_dmy'];
                $obj->datum->unix = $item['datum_unix'];
                array_push($data->items->archiv_digital, $obj);
            }
            
        }else{
            $sth = $this->db->prepare(
                "SELECT 
                    eintraege.id AS eintraege_id,
                    LPAD(eintraege.id, 6, '0') AS eintraege_id_formatted,
                    bestaende.id AS bestaende_id,
                    bestaende.signatur AS bestaende_signatur,
                    eintraege.titel AS eintraege_titel,
                    eintraege.zeit_von AS datierung_von_ymd,
                    DATE_FORMAT(eintraege.zeit_von, '%d.%m.%Y') AS datierung_von_dmy,
                    UNIX_TIMESTAMP(eintraege.zeit_von) AS datierung_von_unix,
                    eintraege.zeit_bis AS datierung_bis_ymd,
                    DATE_FORMAT(eintraege.zeit_bis, '%d.%m.%Y') AS datierung_bis_dmy,
                    UNIX_TIMESTAMP(eintraege.zeit_bis) AS datierung_bis_unix,
                    eintraege.zeit_text AS datierung_text,
                    eintraege.urheber_name,
                    medium_unterkategorien.id AS medium_unterkategorien_id,
                    medium_unterkategorien.titel AS medium_unterkategorien_titel,
                    medium_hauptkategorien.id AS medium_hauptkategorien_id,
                    medium_hauptkategorien.titel AS medium_hauptkategorien_titel,
                    thema_unterkategorien.id AS thema_unterkategorien_id,
                    thema_unterkategorien.titel AS thema_unterkategorien_titel,
                    thema_hauptkategorien.id AS thema_hauptkategorien_id,
                    thema_hauptkategorien.titel AS thema_hauptkategorien_titel,

                    (
                        SELECT 
                            COUNT(*) 
                        FROM 
                            items_physisch 
                        WHERE 
                            items_physisch.eintraege_id = eintraege.id AND 
                            items_physisch.eintraege_bestaende_id = bestaende.id AND
                            besitzer_goa = 1
                    ) AS items_physisch_archiv,

                    (
                        SELECT 
                            COUNT(*) 
                        FROM 
                            items_physisch 
                        WHERE 
                            items_physisch.eintraege_id = eintraege.id AND 
                            items_physisch.eintraege_bestaende_id = bestaende.id AND
                            besitzer_goa = 0
                    ) AS items_physisch_extern,

                    (
                        SELECT 
                            COUNT(*) 
                        FROM 
                            items_digital 
                        WHERE 
                            items_digital.eintraege_id = eintraege.id AND 
                            items_digital.eintraege_bestaende_id = bestaende.id 
                    ) AS items_digital_archiv 
                    
                FROM 
                    eintraege 
                LEFT JOIN 
                    bestaende ON bestaende.id = eintraege.bestaende_id
                LEFT JOIN 
                    medium_unterkategorien ON medium_unterkategorien.id = eintraege.medium_unterkategorien_id
                LEFT JOIN
                    medium_hauptkategorien ON medium_hauptkategorien.id = medium_unterkategorien.medium_hauptkategorien_id
                LEFT JOIN 
                    thema_unterkategorien ON thema_unterkategorien.id = eintraege.thema_unterkategorien_id
                LEFT JOIN
                    thema_hauptkategorien ON thema_hauptkategorien.id = thema_unterkategorien.thema_hauptkategorien_id
                WHERE 
                    bestaende.signatur = :bestaende_signatur"
            );

            $sth->bindParam(':bestaende_signatur', $args['bestaende_signatur'], PDO::PARAM_STR);
            $sth->execute();
            $data = $sth->fetchAll();
        }

        return $this->response->withJson($data);
    });

    $app->post('/v1/eintraege', function ($request, $response, $args) use ($app) {

        $data = $request->getParsedBody();
        
        $sth = $this->db->prepare("SELECT id FROM eintraege WHERE bestaende_id = :bestaende_id ORDER BY id DESC LIMIT 1");
        $sth->bindParam(':bestaende_id', $data['bestand']['id'], PDO::PARAM_INT);
        $sth->execute();
        $new_id_for_bestand = intval($sth->fetch()['id']) + 1;

        $result = new stdClass();
        $result->done = true;
        $result->signatur = "";
        $result->id = -1;
        
        $sth = $this->db->prepare(
            "INSERT INTO 
                `eintraege` (
                    `id`, 
                    `bestaende_id`, 
                    `titel`, 
                    `kommentar`, 
                    `zeit_von`, 
                    `zeit_bis`, 
                    `zeit_text`, 
                    `medium_unterkategorien_id`, 
                    `medium_freitext`, 
                    `quelle_name`, 
                    `quelle_infos`, 
                    `urheber_name`, 
                    `urheber_infos`, 
                    `urheber_verstaendnis_eingeholt`, 
                    `urheber_sperrfrist_erloschen`, 
                    `tags_hauptobjekte`, 
                    `tags_nebenobjekte`, 
                    `tags_personen`, 
                    `topothek_eintrag_vorgesehen`, 
                    `topothek_status`, 
                    `topothek_gml`, 
                    `serien_id`, 
                    `ereignisse_id`, 
                    `oeffentliche_freigabe`, 
                    `thema_unterkategorien_id`
                ) VALUES (
                    :id,
                    :bestaende_id,
                    :titel,
                    :kommentar,
                    :zeit_von,
                    :zeit_bis,
                    :zeit_text,
                    :medium_unterkategorien_id,
                    :medium_freitext,
                    :quelle_name,
                    :quelle_infos,
                    :urheber_name,
                    :urheber_infos, 
                    :urheber_verstaendnis_eingeholt, 
                    :urheber_sperrfrist_erloschen, 
                    :tags_hauptobjekte, 
                    :tags_nebenobjekte, 
                    :tags_personen, 
                    :topothek_eintrag_vorgesehen, 
                    :topothek_status, 
                    :topothek_gml, 
                    :serien_id, 
                    :ereignisse_id, 
                    :oeffentliche_freigabe, 
                    :thema_unterkategorien_id
                )
            ");

        $serien_id = null;
        $ereignisse_id = null;
        
        $sth->bindParam(':id', $new_id_for_bestand, PDO::PARAM_INT);
        $sth->bindParam(':bestaende_id', $data['bestand']['id'], PDO::PARAM_INT);
        $sth->bindParam(':titel', $data['titel'], PDO::PARAM_STR);
        $sth->bindParam(':kommentar', $data['kommentar'], PDO::PARAM_STR);
        $sth->bindParam(':zeit_von', $data['datierung']['von']['ymd'], PDO::PARAM_STR);
        $sth->bindParam(':zeit_bis', $data['datierung']['bis']['ymd'], PDO::PARAM_STR);
        $sth->bindParam(':zeit_text', $data['datierung']['text'], PDO::PARAM_STR);
        $sth->bindParam(':medium_unterkategorien_id', $data['medium']['unterkategorie']['id'], PDO::PARAM_INT);
        $sth->bindParam(':medium_freitext', $data['medium']['freitext'], PDO::PARAM_STR);
        $sth->bindParam(':quelle_name', $data['quelle']['name'], PDO::PARAM_STR);
        $sth->bindParam(':quelle_infos', $data['quelle']['infos'], PDO::PARAM_STR);
        $sth->bindParam(':urheber_name', $data['urheber']['name'], PDO::PARAM_STR);
        $sth->bindParam(':urheber_infos', $data['urheber']['infos'], PDO::PARAM_STR);
        $sth->bindParam(':urheber_verstaendnis_eingeholt', $data['urheber']['verstaendnis_eingeholt'], PDO::PARAM_INT);
        $sth->bindParam(':urheber_sperrfrist_erloschen', $data['urheber']['sperrfrist_erloschen'], PDO::PARAM_INT);
        $sth->bindParam(':tags_hauptobjekte', $data['tags']['hauptobjekte'], PDO::PARAM_STR);
        $sth->bindParam(':tags_nebenobjekte', $data['tags']['nebenobjekte'], PDO::PARAM_STR);
        $sth->bindParam(':tags_personen', $data['tags']['personen'], PDO::PARAM_STR);
        $sth->bindParam(':topothek_eintrag_vorgesehen', $data['topothek']['eintrag_vorgesehen'], PDO::PARAM_INT);
        $sth->bindParam(':topothek_status', $data['topothek']['status'], PDO::PARAM_STR);
        $sth->bindParam(':topothek_gml', $data['topothek']['gml'], PDO::PARAM_STR);
        $sth->bindParam(':serien_id', $serien_id, PDO::PARAM_INT);
        $sth->bindParam(':ereignisse_id', $ereignisse_id, PDO::PARAM_INT);
        $sth->bindParam(':oeffentliche_freigabe', $data['oeffentliche_freigabe'], PDO::PARAM_INT);
        $sth->bindParam(':thema_unterkategorien_id', $data['thema']['nebenkategorie']['id'], PDO::PARAM_INT);

        if ($sth->execute()){
            $data['id'] = $this->db->lastInsertId();

            $result->signatur = $data['bestand']['signatur'];
            $result->id = $data['id'];

            foreach($data['items']['archiv_physisch'] as $item){

                $sth2 = $this->db->prepare(
                    "INSERT INTO 
                        `items_physisch` (
                            `eintraege_id`, 
                            `eintraege_bestaende_id`, 
                            `originalitaeten_id`, 
                            `farbraeume_id`, 
                            `groesse`, 
                            `besitzer_goa`, 
                            `besitzer_extern_name`, 
                            `besitzer_extern_geburtsdatum`, 
                            `besitzer_extern_adresse`, 
                            `besitzer_extern_plz`, 
                            `besitzer_extern_ort`, 
                            `besitzer_extern_email`, 
                            `besitzer_extern_telefon`, 
                            `behaeltnisse_id`, 
                            `behaeltnisse_bestaende_id`, 
                            `quellstueck`
                        ) VALUES (
                            :eintraege_id, 
                            :eintraege_bestaende_id, 
                            :originalitaeten_id, 
                            :farbraeume_id, 
                            :groesse, 
                            1, 
                            null, 
                            null, 
                            null, 
                            null, 
                            null, 
                            null, 
                            null, 
                            :behaeltnisse_id, 
                            :behaeltnisse_bestaende_id, 
                            :quellstueck
                        );
                    ");

                $sth2->bindParam(':eintraege_id', $data['id'], PDO::PARAM_INT);
                $sth2->bindParam(':eintraege_bestaende_id', $data['bestand']['id'], PDO::PARAM_INT);
                $sth2->bindParam(':originalitaeten_id', $item['originalitaet']['id'], PDO::PARAM_INT);
                $sth2->bindParam(':farbraeume_id', $item['farbraum']['id'], PDO::PARAM_INT);
                $sth2->bindParam(':groesse', $item['groesse'], PDO::PARAM_STR);
                $sth2->bindParam(':behaeltnisse_id', $item['behaeltniss']['id'], PDO::PARAM_INT);
                $sth2->bindParam(':behaeltnisse_bestaende_id', $item['behaeltniss']['bestaende_id'], PDO::PARAM_INT);
                $sth2->bindParam(':quellstueck', intval($item['quellstueck']), PDO::PARAM_INT);

                $sth2->execute();

            }

            $i=0;
            foreach($data['items']['archiv_digital'] as $item){

                $i++;

                $sth3 = $this->db->prepare(
                    "INSERT INTO 
                        `items_digital` (
                            `eintraege_id`, 
                            `eintraege_bestaende_id`, 
                            `lfnr`, 
                            `items_physisch_link`, 
                            `filetypen_id`, 
                            `dpi`, 
                            `nachbearbeitet`, 
                            `beschreibung`, 
                            `datum`
                        ) VALUES (
                            :eintraege_id, 
                            :eintraege_bestaende_id, 
                            :lfnr, 
                            :items_physisch_link, 
                            :filetypen_id, 
                            :dpi, 
                            :nachbearbeitet, 
                            :beschreibung, 
                            :datum
                        );
                    ");

                $sth3->bindParam(':eintraege_id', $data['id'], PDO::PARAM_INT);
                $sth3->bindParam(':eintraege_bestaende_id', $data['bestand']['id'], PDO::PARAM_INT);
                $sth3->bindParam(':lfnr', $i, PDO::PARAM_INT);
                $sth3->bindParam(':items_physisch_link', $item['physisch_link']['id'], PDO::PARAM_INT);
                $sth3->bindParam(':filetypen_id', $item['filetypen']['id'], PDO::PARAM_INT);
                $sth3->bindParam(':dpi', $item['dpi'], PDO::PARAM_INT);
                $sth3->bindParam(':nachbearbeitet', $item['nachbearbeitet'], PDO::PARAM_INT);
                $sth3->bindParam(':beschreibung', $item['beschreibung'], PDO::PARAM_STR);
                $sth3->bindParam(':datum', $item['datum']['ymd'], PDO::PARAM_STR);

                $sth3->execute();
            }

            foreach($data['items']['extern'] as $item){

                $sth4 = $this->db->prepare(
                    "INSERT INTO 
                        `items_physisch` (
                            `eintraege_id`, 
                            `eintraege_bestaende_id`, 
                            `originalitaeten_id`, 
                            `farbraeume_id`, 
                            `groesse`, 
                            `besitzer_goa`, 
                            `besitzer_extern_name`, 
                            `besitzer_extern_geburtsdatum`, 
                            `besitzer_extern_adresse`, 
                            `besitzer_extern_plz`, 
                            `besitzer_extern_ort`, 
                            `besitzer_extern_email`, 
                            `besitzer_extern_telefon`, 
                            `behaeltnisse_id`, 
                            `behaeltnisse_bestaende_id`, 
                            `quellstueck`
                        ) VALUES (
                            :eintraege_id, 
                            :eintraege_bestaende_id, 
                            :originalitaeten_id, 
                            :farbraeume_id, 
                            :groesse, 
                            0, 
                            :besitzer_extern_name, 
                            :besitzer_extern_geburtsdatum, 
                            :besitzer_extern_adresse, 
                            :besitzer_extern_plz, 
                            :besitzer_extern_ort, 
                            :besitzer_extern_email, 
                            :besitzer_extern_telefon, 
                            null, 
                            null, 
                            :quellstueck
                        );
                    ");

                $sth4->bindParam(':eintraege_id', $data['id'], PDO::PARAM_INT);
                $sth4->bindParam(':eintraege_bestaende_id', $data['bestand']['id'], PDO::PARAM_INT);
                $sth4->bindParam(':originalitaeten_id', $item['originalitaet']['id'], PDO::PARAM_INT);
                $sth4->bindParam(':farbraeume_id', $item['farbraum']['id'], PDO::PARAM_INT);
                $sth4->bindParam(':groesse', $item['groesse'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_name', $item['besitzer_extern']['name'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_geburtsdatum', $item['besitzer_extern']['geburtsdatum'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_adresse', $item['besitzer_extern']['adresse'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_plz', $item['besitzer_extern']['plz'], PDO::PARAM_INT);
                $sth4->bindParam(':besitzer_extern_ort', $item['besitzer_extern']['ort'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_email', $item['besitzer_extern']['email'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_telefon', $item['besitzer_extern']['telefon'], PDO::PARAM_STR);
                $sth4->bindParam(':quellstueck', $item['quellstueck'], PDO::PARAM_INT);

                $sth4->execute();
            }

        }else{
            $result->done = false;
        }

        return $this->response->withJson($result);

    });
    
    $app->put('/v1/eintraege/{bestaende_signatur}/{id}', function ($request, $response, $args) use ($app) {

        $data = $request->getParsedBody();
        $bestaende_signatur = $args['bestaende_signatur'];
        $id = $args['id'];

        $result = new stdClass();
        $result->done = true;
        $result->signatur = $bestaende_signatur;
        $result->id = $id;

        $sth = $this->db->prepare(
            "UPDATE 
                `eintraege` 
            SET 
                `titel` = :titel, 
                `kommentar` = :kommentar, 
                `zeit_von` = :zeit_von, 
                `zeit_bis` = :zeit_bis, 
                `zeit_text` = :zeit_text, 
                `medium_unterkategorien_id` = :medium_unterkategorien_id, 
                `medium_freitext` = :medium_freitext, 
                `thema_unterkategorien_id` = :thema_unterkategorien_id, 
                `quelle_name` = :quelle_name, 
                `quelle_infos` = :quelle_infos, 
                `urheber_name` = :urheber_name, 
                `urheber_infos` = :urheber_infos, 
                `urheber_verstaendnis_eingeholt` = :urheber_verstaendnis_eingeholt, 
                `urheber_sperrfrist_erloschen` = :urheber_sperrfrist_erloschen, 
                `tags_hauptobjekte` = :tags_hauptobjekte, 
                `tags_nebenobjekte` = :tags_nebenobjekte, 
                `tags_personen` = :tags_personen, 
                `topothek_eintrag_vorgesehen` = :topothek_eintrag_vorgesehen, 
                `topothek_status` = :topothek_status, 
                `topothek_gml` = :topothek_gml, 
                `serien_id` = :serien_id, 
                `ereignisse_id` = :ereignisse_id, 
                `oeffentliche_freigabe` = :oeffentliche_freigabe,
                `datum_bearbeitet` = CURRENT_TIMESTAMP
            WHERE 
                `eintraege`.`id` = :id AND 
                `eintraege`.`bestaende_id` = :bestaende_id 
            ");

        $serien_id = null;
        $ereignisse_id = null;
        
        $sth->bindParam(':id', $id, PDO::PARAM_INT);
        $sth->bindParam(':bestaende_id', $data['bestand']['id'], PDO::PARAM_INT);
        $sth->bindParam(':titel', $data['titel'], PDO::PARAM_STR);
        $sth->bindParam(':kommentar', $data['kommentar'], PDO::PARAM_STR);
        $sth->bindParam(':zeit_von', $data['datierung']['von']['ymd'], PDO::PARAM_STR);
        $sth->bindParam(':zeit_bis', $data['datierung']['bis']['ymd'], PDO::PARAM_STR);
        $sth->bindParam(':zeit_text', $data['datierung']['text'], PDO::PARAM_STR);
        $sth->bindParam(':medium_unterkategorien_id', $data['medium']['unterkategorie']['id'], PDO::PARAM_INT);
        $sth->bindParam(':medium_freitext', $data['medium']['freitext'], PDO::PARAM_STR);
        $sth->bindParam(':quelle_name', $data['quelle']['name'], PDO::PARAM_STR);
        $sth->bindParam(':quelle_infos', $data['quelle']['infos'], PDO::PARAM_STR);
        $sth->bindParam(':urheber_name', $data['urheber']['name'], PDO::PARAM_STR);
        $sth->bindParam(':urheber_infos', $data['urheber']['infos'], PDO::PARAM_STR);
        $sth->bindParam(':urheber_verstaendnis_eingeholt', $data['urheber']['verstaendnis_eingeholt'], PDO::PARAM_INT);
        $sth->bindParam(':urheber_sperrfrist_erloschen', $data['urheber']['sperrfrist_erloschen'], PDO::PARAM_INT);
        $sth->bindParam(':tags_hauptobjekte', $data['tags']['hauptobjekte'], PDO::PARAM_STR);
        $sth->bindParam(':tags_nebenobjekte', $data['tags']['nebenobjekte'], PDO::PARAM_STR);
        $sth->bindParam(':tags_personen', $data['tags']['personen'], PDO::PARAM_STR);
        $sth->bindParam(':topothek_eintrag_vorgesehen', $data['topothek']['eintrag_vorgesehen'], PDO::PARAM_INT);
        $sth->bindParam(':topothek_status', $data['topothek']['status'], PDO::PARAM_STR);
        $sth->bindParam(':topothek_gml', $data['topothek']['gml'], PDO::PARAM_STR);
        $sth->bindParam(':serien_id', $serien_id, PDO::PARAM_INT);
        $sth->bindParam(':ereignisse_id', $ereignisse_id, PDO::PARAM_INT);
        $sth->bindParam(':oeffentliche_freigabe', $data['oeffentliche_freigabe'], PDO::PARAM_INT);
        $sth->bindParam(':thema_unterkategorien_id', $data['thema']['nebenkategorie']['id'], PDO::PARAM_INT);

        if ($sth->execute()){

            foreach($data['items']['archiv_physisch'] as $item){

                // id = -1 ==> New
                if ($item['id'] <= 0){

                    $sth2 = $this->db->prepare(
                        "INSERT INTO 
                            `items_physisch` (
                                `eintraege_id`, 
                                `eintraege_bestaende_id`, 
                                `originalitaeten_id`, 
                                `farbraeume_id`, 
                                `groesse`, 
                                `besitzer_goa`, 
                                `besitzer_extern_name`, 
                                `besitzer_extern_geburtsdatum`, 
                                `besitzer_extern_adresse`, 
                                `besitzer_extern_plz`, 
                                `besitzer_extern_ort`, 
                                `besitzer_extern_email`, 
                                `besitzer_extern_telefon`, 
                                `behaeltnisse_id`, 
                                `behaeltnisse_bestaende_id`, 
                                `quellstueck`
                            ) VALUES (
                                :eintraege_id, 
                                :eintraege_bestaende_id, 
                                :originalitaeten_id, 
                                :farbraeume_id, 
                                :groesse, 
                                1, 
                                null, 
                                null, 
                                null, 
                                null, 
                                null, 
                                null, 
                                null, 
                                :behaeltnisse_id, 
                                :behaeltnisse_bestaende_id, 
                                :quellstueck
                            );
                        ");

                    $sth2->bindParam(':eintraege_id', $data['id'], PDO::PARAM_INT);
                    $sth2->bindParam(':eintraege_bestaende_id', $data['bestand']['id'], PDO::PARAM_INT);
                
                // id >= 1 ==> Bestehendes Item
                }else{
                    
                    $sth2 = $this->db->prepare(
                        "UPDATE 
                            `items_physisch` 
                        SET 
                            `originalitaeten_id` = :originalitaeten_id, 
                            `farbraeume_id` = :farbraeume_id, 
                            `groesse` = :groesse, 
                            `besitzer_goa` = 1, 
                            `besitzer_extern_name` = null, 
                            `besitzer_extern_geburtsdatum` = null, 
                            `besitzer_extern_adresse` = null,  
                            `besitzer_extern_plz` = null,  
                            `besitzer_extern_ort` = null,  
                            `besitzer_extern_email` = null,  
                            `besitzer_extern_telefon` = null,  
                            `behaeltnisse_id` = :behaeltnisse_id, 
                            `behaeltnisse_bestaende_id` = :behaeltnisse_bestaende_id, 
                            `quellstueck` = :quellstueck
                        WHERE 
                            `items_physisch`.`id` = :id
                    ");

                    $sth2->bindParam(':id', $item['id'], PDO::PARAM_INT);
                }

                $sth2->bindParam(':originalitaeten_id', $item['originalitaet']['id'], PDO::PARAM_INT);
                $sth2->bindParam(':farbraeume_id', $item['farbraum']['id'], PDO::PARAM_INT);
                $sth2->bindParam(':groesse', $item['groesse'], PDO::PARAM_STR);
                $sth2->bindParam(':behaeltnisse_id', $item['behaeltniss']['id'], PDO::PARAM_INT);
                $sth2->bindParam(':behaeltnisse_bestaende_id', $item['behaeltniss']['bestaende_id'], PDO::PARAM_INT);
                $sth2->bindParam(':quellstueck', intval($item['quellstueck']), PDO::PARAM_INT);

                $sth2->execute();

            }

            foreach($data['items']['archiv_digital'] as $item){

                // id = -1 ==> New
                if ($item['id'] <= 0){

                    $sth3 = $this->db->prepare(
                        "INSERT INTO 
                            `items_digital` (
                            `eintraege_id`, 
                            `eintraege_bestaende_id`, 
                            `lfnr`, 
                            `items_physisch_link`, 
                            `filetypen_id`, 
                            `dpi`, 
                            `nachbearbeitet`, 
                            `beschreibung`, 
                            `datum`
                        ) VALUES (
                            :eintraege_id, 
                            :eintraege_bestaende_id, 
                            :lfnr, 
                            :items_physisch_link, 
                            :filetypen_id, 
                            :dpi, 
                            :nachbearbeitet, 
                            :beschreibung, 
                            :datum
                        );
                    ");
    
                    $sth3->bindParam(':eintraege_id', $data['id'], PDO::PARAM_INT);
                    $sth3->bindParam(':eintraege_bestaende_id', $data['bestand']['id'], PDO::PARAM_INT);
                
                // id >= 1 ==> Bestehendes Item
                }else{
                    
                    $sth3 = $this->db->prepare(
                        "UPDATE 
                            `items_digital` 
                        SET 
                            `lfnr` = :lfnr, 
                            `items_physisch_link` = :items_physisch_link,
                            `filetypen_id` = :filetypen_id, 
                            `dpi` = :dpi, 
                            `nachbearbeitet` = :nachbearbeitet, 
                            `beschreibung` = :beschreibung, 
                            `datum` = :datum 
                        WHERE 
                            `items_digital`.`id` = :id; 
                        ");

                    $sth3->bindParam(':id', $item['id'], PDO::PARAM_INT);
                }

                if ($item['physisch_link']['id'] == 'null') $item['physisch_link']['id'] = null;
                
                $sth3->bindParam(':lfnr', $item['lfnr'], PDO::PARAM_INT);
                $sth3->bindParam(':items_physisch_link', $item['physisch_link']['id'], PDO::PARAM_INT);
                $sth3->bindParam(':filetypen_id', $item['filetypen']['id'], PDO::PARAM_INT);
                $sth3->bindParam(':dpi', $item['dpi'], PDO::PARAM_INT);
                $sth3->bindParam(':nachbearbeitet', $item['nachbearbeitet'], PDO::PARAM_INT);
                $sth3->bindParam(':beschreibung', $item['beschreibung'], PDO::PARAM_STR);
                $sth3->bindParam(':datum', $item['datum']['ymd'], PDO::PARAM_STR);

                $sth3->execute();

            }

            
            foreach($data['items']['extern'] as $item){

                // id = -1 ==> New
                if ($item['id'] <= 0){

                    $sth4 = $this->db->prepare(
                        "INSERT INTO 
                            `items_physisch` (
                                `eintraege_id`, 
                                `eintraege_bestaende_id`, 
                                `originalitaeten_id`, 
                                `farbraeume_id`, 
                                `groesse`, 
                                `besitzer_goa`, 
                                `besitzer_extern_name`, 
                                `besitzer_extern_geburtsdatum`, 
                                `besitzer_extern_adresse`, 
                                `besitzer_extern_plz`, 
                                `besitzer_extern_ort`, 
                                `besitzer_extern_email`, 
                                `besitzer_extern_telefon`, 
                                `behaeltnisse_id`, 
                                `behaeltnisse_bestaende_id`, 
                                `quellstueck`
                            ) VALUES (
                                :eintraege_id, 
                                :eintraege_bestaende_id, 
                                :originalitaeten_id, 
                                :farbraeume_id, 
                                :groesse, 
                                0, 
                                :besitzer_extern_name, 
                                :besitzer_extern_geburtsdatum, 
                                :besitzer_extern_adresse, 
                                :besitzer_extern_plz, 
                                :besitzer_extern_ort, 
                                :besitzer_extern_email, 
                                :besitzer_extern_telefon, 
                                null, 
                                null, 
                                :quellstueck
                            );
                        ");

                    $sth4->bindParam(':eintraege_id', $data['id'], PDO::PARAM_INT);
                    $sth4->bindParam(':eintraege_bestaende_id', $data['bestand']['id'], PDO::PARAM_INT);
                
                // id >= 1 ==> Bestehendes Item
                }else{
                    
                    $sth4 = $this->db->prepare(
                        "UPDATE 
                            `items_physisch` 
                        SET 
                            `originalitaeten_id` = :originalitaeten_id, 
                            `farbraeume_id` = :farbraeume_id, 
                            `groesse` = :groesse, 
                            `besitzer_goa` = 0, 
                            `besitzer_extern_name` = :besitzer_extern_name, 
                            `besitzer_extern_geburtsdatum` = :besitzer_extern_geburtsdatum, 
                            `besitzer_extern_adresse` = :besitzer_extern_adresse,  
                            `besitzer_extern_plz` = :besitzer_extern_plz,  
                            `besitzer_extern_ort` = :besitzer_extern_ort,  
                            `besitzer_extern_email` = :besitzer_extern_email,  
                            `besitzer_extern_telefon` = :besitzer_extern_telefon,  
                            `behaeltnisse_id` = null, 
                            `behaeltnisse_bestaende_id` = null, 
                            `quellstueck` = :quellstueck
                        WHERE 
                            `items_physisch`.`id` = :id
                    ");

                    $sth4->bindParam(':id', $item['id'], PDO::PARAM_INT);
                }

                $sth4->bindParam(':originalitaeten_id', $item['originalitaet']['id'], PDO::PARAM_INT);
                $sth4->bindParam(':farbraeume_id', $item['farbraum']['id'], PDO::PARAM_INT);
                $sth4->bindParam(':groesse', $item['groesse'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_name', $item['besitzer_extern']['name'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_geburtsdatum', $item['besitzer_extern']['geburtsdatum']['ymd'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_adresse', $item['besitzer_extern']['adresse'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_plz', $item['besitzer_extern']['plz'], PDO::PARAM_INT);
                $sth4->bindParam(':besitzer_extern_ort', $item['besitzer_extern']['ort'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_email', $item['besitzer_extern']['email'], PDO::PARAM_STR);
                $sth4->bindParam(':besitzer_extern_telefon', $item['besitzer_extern']['telefon'], PDO::PARAM_STR);
                $sth4->bindParam(':quellstueck', $item['quellstueck'], PDO::PARAM_INT);

                $sth4->execute();

            }
            

            // Delete

            foreach($data['items']['delete']['physisch'] as $id){
                $sth = $this->db->prepare("DELETE FROM items_physisch WHERE id=:id");
                $sth->bindParam(':id', $id, PDO::PARAM_INT);
                $sth->execute();
            }

            foreach($data['items']['delete']['digital'] as $id){
                $sth = $this->db->prepare("DELETE FROM items_digital WHERE id=:id");
                $sth->bindParam(':id', $id, PDO::PARAM_INT);
                $sth->execute();
            }

        }else{
            $result->done = false;
        }

        return $this->response->withJson($result);

    });


    $app->delete('/v1/eintraege/{bestaende_id}/{id}', function ($request, $response, $args) use ($app) {

        $data = $request->getParsedBody();
        $bestaende_id = $args['bestaende_id'];
        $id = $args['id'];
        $info = $data['info'];
        $ret = new stdClass();
        
        $sth = $this->db->prepare("DELETE FROM items_physisch WHERE eintraege_id=:eintraege_id AND eintraege_bestaende_id=:eintraege_bestaende_id");
        $sth->bindParam(':eintraege_id', $id, PDO::PARAM_INT);
        $sth->bindParam(':eintraege_bestaende_id', $bestaende_id, PDO::PARAM_INT);
        $sth->execute();
        
        $sth = $this->db->prepare("DELETE FROM items_digital WHERE eintraege_id=:eintraege_id AND eintraege_bestaende_id=:eintraege_bestaende_id");
        $sth->bindParam(':eintraege_id', $id, PDO::PARAM_INT);
        $sth->bindParam(':eintraege_bestaende_id', $bestaende_id, PDO::PARAM_INT);
        $sth->execute();
        
        $sth = $this->db->prepare("DELETE FROM eintraege WHERE id=:eintraege_id AND bestaende_id=:eintraege_bestaende_id");
        $sth->bindParam(':eintraege_id', $id, PDO::PARAM_INT);
        $sth->bindParam(':eintraege_bestaende_id', $bestaende_id, PDO::PARAM_INT);
        $ret->done = $sth->execute();

        $sth = $this->db->prepare(
            "INSERT INTO 
                `eintraege_history` (
                    `bestaende_id`, 
                    `eintraege_id`, 
                    `type`, 
                    `ueberfuehrung_bestaende_id`, 
                    `ueberfuehrung_eintraege_id`, 
                    `info`
                ) VALUES (
                    :bestaende_id,
                    :eintraege_id, 
                    'Entfernung', 
                    null, 
                    null,  
                    :info
                );
            ");

        $sth->bindParam(':eintraege_id', $id, PDO::PARAM_INT);
        $sth->bindParam(':bestaende_id', $bestaende_id, PDO::PARAM_INT);
        $sth->bindParam(':info', $info, PDO::PARAM_STR);

        $sth->execute();

        return $this->response->withJson($ret);

    });

?>