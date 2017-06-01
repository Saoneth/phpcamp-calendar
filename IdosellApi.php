<?php

class IdosellApi
{
    private $clientId;
    private $request;
    private $status;

    // IdosellApi('3216');
    function __construct($clientId, $login, $password)
    {
        $this->clientId = $clientId;
        $this->login = $login;
        $this->password = sha1($password);
    }

    private function authenticate()
    {
        $authenticate = array();
        $authenticate['systemKey'] = sha1(date("Ymd") . $this->password);
        $authenticate['systemLogin'] = $this->login;
        $authenticate['lang'] = 'eng';
        return $authenticate;
    }

    /**
     * @return mixed Zwraca odpowiedź z serwera
     */
    // request('reservations', 'get', $request);
    private function request($api, $action, $request)
    {
        $request['authenticate'] = $this->authenticate();
        var_dump($request);
        $request_json = json_encode($request);
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json;charset=UTF-8'
        );

        $curl = curl_init();
        curl_setopt_array($curl, Array(
            CURLOPT_URL => 'https://client' . $this->clientId . '.idosell.com/api/' . $api . '/' . $action . '/0/json',
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $request_json,
            CURLOPT_HTTPHEADER => $headers,
            //CURLINFO_HEADER_OUT => true,
        ));

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 200)
            throw new Exception('Server returned HTTP error: ' . $status);

        curl_close($curl);

        $data = json_decode($response, true);
        if (is_null($data))
            throw new Exception('Server returned invaild json response');

        return $data;
    }

    public function getReservations()
    {
        $request = array(
            'result' => array(
                'page' => 1,
                'number' => 2
            )
        );
        $data = $this->request('reservations', 'get', $request);
        foreach ($data['result']["reservations"] as $rezerwacja) {
            var_dump($rezerwacja["items"]);

            echo "</br></br>";

            $data_poczatkowa = $rezerwacja["reservationDetails"]["dateFrom"];
            $data_koncowa = $rezerwacja["reservationDetails"]["dateTo"];

            $opis_pokoju = array(); // np Pokoj jednosobowy 200

            for ($i = 0; $i < count($rezerwacja["items"]); $i++) {
                $nazwa_pokoju = $rezerwacja["items"][$i]["objectName"];
                $numer_pokoju = $rezerwacja["items"][$i]["itemCode"];
                $cena_za_pokoj = $rezerwacja["items"][$i]["prices"][0]["price"]; //pierwsza cena

                $opis_pokoju[$i] = $nazwa_pokoju . '//' . $numer_pokoju . '//' . $cena_za_pokoj;
            }

            $imie_rezerwujacego = $rezerwacja['client']['firstName'];
            $nazwisko_rezerwujacego = $rezerwacja['client']['lastName'];
            $telefon_rezerwujacego = $rezerwacja['client']['phone'];

            // dane do przekazania
            $status_rezerwacji = $rezerwacja["reservationDetails"]["status"];
            $data_poczatkowa = $rezerwacja["reservationDetails"]["dateFrom"];
            $data_koncowa = $rezerwacja["reservationDetails"]["dateTo"];
            $summary = $imie_rezerwujacego . ' / ' . $nazwisko_rezerwujacego . ' / ' . $telefon_rezerwujacego;

            // zamiana tablicy z opisami pokojow na jednego stringa
            $opis = '';

            foreach ($opis_pokoju as $p_pokoj) {
                $opis .= $p_pokoj;
            }

            $location = $opis;

            echo $status_rezerwacji, '</br>', $data_poczatkowa, '</br>', $data_koncowa, '</br>', $summary, '</br>', $location;


            break;
        }
    }

    public function getClientByEmail($email)
    {
        $request = array(
            'result' => array(
                'page' => 1,
                'number' => 2
            ),
            'paramsSearch' => array(
                'email' => $email
            )
        );
        $data = $this->request('clients', 'get', $request);
        if(count($data['result']['clients'])==0)
            return false;

        return $data['result']['clients'];
    }
    public function addReservation ($reservation)
    {
        
        $request =array ('reservations' => $reservation);
        $data = $this->request('reservations', 'add', $request);

        return $data['result']['clients'];
    }
}


