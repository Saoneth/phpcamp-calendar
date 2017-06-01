<?php

require_once 'IdosellApi.php';

$api = new IdosellApi('3216','phpcamp5','qwerty321');

$reservation = array();
$reservation['dateFrom'] = "2017-06-03";
$reservation['dateTo'] = "2017-06-04";
$reservation['arrivalHour'] = "18:00";
$reservation['price'] = 1.0;
$reservation['clientId'] = 55;
$reservation['apiNote'] = "Błażej";
$reservation['clientNote'] = "Asddddsasdss";
$reservation['externalNote'] = "Asd";
$reservation['internalNote'] = "Asd";
$reservation['sendEmailNotifications'] = 3;
$reservation['status'] = 'confirmed';
$reservation['reservationApiSynchronizationFlag'] = 'none';
$reservation['packages'] = array();
$reservation['packages'][0] = array();
$reservation['packages'][0]['packageId'] = 4;
$reservation['packages'][0]['price'] = 5.0;
$reservation['items'] = array();
$reservation['items'][0] = array();
$reservation['items'][0]['objectItemId'] = 6;
$reservation['items'][0]['capacity'] = 7;
$reservation['items'][0]['price'] = 8.0;
$reservation['currencyId'] = 1;
$reservation['notify'] = true;

echo '<pre>';
var_dump($reservation);

$data = $api ->addReservation($reservation);
var_dump($data);
foreach ($data as $reservation) {
    //var_dump($reservation);
}

//$request = array();
//$request['authenticate'] = array();
//$request['authenticate']['systemKey'] = "qwerty123";
//$request['authenticate']['systemLogin'] = "phpcamp2";
//$request['authenticate']['lang'] = 'eng';
//$request['reservations'] = array();
//$request['reservations'][0] = array();
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//$request['reservations'][0]
//
//var_dump($request);
//$request_json = json_encode($request);
//$headers = array(
//    'Accept: application/json',
//    'Content-Type: application/json;charset=UTF-8'
//);
//
//$curl = curl_init($address);
//curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
//curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
//curl_setopt($curl, CURLOPT_HEADER, 1);
//curl_setopt($curl, CURLOPT_POST, 1);
//curl_setopt($curl, CURLOPT_POSTFIELDS, $request_json);
//curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//
//$response = curl_exec($curl);
//echo "<br><br>";
//var_dump($response);
//$status = curl_getinfo($curl);
//curl_close($curl);