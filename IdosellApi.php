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

    public function getReservations($page=1)
    {
        $request = array(
            'result' => array(
                'page' => $page,
                'number' => 20
            )
        );
        $data = $this->request('reservations', 'get', $request);
        $reservations = array();
        foreach ($data['result']["reservations"] as $rezerwacja) {
            $reservation = array();

            $reservation['summary'] = $rezerwacja['client']['firstName'].' '.$rezerwacja['client']['lastName'].' '.$rezerwacja['client']['phone'];
            $tmp = array();
            foreach ($rezerwacja["items"] as $item) {
                $nazwa_pokoju = $item["objectName"];
                $numer_pokoju = $item["itemCode"];
                $cena_za_pokoj = $item["prices"][0]["price"]; //pierwsza cena

                $tmp[] = $nazwa_pokoju . ' / ' . $numer_pokoju . ' / ' . $cena_za_pokoj;
            }
            $reservation['localization'] = implode(';',$tmp);
            $reservation['colorId'] = 1;

            $reservation['start']['dateTime'] = $rezerwacja["reservationDetails"]["dateFrom"];
            $reservation['start']['timeZone'] = 'Poland/Warsaw';
            $reservation['end']['dateTime'] = $rezerwacja["reservationDetails"]["dateTo"];
            $reservation['end']['timeZone'] = 'Poland/Warsaw';

            $reservation['attendees'] = array();
            $reservation['attendees'][] = array(
                'email' => $rezerwacja['client']['email'],
                'displayName'=>$rezerwacja['client']['firstName'].' '.$rezerwacja['client']['lastName'],
                'comment'=>$rezerwacja["reservationDetails"]["clientNote"]
            );

            $reservations[] = $reservation;
        }
        return $reservations;
    }

    public function getClientByEmail($email)
    {
        $request = array(
            'result' => array(
                'page' => 1,
                'number' => 1
            ),
            'paramsSearch' => array(
                'email' => $email
            )
        );
        $data = $this->request('clients', 'get', $request);
        if(count($data['result']['clients']) == 0)
            return false;

        return $data['result']['clients'][0];
    }

    public function addReservation($reservation)
    {
        $request = Array('reservations' => Array($reservation));
        $data = $this->request('reservations', 'add', $request);
        if(count($data['result']['reservations']) == 0)
            return false;

        return $data['result']['reservations'][0];
    }

    public function addClient($client)
    {
        $request = array(
            'clients' => array(
                $client
            )
        );
        $data = $this->request('clients', 'add', $request);
        if(count($data['result']['clients']) == 0)
            return false;

        return $data['result']['clients'];
    }
}


