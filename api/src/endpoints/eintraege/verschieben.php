<?php

    $app->get('/v1/eintraege-history', function ($request, $response, $args) {

        $sth = $this->db->prepare(
            "SELECT 
                eintraege_history.id,
                bestaende.id AS bestaende_id,
                bestaende.signatur AS bestaende_signatur,
                eintraege_history.eintraege_id AS eintraege_id,
                LPAD(eintraege_history.eintraege_id, 6, '0') AS eintraege_id_formatted,
                eintraege_history.type,
                bestaende_ueberfuehrung.id AS bestaende_ueberfuehrung_id,
                bestaende_ueberfuehrung.signatur AS bestaende_ueberfuehrung_signatur,
                eintraege_ueberfuehrung.id AS eintraege_ueberfuehrung_id,
                LPAD(eintraege_ueberfuehrung.id, 6, '0') AS eintraege_ueberfuehrung_id_formatted,
                eintraege_ueberfuehrung.titel AS eintraege_ueberfuehrung_titel,
                eintraege_history.info,
                eintraege_history.timestamp
            FROM 
                eintraege_history 
            LEFT JOIN 
                bestaende ON bestaende.id = eintraege_history.bestaende_id
            LEFT JOIN 
                bestaende AS bestaende_ueberfuehrung ON bestaende_ueberfuehrung.id = eintraege_history.ueberfuehrung_bestaende_id
            LEFT JOIN
                eintraege AS eintraege_ueberfuehrung ON (eintraege_ueberfuehrung.id = eintraege_history.ueberfuehrung_eintraege_id AND eintraege_ueberfuehrung.bestaende_id = bestaende_ueberfuehrung.id)
            ORDER BY 
                eintraege_history.timestamp DESC       
        ");

        $sth->execute();
        $data = $sth->fetchAll();

        return $this->response->withJson($data);
    });

    $app->put('/v1/eintraege/verschieben/{von_besteande_id}/{von_id}/{zu_bestaende_id}', function ($request, $response, $args) use ($app) {

        $data = $request->getParsedBody();
        $von_bestaende_id = intval($args['von_besteande_id']);
        $von_id = intval($args['von_id']);
        $zu_besteande_id = intval($args['zu_bestaende_id']);
        $info = $data['info'];
            
        $sth = $this->db->prepare("SELECT id FROM eintraege WHERE bestaende_id = :bestaende_id ORDER BY id DESC LIMIT 1");
        $sth->bindParam(':bestaende_id', $zu_besteande_id, PDO::PARAM_INT);
        $sth->execute();
        $zu_id = intval($sth->fetch()['id']) + 1;

        $sth = $this->db->prepare("SET FOREIGN_KEY_CHECKS=0");
        $sth->execute();

        $result = new stdClass();

        $sth = $this->db->prepare(
            "UPDATE 
                `eintraege` 
            SET 
                `id` = :zu_id, 
                `bestaende_id` = :zu_bestaende_id, 
                `thema_unterkategorien_id` = null,
                `datum_bearbeitet` = CURRENT_TIMESTAMP
            WHERE 
                `eintraege`.`id` = :von_id AND 
                `eintraege`.`bestaende_id` = :von_bestaende_id 
            ");

        $sth->bindParam(':zu_id', $zu_id, PDO::PARAM_INT);
        $sth->bindParam(':zu_bestaende_id', $zu_besteande_id, PDO::PARAM_INT);
        $sth->bindParam(':von_id', $von_id, PDO::PARAM_INT);
        $sth->bindParam(':von_bestaende_id', $von_bestaende_id, PDO::PARAM_INT);

        //var_dump($zu_id, $zu_besteande_id, $von_id, $von_bestaende_id);

        if ($sth->execute()){

            $sth = $this->db->prepare(
                "UPDATE 
                    `items_physisch` 
                SET 
                    `eintraege_id` = :zu_id, 
                    `eintraege_bestaende_id` = :zu_bestaende_id
                WHERE 
                    `eintraege_id` = :von_id AND
                    `eintraege_bestaende_id` = :von_bestaende_id
            ");

            $sth->bindParam(':zu_id', $zu_id, PDO::PARAM_INT);
            $sth->bindParam(':zu_bestaende_id', $zu_besteande_id, PDO::PARAM_INT);
            $sth->bindParam(':von_id', $von_id, PDO::PARAM_INT);
            $sth->bindParam(':von_bestaende_id', $von_bestaende_id, PDO::PARAM_INT);

            $sth->execute();
            
                    
            $sth = $this->db->prepare(
                "UPDATE 
                    `items_digital` 
                SET 
                    `eintraege_id` = :zu_id, 
                    `eintraege_bestaende_id` = :zu_bestaende_id
                WHERE 
                    `eintraege_id` = :von_id AND
                    `eintraege_bestaende_id` = :von_bestaende_id
                ");

            $sth->bindParam(':zu_id', $zu_id, PDO::PARAM_INT);
            $sth->bindParam(':zu_bestaende_id', $zu_besteande_id, PDO::PARAM_INT);
            $sth->bindParam(':von_id', $von_id, PDO::PARAM_INT);
            $sth->bindParam(':von_bestaende_id', $von_bestaende_id, PDO::PARAM_INT);

            $sth->execute();


            $sth = $this->db->prepare("SET FOREIGN_KEY_CHECKS=1");
            $sth->execute();


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
                        :von_bestaende_id,
                        :von_id, 
                        'Überführung', 
                        :zu_bestaende_id, 
                        :zu_id,  
                        :info
                    );
                ");

            $sth->bindParam(':zu_id', $zu_id, PDO::PARAM_INT);
            $sth->bindParam(':zu_bestaende_id', $zu_besteande_id, PDO::PARAM_INT);
            $sth->bindParam(':von_id', $von_id, PDO::PARAM_INT);
            $sth->bindParam(':von_bestaende_id', $von_bestaende_id, PDO::PARAM_INT);
            $sth->bindParam(':info', $info, PDO::PARAM_STR);

            $sth->execute();


            $result->done = true;
            $result->bestaende_id = $zu_besteande_id;
            $result->id = $zu_id;

        }else{
            $result->done = false;
        }

        return $this->response->withJson($result);

    });

?>