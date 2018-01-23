<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Offer Routes
$app->get('/offers', function (Request $request, Response $response, array $args) {
    $this->logger->info("Offers listing");

    $list=$this->offer->getAll();

    return $this->renderer->render($response, 'offers.phtml', [
      "offers"=> $list['result']
    ]);
});

$app->get('/offer/delete/[{id}]', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Delete offer #".$id);

    $result=$this->offer->delete($id);
    $list=$this->offer->getAll();

    return $this->renderer->render($response, 'offers.phtml', [
      "offers" => $list['result'],
      "message" => 'The offer #'.$id.' was sucessful deleted.'
    ]);
});

$app->get('/offer/confirm/[{id}]', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Confirm offer #".$id." deletion");

    $list=$this->offer->getAll();
    $offer=$this->offer->getById($id);

    return $this->renderer->render($response, 'offers.phtml', [
      "offers" => $list['result'],
      "delete_offer" => $offer['result']
    ]);
});

$app->get('/offer/edit/[{id}]', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Edit offer #".$id);

    $list=$this->offer->getAll();
    $offer=$this->offer->getById($id);

    return $this->renderer->render($response, 'offers.phtml', [
      "offers" => $list['result'],
      "offer" => $offer['result'],
      "action" => '/offer/save/'.$id
    ]);
});

$app->post('/offer/save/[{id}]', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Update offer #".$id);

    $data = json_encode(array(
      "id" => $_POST["id"],
      "name" => $_POST["name"],
      "discount" => $_POST["discount"]));

    $result=$this->offer->update($id, $data, true);
    $list=$this->offer->getAll();

    return $this->renderer->render($response, 'offers.phtml', [
      "offers"=> $list['result'],
      "message" => 'Offer #'.$id.' was updated'
    ]);
});

$app->post('/offer/create', function (Request $request, Response $response, array $args) {
    $this->logger->info("Creating new offer");

    $data = json_encode(array(
      "name" => $_POST["name"],
      "discount" => $_POST["discount"]
    ));

    $result=$this->offer->update($id, $data, false);
    $list=$this->offer->getAll();

    return $this->renderer->render($response, 'offers.phtml', [
      "offers"=> $list['result'],
      "message" => 'The new offer was created'
    ]);
});

$app->get('/offer/new', function (Request $request, Response $response, array $args) {
    $this->logger->info("Editing new offer");

    $list=$this->offer->getAll();

    return $this->renderer->render($response, 'offers.phtml', [
      "offers"=> $list['result'],
      "action" => '/offer/create',
      "offer" => [
        'name' => '',
        'discount' => 0
      ]
    ]);
});
