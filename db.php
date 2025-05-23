<?php

$host = 'localhost'; 
$username = 'root'; 
$password = 'My_Chaeyoung01!'; 
$database = 'Final_Project'; 


$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>