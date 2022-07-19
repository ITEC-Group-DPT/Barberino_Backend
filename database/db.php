<?php
    $host = "217.21.74.1";
    $user = "u448613383_theshear2";
    $pw = "u4/FYJU|SA>W";
    $db = "u448613383_theshear2";
    $port = "3306";
    $conn = new mysqli($host, $user, $pw, $db, $port);
    $conn_err = $conn->connect_errno;
    date_default_timezone_set('Asia/Saigon');
    $conn -> query("SET time_zone = '+07:00';");


    // if (date_default_timezone_get()) {
    //     echo 'date_default_timezone_set: ' . date_default_timezone_get() . '
    // ';
    // }
    // echo date('d/m/Y H:i:s');
 
    // if ($result = $conn -> query("SELECT current_timestamp()")) {
    //     var_dump(mysqli_fetch_all($result,MYSQLI_ASSOC));
    //   }
?>