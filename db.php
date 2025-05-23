<?php

$host = 'localhost';
$username = 'root';
$password = 'My_Chaeyoung01!';
$dbname = 'say ipangan sa database???';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>