<?php

namespace Tests\Functional;

// API Test for Offer

class ApiOfferTest extends BaseTestCase
{

  public function setUp()
  {
    // Prepare database to clean test
    $this->db->exec("DELETE FROM offers");
    $this->db->exec("ALTER TABLE offers AUTO_INCREMENT = 1");
    $this->db->exec("INSERT INTO offers (name, discount) VALUES ('A Black Friday HUGE discount', 70)");
    $this->db->exec("INSERT INTO offers (name, discount) VALUES ('A very personal and minimal discount', 3)");

  }

    /**
     * CRUD Test: CREATE
     */
    public function testCreateOffer()
    {
        $object = [
          name => "A Sample Offer (13%)",
          discount => 13
        ];
        $response = $this->runApp('POST', '/api/offer', $object);

        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('The new offer was sucessfuly created', $body['message']);
        $this->assertEquals('offer:created', $body['result']);
    }

    /**
     * CRUD Test: READ multiple objects
     */
    public function testReadOffers()
    {

        $response = $this->runApp('GET', '/api/offers');

        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Listing 2 offers', $body['message']);
        $this->assertArrayHasKey(1, $body['result']);
    }

    /**
     * CRUD Test: READ single object
     */
    public function testReadOffer()
    {
      $response = $this->runApp('GET', '/api/offer/1');

      $body = json_decode((string) $response->getBody(), true);

      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('Offer found', $body['message']);
      $this->assertArrayHasKey('name', $body['result']);
    }

    /**
     * CRUD Test: UPDATE
     */
    public function testUpdateOffer()
    {
      $object = [
        name => "Another Daily Crazy Discount!",
        discount => 87
      ];
      $response = $this->runApp('PUT', '/api/offer/2', $object);

      $body = json_decode((string) $response->getBody(), true);

      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('The new offer was sucessfuly updated', $body['message']);
      $this->assertEquals('offer:updated', $body['result']);
    }

    /**
     * CRUD Test: DELETE
     */
    public function testDeleteOffer()
    {
      $response = $this->runApp('DELETE', '/api/offer/1');

      $body = json_decode((string) $response->getBody(), true);

      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('The new offer was sucessfuly deleted', $body['message']);
      $this->assertEquals('offer:deleted', $body['result']);
    }
}
