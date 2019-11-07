<?php

    $app->get('/v1/debug', function ($request, $response, $args) {

        $data = [];
        $data['request'] = true;

        return $this->response->withJson($data);
    });

    /*******************************************************************************
    *** POST - bestellungen - Neue Bestellung
    *******************************************************************************/

    $app->post('/v1/debug', function ($request, $response, $args) use ($app) {

        $data = [];
        $data['test'] = $request->getParam('test');
        $data['tische_id'] = $request->getParam('tische_id');
        $data['timestamp_begonnen'] = $request->getParam('timestamp_begonnen');
        $data['aufnehmer_id'] = $request->getParam('aufnehmer_id');
        $data['geraete_id'] = $request->getParam('geraete_id');
        $data['positionen'] = $request->getParam('positionen');

        $data['request'] = true;

        return $this->response->withJson($data);
    });

?>