<?php

require_once 'phpcamp-calendar/IdosellApi.php';
$api = new IdosellApi('3216','phpcamp5','qwerty321');

$address = 'http://client3216.idosell.com/api/clients/add/0/json';

$client=array();
$client['clientType'] = 'person';
$client['firstName'] = "firstName";
$client['lastName'] = "lastName";
$client['countryCode'] = "pl";
$client['phone'] = "+48.222309902";
$client['email'] = "abcwe@wp.pl";
$client['language'] = "pol";
$client['currency'] = "PLN";
$client['guests'] = array();
$client['guests'][0] = array();
$client['guests'][0]['firstName'] = "firstName";
$client['guests'][0]['lastName'] = "lastName";
$client['guests'][0]['countryCode'] = "pl";
$client['guests'][0]['phone'] = "+48.914436660";
$client['guests'][0]['email'] = "abcwe@wp.pl";
$client['guests'][0]['language'] = "pol";
$client['notification'] = 1;
$client['sendNewsletter'] = 2;


$data = $api ->addClient($client);
foreach ($data as $client) {
    var_dump($client);
}