<?php

    $app->get('/v1/items/digital', function ($request, $response, $args) {

        $sth = $this->db->prepare(
            "SELECT 
                items_digital.eintraege_id,
                LPAD(eintraege.id, 6, '0') AS eintraege_id_formatted,
                bestaende.id AS bestaende_id,
                bestaende.signatur AS bestaende_signatur,
                items_digital.lfnr,
                filetypen.titel AS filetypen_titel
            FROM 
                items_digital 
            LEFT JOIN 
                eintraege ON eintraege.id = items_digital.eintraege_id AND eintraege.bestaende_id = items_digital.eintraege_bestaende_id
            LEFT JOIN
                bestaende ON bestaende.id = items_digital.eintraege_bestaende_id
            LEFT JOIN 
                filetypen ON filetypen.id = items_digital.filetypen_id"
        );

        $sth->execute();
        $data = $sth->fetchAll();

        return $this->response->withJson($data);
    });

    $app->put('/v1/behaeltniss-zuweisung', function ($request, $response, $args) use ($app) {

        $data = $request->getParsedBody();
        $bestaende_id = null;
        $bestand_signatur_bestaende_id = $data['bestand_signatur_eintrag_id'];
        $bestand_signatur = substr($bestand_signatur_bestaende_id, 0, 3);

        if ($bestand_signatur == 'GOA'){
            $bestaende_id = 1;
        } else if ($bestand_signatur == 'GFA'){
            $bestaende_id = 2;
        } else if ($bestand_signatur == 'GSA'){
            $bestaende_id = 3;
        }

        $eintrag_id = substr($bestand_signatur_bestaende_id, 3, 6);
        
        $behaeltnisse_bestaende_id = $data['behaeltniss']['bestaende_id'];
        $behaeltnisse_id = $data['behaeltniss']['id'];

        $result = new stdClass();
        $result->done = false;
        $result->signatur = $bestand_signatur;
        $result->id = intval($eintrag_id);
        $result->id_formatted = $eintrag_id;
        
        $sth = $this->db->prepare(
            "UPDATE 
                `items_physisch`
            SET 
                `items_physisch`.`behaeltnisse_id` = :behaeltnisse_id,
                `items_physisch`.`behaeltnisse_bestaende_id` = :behaeltnisse_bestaende_id
            WHERE 
                `items_physisch`.`eintraege_id` = :eintraege_id AND
                `items_physisch`.`eintraege_bestaende_id` = :bestaende_id AND
                `items_physisch`.`besitzer_goa` = 1"
        );
        
        $sth->bindParam(':behaeltnisse_id', $behaeltnisse_id, PDO::PARAM_INT);
        $sth->bindParam(':behaeltnisse_bestaende_id', $behaeltnisse_bestaende_id, PDO::PARAM_INT);
        $sth->bindParam(':eintraege_id', $eintrag_id, PDO::PARAM_INT);
        $sth->bindParam(':bestaende_id', $bestaende_id, PDO::PARAM_INT);

        if ($sth->execute()){
            $result->done = true;
        }

        return $this->response->withJson($result);
    });
    

?>