<?php

$address = 'https://client3216.idosell.com/api/reservations/add/3/json';

$key = sha1(date('Ymd').sha1("qwerty321"));

$dateFrom = "2017-06-19";
$dateTo = "2017-06-21";
$arrivalHour = "10:00";
$price = 

$request = array();
$request['authenticate'] = array();
$request['authenticate']['systemKey'] = $key;
$request['authenticate']['systemLogin'] = "phpcamp5";
$request['authenticate']['lang'] = 'eng';
$request['reservations'] = array();
$request['reservations'][0] = array();
$request['reservations'][0]['dateFrom'] = $dateFrom;
$request['reservations'][0]['dateTo'] = $dateTo;
$request['reservations'][0]['arrivalHour'] = $arrivalHour;
$request['reservations'][0]['price'] = 1.0;
$request['reservations'][0]['clientId'] = 2;
$request['reservations'][0]['apiNote'] = "team5";
$request['reservations'][0]['clientNote'] = "team5";
$request['reservations'][0]['externalNote'] = "team5";
$request['reservations'][0]['internalNote'] = "team5";
$request['reservations'][0]['sendEmailNotifications'] = 3;
$request['reservations'][0]['status'] = 'unconfirmed';
$request['reservations'][0]['reservationApiSynchronizationFlag'] = 'none';
$request['reservations'][0]['packages'] = array();
$request['reservations'][0]['packages'][0] = array();
$request['reservations'][0]['packages'][0]['packageId'] = 4;
$request['reservations'][0]['packages'][0]['price'] = 5.0;
$request['reservations'][0]['items'] = array();
$request['reservations'][0]['items'][0] = array();
$request['reservations'][0]['items'][0]['objectItemId'] = 6;
$request['reservations'][0]['items'][0]['capacity'] = 7;
$request['reservations'][0]['items'][0]['price'] = 8.0;
$request['reservations'][0]['currencyId'] = 1;
$request['reservations'][0]['notify'] = true;

$request_json = json_encode($request);
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json;charset=UTF-8'
);

$curl = curl_init($address);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $request_json);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($curl);
$status = curl_getinfo($curl);
curl_close($curl);

// var_dump($status);
var_dump($response);