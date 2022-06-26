<?php
    include  __DIR__ . '/../database/db.php';
    if (isset($_SERVER["HTTP_ORIGIN"])) {
        $allowedOrigins = [
            "http://localhost:3000",
            "http://192.168.1.155:3000",
            "http://localhost:8080",
            "http://localhost:3001",
            "https://technow-b54b6.web.app",
        ];

        if (in_array($_SERVER["HTTP_ORIGIN"], $allowedOrigins)) {
            header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
        }
        header("Access-Control-Allow-Methods: POST, GET, DELETE");
        header("Access-Control-Allow-Headers: origin, content-type, accept, Userid");
    }

    function errorAPI(){
        http_response_code(600);
        exit;
    }

    function failApi($text){
        $response = [];
        $response['success'] = false;
        if (isset($text))  $response['data'] = $text;
        echo json_encode($response);
        exit;
    }

    function successApi($data,$message = ""){
        $response = [];
        $response['success'] = true;
        if(!empty($message)) $data['message'] = $message;

        if (isset($data)) $response['data'] = $data;
        echo json_encode($response);
        exit;
    }
?>