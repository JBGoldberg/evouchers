<?php

namespace Tests\Functional;

// API Test for Voucher

class ApiVoucherTest extends BaseTestCase
{
    public function setUp()
    {
        // Prepare database to clean test
        $this->db->exec("DELETE FROM vouchers");
        $this->db->exec("ALTER TABLE vouchers AUTO_INCREMENT = 1");

        $this->db->exec("DELETE FROM recipients");
        $this->db->exec("ALTER TABLE recipients AUTO_INCREMENT = 1");

        $this->db->exec("DELETE FROM offers");
        $this->db->exec("ALTER TABLE offers AUTO_INCREMENT = 1");

        $this->db->exec("INSERT INTO offers (name, discount) VALUES ('A very personal and minimal discount', 3)");

        $this->db->exec("INSERT INTO recipients (name, email) VALUES ('John Doe', 'john@gmail.com')");

        $this->db->exec("INSERT INTO vouchers (code, recipient_id, offer_id, expiration_date) VALUES ('123456789012', 1, 1, '2017-03-30')");

        $this->db->exec("INSERT INTO vouchers (code, recipient_id, offer_id, expiration_date) VALUES ('abcdefghijkl', 1, 1, '2020-04-19')");
    }

    /**
     * Validate Voucher and set de usage date
     */
    public function testUseVoucher()
    {
        $object =[];
        $response = $this->runApp('GET', '/api/voucher/use/abcdefghijkl/john@gmail.com', $object);

        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('The new voucher was sucessfuly used', $body['message']);
        $this->assertEquals('voucher:used', $body['result']);
        $this->assertArrayHasKey('discount', $body);
    }


    /**
     * CRUD Test: CREATE
     */
    public function testCreateVoucher()
    {
        $object = [
          code => 'dfgdfg',
          offer_id => 200,
          recipient_id => 300,
          expiration_date => '2020-06-29'
        ];
        $response = $this->runApp('POST', '/api/voucher', $object);

        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('The new voucher was sucessfuly created', $body['message']);
        $this->assertEquals('voucher:created', $body['result']);
        $this->assertArrayHasKey('code', $body);
    }

    /**
     * CRUD Test: READ multiple objects
     */
    public function testReadVouchers()
    {

        $response = $this->runApp('GET', '/api/vouchers');

        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Listing 2 vouchers', $body['message']);
        $this->assertArrayHasKey(1, $body['result']);
    }

    /**
     * CRUD Test: READ single object
     */
    public function testReadVoucher()
    {
      $response = $this->runApp('GET', '/api/voucher/1');

      $body = json_decode((string) $response->getBody(), true);

      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('Voucher found', $body['message']);
      $this->assertArrayHasKey('code', $body['result']);
    }

    /**
     * CRUD Test: UPDATE
     */
    public function testUpdateVoucher()
    {
      $object = [
        offer_id => 202,
        recipient_id => 303,
        expiration_date => '2029-10-14'
      ];
      $response = $this->runApp('PUT', '/api/voucher/2', $object);

      $body = json_decode((string) $response->getBody(), true);

      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('The new voucher was sucessfuly updated', $body['message']);
      $this->assertEquals('voucher:updated', $body['result']);
    }

    /**
     * CRUD Test: DELETE
     */
    public function testDeleteVoucher()
    {
      // $this->markTestSkipped('Not for now');
      $response = $this->runApp('DELETE', '/api/voucher/1');

      $body = json_decode((string) $response->getBody(), true);

      $this->assertEquals(200, $response->getStatusCode());
      $this->assertEquals('The new voucher was sucessfuly deleted', $body['message']);
      $this->assertEquals('voucher:deleted', $body['result']);
    }
}
