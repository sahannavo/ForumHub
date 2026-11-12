<?php
// Database connection (update with your credentials)
$server_name="localhost";
$username="root";
$password="";
$database_name="forum";
$conn = mysqli_connect($server_name, $username, $password, $database_name);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}else{
    // echo "Connected successfully";
}
// $sql= "CREATE DATABASE IF NOT EXISTS forum;";
