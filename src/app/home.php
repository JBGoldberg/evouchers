<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Home access");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
