<?php

    $app->get('/v1/suche', function ($request, $response, $args) {

        $bestaende_1_bool = $request->getParam('bestaende_1_bool');
        $bestaende_2_bool = $request->getParam('bestaende_2_bool');
        $bestaende_3_bool = $request->getParam('bestaende_3_bool');
        $suchbegriff = strtolower($request->getParam('suchbegriff'));

        $sth = $this->db->prepare(
            "SELECT 
                eintraege.id AS eintraege_id,
                LPAD(eintraege.id, 6, '0') AS eintraege_id_formatted,
                bestaende.id AS bestaende_id,
                bestaende.signatur AS bestaende_signatur,
                eintraege.titel AS eintraege_titel,
                eintraege.kommentar AS eintraege_kommentar,
                eintraege.zeit_von AS datierung_von_ymd,
                DATE_FORMAT(eintraege.zeit_von, '%d.%m.%Y') AS datierung_von_dmy,
                UNIX_TIMESTAMP(eintraege.zeit_von) AS datierung_von_unix,
                eintraege.zeit_bis AS datierung_bis_ymd,
                DATE_FORMAT(eintraege.zeit_bis, '%d.%m.%Y') AS datierung_bis_dmy,
                UNIX_TIMESTAMP(eintraege.zeit_bis) AS datierung_bis_unix,
                eintraege.zeit_text AS datierung_text,
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

        $sth->bindParam(':bestaende_1_bool', $bestaende_1_bool, PDO::PARAM_INT);
        $sth->bindParam(':bestaende_2_bool', $bestaende_2_bool, PDO::PARAM_INT);
        $sth->bindParam(':bestaende_3_bool', $bestaende_3_bool, PDO::PARAM_INT);
        $sth->execute();

        $all = $sth->fetchAll();
        $eintraege = array();
        $max_search_points = 0;

        foreach($all as $item){

            $item['search_points'] = 0;
            
            if (strpos(strtolower($item['eintraege_titel']), $suchbegriff) !== false){
                $item['search_points'] += 100;
            }
            if (strpos(strtolower($item['eintraege_kommentar']), $suchbegriff) !== false){
                $item['search_points'] += 40;
            }
            if (strpos(strtolower($item['urheber_name']), $suchbegriff) !== false){
                $item['search_points'] += 20;
            }
            if (strpos(strtolower($item['urheber_infos']), $suchbegriff) !== false){
                $item['search_points'] += 10;
            }
            if (strpos(strtolower($item['tags_hauptobjekte']), $suchbegriff) !== false){
                $item['search_points'] += 60;
            }
            if (strpos(strtolower($item['tags_nebenobjekte']), $suchbegriff) !== false){
                $item['search_points'] += 35;
            }
            if (strpos(strtolower($item['tags_personen']), $suchbegriff) !== false){
                $item['search_points'] += 50;
            }

            if ($item['search_points'] > 0){
                if($item['search_points'] > $max_search_points) $max_search_points = $item['search_points'];
                array_push($eintraege, $item);
            }
            
        }

        for($i=0;$i<count($eintraege);$i++){
            $eintraege[$i]['search_percentage'] = ($eintraege[$i]['search_points']/$max_search_points) * 100.00;
        }

        usort($eintraege, function ($item1, $item2) {
            return $item2['search_points'] <=> $item1['search_points'];
        });
        
        return $this->response->withJson($eintraege);
    });

?>