<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/recipients', function (Request $request, Response $response, array $args) {
    $this->logger->info("Recipients listing");

    $list=$this->recipient->getAll();

    return $this->renderer->render($response, 'recipients.phtml', [
      "recipients"=> $list['result']
    ]);
});

$app->get('/recipient/delete/[{id}]', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Delete recipient #".$id);

    $result=$this->recipient->delete($id);
    $list=$this->recipient->getAll();

    return $this->renderer->render($response, 'recipients.phtml', [
      "recipients" => $list['result'],
      "message" => 'The recipient #'.$id.' was sucessful deleted.'
    ]);
});

$app->get('/recipient/confirm/[{id}]', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Confirm recipient #".$id." deletion");

    $list=$this->recipient->getAll();
    $recipient=$this->recipient->getById($id);

    return $this->renderer->render($response, 'recipients.phtml', [
      "recipients" => $list['result'],
      "delete_recipient" => $recipient['result']
    ]);
});

$app->get('/recipient/edit/[{id}]', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Edit recipient #".$id);

    $list=$this->recipient->getAll();
    $recipient=$this->recipient->getById($id);

    return $this->renderer->render($response, 'recipients.phtml', [
      "recipients" => $list['result'],
      "recipient" => $recipient['result'],
      "action" => '/recipient/save/'.$id
    ]);
});

$app->post('/recipient/save/[{id}]', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $this->logger->info("Update recipient #".$id);

    $data = json_encode(array(
      "id" => $_POST["id"],
      "name" => $_POST["name"],
      "email" => $_POST["email"]));

    $result=$this->recipient->update($id, $data,true);
    $list=$this->recipient->getAll();

    return $this->renderer->render($response, 'recipients.phtml', [
      "recipients"=> $list['result'],
      "message" => 'Recipient #'.$id.' was updated'
    ]);
});

$app->post('/recipient/create', function (Request $request, Response $response, array $args) {
    $this->logger->info("Creating new recipient");

    $data = json_encode(array("name" => $_POST["name"], "email" => $_POST["email"]));

    $ch = curl_init($this->settings['main_url'].'/api/recipient');
    curl_setopt_array($ch, array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_TIMEOUT => $this->settings['api_request_timeout'],
      CURLOPT_HTTPHEADER =>  array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($data),
      ),
      CURLOPT_CUSTOMREQUEST => 'POST'
    ));

    if (! $output = curl_exec($ch)) {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);

    $result=json_decode($output, true);

    $list=$this->recipient->getAll();

    return $this->renderer->render($response, 'recipients.phtml', [
      "recipients"=> $list['result'],
      "message" => 'The new recipient was created'
    ]);
});

$app->get('/recipient/new', function (Request $request, Response $response, array $args) {
    $this->logger->info("Editing new recipient");

    $list=$this->recipient->getAll();

    return $this->renderer->render($response, 'recipients.phtml', [
      "recipients"=> $list['result'],
      "action" => '/recipient/create',
      "recipient" => [
        'name' => '',
        'email' => ''
      ]
    ]);
});
