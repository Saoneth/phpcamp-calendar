<?php

$address = 'https://client3216.idosell.com/api/reservations/get/0/json';

$request = array();
$request['authenticate'] = array();
$request['authenticate']['systemKey'] = sha1(date("Ymd").sha1("qwerty321"));
$request['authenticate']['systemLogin'] = "phpcamp5";
$request['authenticate']['lang'] = 'eng';
$request['result'] = array();
$request['result']['page'] = 1;
$request['result']['number'] = 2;

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
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $request_json);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($curl);
$status = curl_getinfo($curl);

curl_close($curl);

$data = json_decode($response, true);

foreach ($data['result']["reservations"] as $rezerwacja){
    var_dump($rezerwacja["items"]);

    echo "</br></br>";

    $data_poczatkowa = $rezerwacja["reservationDetails"]["dateFrom"];
    $data_koncowa = $rezerwacja["reservationDetails"]["dateTo"];

    $opis_pokoju = array(); // np Pokoj jednosobowy 200

    for($i=0;$i<count($rezerwacja["items"]);$i++){
        $nazwa_pokoju = $rezerwacja["items"][$i]["objectName"];
        $numer_pokoju = $rezerwacja["items"][$i]["itemCode"];
        $cena_za_pokoj = $rezerwacja["items"][$i]["prices"][0]["price"]; //pierwsza cena

        $opis_pokoju[$i] = $nazwa_pokoju.'//'.$numer_pokoju.'//'.$cena_za_pokoj;
    }

    $imie_rezerwujacego = $rezerwacja['client']['firstName'];
    $nazwisko_rezerwujacego = $rezerwacja['client']['lastName'];
    $telefon_rezerwujacego = $rezerwacja['client']['phone'];

    // dane do przekazania
        $status_rezerwacji = $rezerwacja["reservationDetails"]["status"];
        $data_poczatkowa = $rezerwacja["reservationDetails"]["dateFrom"];
        $data_koncowa = $rezerwacja["reservationDetails"]["dateTo"];
        $summary = $imie_rezerwujacego.' / '.$nazwisko_rezerwujacego.' / '.$telefon_rezerwujacego;

        // zamiana tablicy z opisami pokojow na jednego stringa
        $opis = '';

        foreach ($opis_pokoju as $p_pokoj){
            $opis .= $p_pokoj;
        }

        $location = $opis;

    echo $status_rezerwacji, '</br>', $data_poczatkowa, '</br>', $data_koncowa, '</br>', $summary, '</br>', $location;


    break;
}
