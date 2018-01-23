<?php

class Offer {

  public $container;

  function __construct($container) {
    $this->container = $container;
  }

  // Retreive single offer
  public function getById($id) {
   $ch = curl_init();
   curl_setopt_array($ch, array(
     CURLOPT_RETURNTRANSFER => true,
     CURLOPT_POST => false,
     CURLOPT_TIMEOUT => $this->container->settings['api_request_timeout'],
     CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
     CURLOPT_URL => $this->container->settings['main_url'].'/api/offer/'.$id,
     CURLOPT_CUSTOMREQUEST => 'GET'
   ));

   if (! $output = curl_exec($ch)) {
       trigger_error(curl_error($ch));
   }
   curl_close($ch);
    return json_decode($output, true);
  }

  // Retreive all offers
  function getAll() {
   $ch = curl_init();
   curl_setopt_array($ch, array(
     CURLOPT_RETURNTRANSFER => true,
     CURLOPT_POST => false,
     CURLOPT_TIMEOUT => $this->container->settings['api_request_timeout'],
     CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
     CURLOPT_URL => $this->container->settings['main_url'].'/api/offers',
     CURLOPT_CUSTOMREQUEST => 'GET'
   ));

   if (! $output = curl_exec($ch)) {
       trigger_error(curl_error($ch));
   }
   curl_close($ch);
   return json_decode($output, true);
  }

  // Delete single offer
  function delete($id) {

   $ch = curl_init($this->container->settings['main_url'].'/api/offer/'.$id);
   curl_setopt_array($ch, array(
     CURLOPT_RETURNTRANSFER => true,
     CURLOPT_POST => false,
     CURLOPT_TIMEOUT => $this->container->settings['api_request_timeout'],
     CURLOPT_CUSTOMREQUEST => 'DELETE'
   ));

   if (! $output = curl_exec($ch)) {
       trigger_error(curl_error($ch));
   }
   curl_close($ch);
   return json_decode($output, true);

  }

  // Create or Update offer
  function update($id, $data, $update) {

   if ($update) {
     $ch = curl_init($this->container->settings['main_url'].'/api/offer/'.$id);
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
   } else {
     $ch = curl_init($this->container->settings['main_url'].'/api/offer');
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
   }

   curl_setopt_array($ch, array(
     CURLOPT_RETURNTRANSFER => true,
     CURLOPT_POST => !$update,
     CURLOPT_POSTFIELDS => $data,
     CURLOPT_TIMEOUT => $this->container->settings['api_request_timeout'],
     CURLOPT_HTTPHEADER =>  array(
         'Content-Type: application/json',
         'Content-Length: ' . strlen($data)
       )
     ));

   if (! $output = curl_exec($ch)) {
       trigger_error(curl_error($ch));
   }
   curl_close($ch);

   return json_decode($output, true);

  }

}

?>
