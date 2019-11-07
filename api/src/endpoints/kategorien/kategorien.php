<?php

    $app->get('/v1/kategorien', function ($request, $response, $args) {

        $data = new stdClass();

        $sth = $this->db->prepare("SELECT * FROM thema_hauptkategorien");
        $sth->execute();
        $data->thema_hauptkategorien = $sth->fetchAll();

        $sth = $this->db->prepare("SELECT * FROM thema_unterkategorien");
        $sth->execute();
        $data->thema_unterkategorien = $sth->fetchAll();

        $sth = $this->db->prepare("SELECT * FROM medium_hauptkategorien");
        $sth->execute();
        $data->medium_hauptkategorien = $sth->fetchAll();

        $sth = $this->db->prepare("SELECT * FROM medium_unterkategorien");
        $sth->execute();
        $data->medium_unterkategorien = $sth->fetchAll();

        return $this->response->withJson($data);
    });

    $app->get('/v1/selects', function ($request, $response, $args) {

        $data = new stdClass();

        $sth = $this->db->prepare("SELECT * FROM thema_hauptkategorien");
        $sth->execute();
        $data->thema_hauptkategorien = $sth->fetchAll();

        $sth = $this->db->prepare("SELECT * FROM thema_unterkategorien");
        $sth->execute();
        $data->thema_unterkategorien = $sth->fetchAll();

        $sth = $this->db->prepare("SELECT * FROM medium_hauptkategorien");
        $sth->execute();
        $data->medium_hauptkategorien = $sth->fetchAll();

        $sth = $this->db->prepare("SELECT * FROM medium_unterkategorien");
        $sth->execute();
        $data->medium_unterkategorien = $sth->fetchAll();
        
        $sth = $this->db->prepare("SELECT LPAD(behaeltnisse.id, 3, '0') AS id_formatted, behaeltnisse.id, behaeltnisse.name, behaeltnisse.inhalt, bestaende.id AS bestaende_id, bestaende.signatur AS bestaende_signatur FROM behaeltnisse LEFT JOIN bestaende ON bestaende.id = behaeltnisse.bestaende_id");
        $sth->execute();
        $data->behaeltnisse = $sth->fetchAll();

        $sth = $this->db->prepare("SELECT * FROM farbraeume");
        $sth->execute();
        $data->farbraeume = $sth->fetchAll();

        $sth = $this->db->prepare("SELECT * FROM filetypen");
        $sth->execute();
        $data->filetypen = $sth->fetchAll();

        $sth = $this->db->prepare("SELECT * FROM originalitaeten");
        $sth->execute();
        $data->originalitaeten = $sth->fetchAll();

        return $this->response->withJson($data);
    });

?>