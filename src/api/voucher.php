<?php

use Slim\Http\Request;
use Slim\Http\Response;

// API Handlers for Voucher

// Code Genetator
$app->patch('/api/voucher/code', function (Request $request, Response $response, array $args) {
    $id =(int) $args['id'];
    $this->logger->info("Generate new voucher code ");

    try {
        $looking = true;

        while ($looking) {
          $code = substr(md5(microtime()), 3, 12);

          $stmt = $this->db->prepare("SELECT * FROM vouchers WHERE code = :code");
          $stmt->bindParam(':code', $code);
          $stmt->execute();

          $found = $stmt->fetch();
          if (!$found) {
            $looking=false;
          }
        }

    } catch (PDOException $Exception) {
        $result = [
        message => $Exception->getMessage(),
        result => 'voucher:error'
      ];
        return $response->withJson($result, 500);
    }

    $result = [
      message => 'An avaliable code was sucessfuly found',
      result => 'code:found',
      code => $code
    ];
    return $response->withJson($result, 200);
});

// Create
$app->post('/api/voucher', function (Request $request, Response $response, array $args) {
    $this->logger->info("Creating new voucher");
    $body = $request->getParsedBody();

    try {

        $stmt = $this->db->prepare("INSERT INTO vouchers (code, recipient_id, offer_id, expiration_date) VALUES (:code, :recipient_id, :offer_id, :expiration_date)");
        $stmt->bindParam(':code',  $body['code']);
        $stmt->bindParam(':recipient_id', $body['recipient_id']);
        $stmt->bindParam(':offer_id', $body['offer_id']);
        $stmt->bindParam(':expiration_date', $body['expiration_date']);

        $stmt->execute();
    } catch (PDOException $Exception) {
        $result = [
        message => $Exception->getMessage(),
        result => 'voucher:error'
      ];
        return $response->withJson($result, 500);
    }

    $result = [
      message => 'The new voucher was sucessfuly created',
      result => 'voucher:created',
      code => $code
    ];

    return $response->withJson($result, 201);
});

// Read (Multiple)
$app->get('/api/vouchers', function (Request $request, Response $response, array $args) {
    $this->logger->info("Retreiving all vouchers");

    try {
        $stmt = $this->db->prepare("SELECT vouchers.id,vouchers.code,vouchers.used_on_date,vouchers.recipient_id,vouchers.offer_id,offers.discount,offers.name as offer,recipients.name as recipient, recipients.email FROM vouchers JOIN recipients ON vouchers.recipient_id = recipients.id JOIN offers ON vouchers.offer_id = offers.id");

        $stmt->execute();
    } catch (PDOException $Exception) {
        $result = [
      message => $Exception->getMessage(),
      result => 'voucher:error'
    ];
        return $response->withJson($result, 500);
    }

    $result = [
      result => $stmt->fetchAll(),
      message => "Listing ".$stmt->rowCount()." vouchers"
    ];
    return $response->withJson($result, 200);
});

// Read (Single)
$app->get('/api/voucher/{id}', function (Request $request, Response $response, array $args) {
    $id =(int) $args['id'];
    $this->logger->info("Retreiving voucher #".$id);

    try {
        $stmt = $this->db->prepare("SELECT * FROM vouchers WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    } catch (PDOException $Exception) {
        $result = [
      message => $Exception->getMessage(),
      result => 'voucher:error'
    ];
        return $response->withJson($result, 500);
    }

    $object = $stmt->fetch();
    $result = [
      result => ($object ?  $object : null),
      message => ($object ?  "Voucher found" : "No voucher found")
    ];
    return $response->withJson($result, 200);
});

// Update
$app->put('/api/voucher/{id}', function (Request $request, Response $response, array $args) {
    $id = (int) $args['id'];
    $this->logger->info("Updating voucher #".$id);
    $body = $request->getParsedBody();

    try {
        $stmt = $this->db->prepare("UPDATE vouchers SET recipient_id=:recipient_id, offer_id=:offer_id, expiration_date=:expiration_date WHERE id = :id");

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':recipient_id', $body['recipient_id']);
        $stmt->bindParam(':offer_id', $body['offer_id']);
        $stmt->bindParam(':expiration_date', $body['expiration_date']);

        $stmt->execute();

    } catch (PDOException $Exception) {
        $result = [
        message => $Exception->getMessage(),
        result => 'voucher:error'
      ];
        return $response->withJson($result, 500);
    }

    $result = [
      message => 'The new voucher was sucessfuly updated',
      result => 'voucher:updated'
    ];
    return $response->withJson($result, 200);
});

// Use voucher
$app->get('/api/voucher/use/{code}/{email}', function (Request $request, Response $response, array $args) {

    $code = $args['code'];
    $email = $args['email'];
    $this->logger->info("Using voucher code ".$code);

    try {

      $stmt = $this->db->prepare("select vouchers.id as id,vouchers.code,recipients.email, offers.discount from vouchers join recipients join offers WHERE code = :code AND expiration_date > CURDATE() AND used_on_date IS NULL AND recipient_id = recipients.id AND email = :email");
      $stmt->bindParam(':code', $code);
      $stmt->bindParam(':email', $email);
      $stmt->execute();

     $found = $stmt->fetch();

     if (!isset($found['id'])) {
       $result = [
         message => 'This is not a valid voucher',
         result => 'voucher:not-avaliable'
       ];
       return $response->withJson($result, 200);
     }

        $stmt = $this->db->prepare("UPDATE vouchers SET used_on_date= CURDATE() WHERE id = :id");
        $stmt->bindParam(':id', $found['id']);

        $stmt->execute();

    } catch (PDOException $Exception) {
        $result = [
        message => $Exception->getMessage(),
        result => 'voucher:error'
      ];
        return $response->withJson($result, 500);
    }

    $result = [
      message => 'The new voucher was sucessfuly used',
      result => 'voucher:used',
      discount => (int) $found['discount']
    ];
    return $response->withJson($result, 200);
});

// Delete
$app->delete('/api/voucher/[{id}]', function (Request $request, Response $response, array $args) {
    $id = (int) $args['id'];
    $this->logger->info("Deleting voucher #".$id);

    try {
        $stmt = $this->db->prepare("DELETE FROM vouchers WHERE id = :id");
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    } catch (PDOException $Exception) {
        $result = [
      message => $Exception->getMessage(),
      result => 'voucher:error'
    ];
        return $response->withJson($result, 500);
    }

    $result = [
      message => 'The new voucher was sucessfuly deleted',
      result => 'voucher:deleted'
    ];
    return $response->withJson($result, 200);
});
