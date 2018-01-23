<?php

namespace Tests\Functional;

// API Test for Recipient

class ApiRecipientTest extends BaseTestCase
{

  public function setUp()
  {
    // Prepare database to clean test
    $this->db->exec("DELETE FROM recipients");
    $this->db->exec("ALTER TABLE recipients AUTO_INCREMENT = 1");
    $this->db->exec("INSERT INTO recipients (name, email) VALUES ('John Doe', 'john@gmail.com')");
    $this->db->exec("INSERT INTO recipients (name, email) VALUES ('Jane Doe', 'jane@gmail.com')");

  }

    /**
     * CRUD Test: CREATE
     */
    public function testCreateRecipient()
    {
        $object = [
          name => "Steve Wozniak",
          email => "wozniak@apple.com"
        ];
        $response = $this->runApp('POST', '/api/recipient', $object);

        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('The new recipient was sucessfuly created', $body['message']);
        $this->assertEquals('recipient:created', $body['result']);
    }

    /**
     * CRUD Test: READ multiple objects
     */
    public function testReadRecipients()
    {

        $response = $this->runApp('GET', '/api/recipients');

        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Listing 2 recipients', $body['message']);
        $this->assertArrayHasKey(1, $body['result']);
    }

    /**
     * CRUD Test: READ single object
     */
    public function testReadRecipient()
    {
      $response = $this->runApp('GET', '/api/recipient/1');

      $body = json_decode((string) $response->getBody(), true);

      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('Recipient found', $body['message']);
      $this->assertArrayHasKey('name', $body['result']);
    }

    /**
     * CRUD Test: UPDATE
     */
    public function testUpdateRecipient()
    {
      $object = [
        name => "Another Daily Crazy Discount!",
        email => 87
      ];
      $response = $this->runApp('PUT', '/api/recipient/2', $object);

      $body = json_decode((string) $response->getBody(), true);

      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('The new recipient was sucessfuly updated', $body['message']);
      $this->assertEquals('recipient:updated', $body['result']);
    }

    /**
     * CRUD Test: DELETE
     */
    public function testDeleteRecipient()
    {
      $response = $this->runApp('DELETE', '/api/recipient/1');

      $body = json_decode((string) $response->getBody(), true);

      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('The new recipient was sucessfuly deleted', $body['message']);
      $this->assertEquals('recipient:deleted', $body['result']);
    }
}
