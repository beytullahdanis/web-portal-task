<?php

function getAccessToken() {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.baubuddy.de/index.php/login",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode(["username" => "365", "password" => "1"]),
        CURLOPT_HTTPHEADER => [
            "Authorization: Basic QVBJX0V4cGxvcmVyOjEyMzQ1NmlzQUxhbWVQYXNz",
            "Content-Type: application/json"
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        header('Content-Type: application/json');
        echo json_encode(["error" => "CURL Error: " . $err]);
        return null;
    } else {
        $responseData = json_decode($response, true);
        header('Content-Type: application/json');
        
        if (isset($responseData['oauth']['access_token'])) {
            return $responseData['oauth']['access_token'];
        } else {
            echo json_encode(["error" => "Access token not found"]);
            return null;
        }
    }
}

function fetchData($accessToken) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.baubuddy.de/dev/index.php/v1/tasks/select",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        header('Content-Type: application/json');
        echo json_encode(["error" => "Fetching error!" . $err]);
        return null;
    } else {
        return json_decode($response, true);
    }
}

header('Content-Type: application/json');

$accessToken = getAccessToken();
if ($accessToken) {
    $data = fetchData($accessToken);
    if ($data) {
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Fetching error!"]);
    }
} else {
    exit();
}

?>