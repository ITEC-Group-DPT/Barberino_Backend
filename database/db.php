<?php
    $host = "localhost";
    $user = "root";
    $pw = "";
    $db = "barbershop";
    $port = "3306";
    $conn = new mysqli($host, $user, $pw, $db, $port);
    $conn->query("SET time_zone = '+07:00';") 
?>