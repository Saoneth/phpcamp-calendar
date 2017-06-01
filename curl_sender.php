<?php
/**
 * Created by PhpStorm.
 * User: Paweł
 * Date: 2017-06-01
 * Time: 13:45
 */

/**
 * @param $curl
 * @param $request_json
 * @param $headers
 * @return mixed
 */
class CURLSender{

    private $address;
    private $request;
    private $status;

    function __constructor($address, $request){
        $this->address = $address;
        $this->request = $request;
    }

    /**
     * @return mixed Zwraca odpowiedź z serwera
     */
    public function send_request(){
        $request_json = json_encode($this->request);
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json;charset=UTF-8'
        );

        $curl = curl_init($this->address);
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
        $this->status = curl_getinfo($curl);
        curl_close($curl);

        return $response;
    }

    /**
     * @return mixed Zwraca status połączenia
     */
    public function status(){
        if($this->status != null){
            return $this->status;
        }
        return null;
    }

}