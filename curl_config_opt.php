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
function curl_opt($curl, $request_json, $headers){
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

    return $curl;
}