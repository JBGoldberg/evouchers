<?php

use Slim\Http\Request;
use Slim\Http\Response;

// API Handlers for Recipient

// Create
$app->post('/api/recipient', function (Request $request, Response $response, array $args) {
    $this->logger->info("Creating new recipient");
    $body = $request->getParsedBody();

    try {
        $stmt = $this->db->prepare("INSERT INTO recipients (name, email) VALUES (:name, :email)");
        $stmt->bindParam(':name', $body['name']);
        $stmt->bindParam(':email', $body['email']);

        $stmt->execute();
    } catch (PDOException $Exception) {
        $result = [
        message => $Exception->getMessage(),
        result => 'recipient:error'
      ];
        return $response->withJson($result, 500);
    }

    $result = [
      message => 'The new recipient was sucessfuly created',
      result => 'recipient:created'
    ];

    return $response->withJson($result, 201);
});

// Read (Multiple)
$app->get('/api/recipients', function (Request $request, Response $response, array $args) {
    $this->logger->info("Retreiving all recipients");

    try {
        $stmt = $this->db->prepare("SELECT * FROM recipients");
        $stmt->execute();
    } catch (PDOException $Exception) {
        $result = [
      message => $Exception->getMessage(),
      result => 'recipient:error'
    ];
        return $response->withJson($result, 500);
    }

    $result = [
      result => $stmt->fetchAll(),
      message => "Listing ".$stmt->rowCount()." recipients"
    ];
    return $response->withJson($result, 200);
});

// Read (Single)
$app->get('/api/recipient/[{id}]', function (Request $request, Response $response, array $args) {
    $id =(int) $args['id'];
    $this->logger->info("Retreiving recipient #".$id);

    try {

        $stmt = $this->db->prepare("SELECT * FROM recipients WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

    } catch (PDOException $Exception) {
        $result = [
      message => $Exception->getMessage(),
      result => 'recipient:error'
    ];
        return $response->withJson($result, 500);
    }

    $object = $stmt->fetch();
    $result = [
      result => ($object ?  $object : null),
      message => ($object ?  "Recipient found" : "No recipient found")
    ];
    return $response->withJson($result, 200);
});

// Update
$app->put('/api/recipient/[{id}]', function (Request $request, Response $response, array $args) {
    $id = (int) $args['id'];
    $this->logger->info("Updating recipient #".$id);
    $body = $request->getParsedBody();

    try {
        $stmt = $this->db->prepare("UPDATE recipients SET name=:name, email=:email WHERE id = :id");

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $body['name']);
        $stmt->bindParam(':email', $body['email']);

        $stmt->execute();

    } catch (PDOException $Exception) {
        $result = [
        message => $Exception->getMessage(),
        result => 'recipient:error'
      ];
        return $response->withJson($result, 500);
    }

    $result = [
      message => 'The new recipient was sucessfuly updated',
      result => 'recipient:updated'
    ];
    return $response->withJson($result, 200);
});

// Delete
$app->delete('/api/recipient/[{id}]', function (Request $request, Response $response, array $args) {
    $id = (int) $args['id'];
    $this->logger->info("Deleting recipient #".$id);

    try {
        $stmt = $this->db->prepare("DELETE FROM recipients WHERE id = :id");
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    } catch (PDOException $Exception) {
        $result = [
      message => $Exception->getMessage(),
      result => 'recipient:error'
    ];
        return $response->withJson($result, 500);
    }

    $result = [
      message => 'The new recipient was sucessfuly deleted',
      result => 'recipient:deleted'
    ];
    return $response->withJson($result, 200);
});
