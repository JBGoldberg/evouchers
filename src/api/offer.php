<?php

use Slim\Http\Request;
use Slim\Http\Response;

// API Handlers for Offer

// Create
$app->post('/api/offer', function (Request $request, Response $response, array $args) {
    $this->logger->info("Creating new offer");
    $body = $request->getParsedBody();

    // Simple validation to database
    // Use a ORM tool or validation in database is too overkill for this case
    // Even a separated function is too much
    // I decided to ignore DRY and I duplicate it in CREATE and UPDATE
    $discount = intval($body['discount']);
    if ($discount <0 ||  $discount > 100) {
        $result = [
          message => 'Discount must be a percentage',
          result => 'offer:error'
        ];
        return $response->withJson($result, 406);
    }

    try {
        $stmt = $this->db->prepare("INSERT INTO offers (name, discount) VALUES (:name, :discount)");
        $stmt->bindParam(':name', $body['name']);
        $stmt->bindParam(':discount', $discount);

        $stmt->execute();
    } catch (PDOException $Exception) {
        $result = [
          message => $Exception->getMessage(),
          result => 'offer:error'
      ];
        return $response->withJson($result, 500);
    }

    $result = [
      message => 'The new offer was sucessfuly created',
      result => 'offer:created'
    ];
    return $response->withJson($result, 201);
});

// Read (Multiple)
$app->get('/api/offers', function (Request $request, Response $response, array $args) {
    $this->logger->info("Retreiving all offers");

    try {
        $stmt = $this->db->prepare("SELECT * FROM offers");
        $stmt->execute();
    } catch (PDOException $Exception) {
        $result = [
      message => $Exception->getMessage(),
      result => 'offer:error'
    ];
        return $response->withJson($result, 500);
    }

    $result = [
      result => $stmt->fetchAll(),
      message => "Listing ".$stmt->rowCount()." offers"
    ];
    return $response->withJson($result, 200);
});

// Read (Single)
$app->get('/api/offer/[{id}]', function (Request $request, Response $response, array $args) {
    $id =(int) $args['id'];
    $this->logger->info("Retreiving offer #".$id);

    try {
        $stmt = $this->db->prepare("SELECT * FROM offers WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $object = $stmt->fetch();
    } catch (PDOException $Exception) {
        $result = [
      message => $Exception->getMessage(),
      result => 'offer:error'
    ];
        return $response->withJson($result, 500);
    }
    $result = [
      result => ($object ?  $object : null),
      message => ($object ?  "Offer found" : "No offer found")
    ];
    return $response->withJson($result, 200);
});

// Update
$app->put('/api/offer/[{id}]', function (Request $request, Response $response, array $args) {
    $id = (int) $args['id'];
    $this->logger->info("Updating offer #".$id);
    $body = $request->getParsedBody();

    // Simple validation to database
    // Use a ORM tool or validation in database is too overkill for this case
    // Even a separated function is too much
    // I decided to ignore DRY and I duplicate it in CREATE and UPDATE
    $discount = intval($body['discount']);
    if ($discount <0 ||  $discount > 100) {
        $result = [
          message => 'Discount must be a percentage',
          result => 'offer:error'
        ];
        return $response->withJson($result, 406);
    }

    try {

        $stmt = $this->db->prepare("UPDATE offers SET name=:name, discount=:discount WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $body['name']);
        $stmt->bindParam(':discount', $discount);
        $stmt->execute();

    } catch (PDOException $Exception) {
        $result = [
      message => $Exception->getMessage(),
      result => 'offer:error',
    ];
        return $response->withJson($result, 500);
    }
    $result = [
      message => 'The new offer was sucessfuly updated',
      result => 'offer:updated'
    ];
    return $response->withJson($result, 200);
});

// Delete
$app->delete('/api/offer/[{id}]', function (Request $request, Response $response, array $args) {
    $id = (int) $args['id'];
    $this->logger->info("Deleting offer #".$id);

    try {
        $stmt = $this->db->prepare("DELETE FROM offers WHERE id = :id");
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    } catch (PDOException $Exception) {
        $result = [
      message => $Exception->getMessage(),
      result => 'offer:error'
    ];
        return $response->withJson($result, 500);
    }

    $result = [
      message => 'The new offer was sucessfuly deleted',
      result => 'offer:deleted'
    ];
    return $response->withJson($result, 200);
});
