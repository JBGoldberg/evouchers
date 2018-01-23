<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/vouchers', function (Request $request, Response $response, array $args) {
    $this->logger->info("Vouchers listing");

    $list=$this->voucher->getAll();
    $recipients=$this->recipient->getAll();
    $offers=$this->offer->getAll();

    return $this->renderer->render($response, 'vouchers.phtml', [
      "vouchers"=> $list['result'],
      "recipients"=> $recipients['result'],
      "offers"=> $offers['result']
    ]);
});

$app->get('/voucher/delete/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Delete voucher #".$id);

    $result=$this->voucher->delete($id);
    $list=$this->voucher->getAll();

    return $this->renderer->render($response, 'vouchers.phtml', [
      "vouchers" => $list['result'],
      "message" => 'The voucher #'.$id.' was sucessful deleted.'
    ]);
});

$app->get('/voucher/use/{code}/{email}', function (Request $request, Response $response, array $args) {
    $code = $args['code'];
    $email = $args['email'];
    $this->logger->info("Using voucher #".$code);

    $result=$this->voucher->use($code, $email);
    $list=$this->voucher->getAll();

    if(!isset($result['discount'])) {
      return $this->renderer->render($response, 'vouchers.phtml', [
        "vouchers" => $list['result'],
        "error" => 'Your code is not valid.'
      ]);
    }

    return $this->renderer->render($response, 'vouchers.phtml', [
      "vouchers" => $list['result'],
      "message" => 'The voucher code '.$code.' was used. The discount value is '.$result['discount'].'%'
    ]);
});

$app->get('/voucher/confirm/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Confirm voucher #".$id." deletion");

    $list=$this->voucher->getAll();
    $voucher=$this->voucher->getById($id);

    return $this->renderer->render($response, 'vouchers.phtml', [
      "vouchers" => $list['result'],
      "delete_voucher" => $voucher['result']
    ]);
});

$app->get('/voucher/edit/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Edit voucher #".$id);

    $list=$this->voucher->getAll();
    $voucher=$this->voucher->getById($id);
    $recipients=$this->recipient->getAll();
    $offers=$this->offer->getAll();

    return $this->renderer->render($response, 'vouchers.phtml', [
      "vouchers" => $list['result'],
      "voucher" => $voucher['result'],
      "action" => '/voucher/save/'.$id,
      "recipients"=> $recipients['result'],
      "offers"=> $offers['result']
    ]);
});

$app->post('/voucher/save/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Update voucher #".$id);

    $data = json_encode(array(
      "id" => $_POST["id"],
      "recipient_id" => $_POST["recipient_id"],
      "offer_id" => $_POST["offer_id"],
      "code" => $_POST["code"],
      "expiration_date" => $_POST["expiration_date"]
    ));

    $result=$this->voucher->update($id, $data, true);

    $list=$this->voucher->getAll();

    return $this->renderer->render($response, 'vouchers.phtml', [
      "vouchers"=> $list['result'],
      "message" => 'Voucher #'.$id.' was updated'
    ]);
});

$app->post('/voucher/create', function (Request $request, Response $response, array $args) {
    $this->logger->info("Creating new voucher");

    $data = json_encode(array(
      "recipient_id" => $_POST["recipient_id"],
      "code" => $_POST["code"],
      "offer_id" => $_POST["offer_id"],
      "expiration_date" => $_POST["expiration_date"]
    ));
    $result=$this->voucher->update($id, $data, false);

    $list=$this->voucher->getAll();

    return $this->renderer->render($response, 'vouchers.phtml', [
      "vouchers"=> $list['result'],
      "message" => 'The new voucher was created'
    ]);
});

$app->get('/voucher/new', function (Request $request, Response $response, array $args) {
    $this->logger->info("Editing new voucher");

    $list=$this->voucher->getAll();
    $code=$this->voucher->generateCode();
    $recipients=$this->recipient->getAll();
    $offers=$this->offer->getAll();

    return $this->renderer->render($response, 'vouchers.phtml', [
      "vouchers"=> $list['result'],
      "recipients"=> $recipients['result'],
      "offers"=> $offers['result'],
      "action" => '/voucher/create',
      "voucher" => [
        'code' => $code['code']
      ]
    ]);
});
