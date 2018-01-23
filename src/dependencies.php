<?php

// DIC configuration
$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// PDO
$container['db'] = function ($c) {
    $db = $c->get('settings')['db'];
    $connectstring = 'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'];
    $pdo = new PDO($connectstring, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['db'] = function ($c) {
    $db = $c->get('settings')['db'];
    $connectstring = 'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'];
    $pdo = new PDO($connectstring, $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

$container['offer'] = function ($c) {
    return new Offer($c);
};

$container['recipient'] = function ($c) {
    return new Recipient($c);
};

$container['voucher'] = function ($c) {
    return new Voucher($c);
};
