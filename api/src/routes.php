<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Middleware;

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Routes

require __DIR__ . '/endpoints/debug/debug.php';

require __DIR__ . '/endpoints/kategorien/kategorien.php';
require __DIR__ . '/endpoints/eintraege/items.php';
require __DIR__ . '/endpoints/eintraege/eintraege.php';
require __DIR__ . '/endpoints/eintraege/suche.php';
require __DIR__ . '/endpoints/eintraege/etiketten.php';
require __DIR__ . '/endpoints/eintraege/verschieben.php';
require __DIR__ . '/endpoints/eintraege/findbuch.php';
require __DIR__ . '/endpoints/stats/stats.php';