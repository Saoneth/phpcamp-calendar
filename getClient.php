<?php

require_once 'phpcamp-calendar/IdosellApi.php';
$api = new IdosellApi('3216','phpcamp5','qwerty321');

$address = 'http://client3216.idosell.com/api/clients/get/0/json';

$data = $api ->getClientByEmail('abcwe@wp.pl');
foreach ($data as $client) {
    var_dump($client);
}