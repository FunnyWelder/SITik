<?php

namespace App\Client\Controller;

class ApiController
{
    public function getData(string $url, ?string $token) {
        $headers = [
            'Content-Type:application/json',
            'Authorization: Bearer ' . $token,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return $response;
    }

    public function postData(array $body, string $url, ?string $token = null) {
        $headers = [
            'Content-Type:application/json',
        ];
        if (!is_null ($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE));
        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return $response;
    }

    public function postFile(array $body, string $url, ?string $token = null) {
        $headers = [
            'Authorization: Bearer ' . $token,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return $response;
    }

    public function putData(array $body, string $url, ?string $token) {
        $headers = [
            'Content-Type:application/json',
            'Authorization: Bearer ' . $token,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE));
        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return $response;
    }

    public function deleteData(string $url, ?string $token) {
        $headers = [
            'Content-Type:application/json',
            'Authorization: Bearer ' . $token,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_URL, $url);
        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return $response;
    }
}